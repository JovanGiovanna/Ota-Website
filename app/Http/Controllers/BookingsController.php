<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\{Booking, Package, Product, Addon};
use App\Models\{BookPackage, BookPackageAddon, BookProduct, BookProductAddon, BookAddon};
use App\Notifications\EmailNotification;

class BookingsController extends Controller
{
    /** Simpan booking (Package + Product + Addons) */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_types' => 'required|array|min:1',
            'booking_types.*' => 'in:package,product,addon',
            'id_package' => 'nullable|array',
            'id_package.*' => 'exists:packages,id',
            'product_id' => 'nullable|array',
            'product_id.*' => 'exists:products,id',
            'addon_id' => 'nullable|array',
            'addon_id.*' => 'exists:addons,id',
            'quantity' => 'nullable|array',
            'quantity.*' => 'integer|min:1',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email',
            'booker_telp' => 'required|string|max:20',
            'checkin_appointment_start' => 'required|date',
            'checkout_appointment_end' => 'required|date|after:checkin_appointment_start',
            'duration_days' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'requests' => 'nullable|string',
        ]);

        // Handle user authentication - create new user if not logged in
        $user = Auth::user();
        if (!$user) {
            // Check if user with this email already exists
            $existingUser = \App\Models\User::where('email', $validated['booker_email'])->first();

            if ($existingUser) {
                // If user exists but has no password (guest account), use it
                if (is_null($existingUser->password)) {
                    $user = $existingUser;
                } else {
                    // User exists and has password, redirect to login
                    return redirect()->route('login')->with('error', 'Email sudah terdaftar. Silakan login untuk melanjutkan booking.');
                }
            } else {
                // Create new guest user
                $user = \App\Models\User::create([
                    'name' => $validated['booker_name'],
                    'email' => $validated['booker_email'],
                    'phone' => $validated['booker_telp'],
                    'password' => null, // NULL password for guest accounts
                    'activation_token' => Str::random(60), // Generate activation token
                    'status' => 'active',
                ]);
            }
        }

        if (in_array('package', $validated['booking_types']) && empty($validated['id_package'])) {
            throw ValidationException::withMessages(['id_package' => 'Please select at least one package.']);
        }
        if (in_array('product', $validated['booking_types']) && empty($validated['product_id'])) {
            throw ValidationException::withMessages(['product_id' => 'Please select at least one product.']);
        }
        if (in_array('addon', $validated['booking_types']) && empty($validated['addon_id'])) {
            throw ValidationException::withMessages(['addon_id' => 'Please select at least one addon.']);
        }

        DB::beginTransaction();

        try {
            $bookingCode = 'BK-' . now()->format('YmdHis') . '-' . rand(100,999);

            // 1️⃣ Buat master booking
            $booking = Booking::create([
                'id_user' => $user->id,
                'booker_name' => $validated['booker_name'],
                'booker_email' => $validated['booker_email'],
                'booker_telp' => $validated['booker_telp'],
                'booking_code' => $bookingCode,
                'checkin_appointment_start' => $validated['checkin_appointment_start'],
                'checkout_appointment_end' => $validated['checkout_appointment_end'],
                'duration_days' => $validated['duration_days'],
                'amount' => $validated['amount'],
                'total_price' => 0, // akan dihitung
                'status' => 'pending', // start with pending, will update to 'book' after creating related models
                'note' => $validated['requests'] ?? null,
                'payment_expires_at' => now()->addMinutes(30), // Payment expires in 30 minutes
            ]);

            $totalPrice = 0;
            $usedAddonIds = []; // track addon yang sudah dipakai

            // 2️⃣ Booking Package
            if (!empty($validated['id_package'])) {
                foreach ($validated['id_package'] as $index => $packageId) {
                    $package = Package::find($packageId);
                    if (!$package) continue;

                    // Get quantity for this package (default to 1 if not specified)
                    $packageQty = $validated['quantity'][$index] ?? 1;
                    
                    $packagePrice = $package->nta * $packageQty * $validated['duration_days'];
                    $totalPrice += $packagePrice;

                    // Simpan package
                    $bookPackage = BookPackage::create([
                        'id' => (string) Str::uuid(), // UUID
                        'id_book' => $booking->id,
                        'id_user' => $user->id,
                        'id_package' => $packageId,
                        'booker_name' => $validated['booker_name'],
                        'booker_email' => $validated['booker_email'],
                        'booker_telp' => $validated['booker_telp'],
                        'booking_code' => $bookingCode,
                        'checkin_appointment_start' => $validated['checkin_appointment_start'],
                        'checkout_appointment_end' => $validated['checkout_appointment_end'],
                        'quantity' => $packageQty,
                        'total_price' => $packagePrice,
                        'status' => 'pending',
                        'notes' => $validated['requests'] ?? null,
                    ]);

                    // Auto-create BookProduct for each product in package (for transaction reports)
                    $productsData = is_array($package->products_data) ? $package->products_data : [];
                    foreach ($productsData as $productData) {
                        $productId = $productData['id'] ?? null;
                        if (!$productId) continue;

                        $product = Product::find($productId);
                        if (!$product) continue;

                        $paxInPackage = $productData['pax'] ?? 1;
                        $productSubTotal = $productData['sub_total'] ?? ($product->finalPrice * $paxInPackage);

                        BookProduct::create([
                            'id_book' => $booking->id,
                            'id_product' => $productId,
                            'amount' => $paxInPackage * $packageQty,
                            'total_price' => $productSubTotal * $packageQty,
                            'booking_code' => $bookingCode,
                            'status' => 'pending',
                            'notes' => 'From package: ' . $package->name_package,
                        ]);
                    }

                    // Auto-create BookAddon for each addon in package (for transaction reports)
                    $addonsData = is_array($package->addons_data) ? $package->addons_data : [];
                    foreach ($addonsData as $addonData) {
                        $addonId = $addonData['id'] ?? null;
                        if (!$addonId) continue;

                        $addon = Addon::find($addonId);
                        if (!$addon) continue;

                        $paxInPackage = $addonData['pax'] ?? 1;
                        $addonSubTotal = $addonData['sub_total'] ?? ($addon->finalPrice * $paxInPackage);

                        \App\Models\BookAddon::create([
                            'id_user' => $user->id,
                            'id_book' => $booking->id,
                            'id_addon' => $addonId,
                            'checkin_appointment_start' => $validated['checkin_appointment_start'],
                            'checkout_appointment_end' => $validated['checkout_appointment_end'],
                            'amount' => $paxInPackage * $packageQty,
                            'total_price' => $addonSubTotal * $packageQty,
                            'booker_name' => $validated['booker_name'],
                            'booker_email' => $validated['booker_email'],
                            'booker_telp' => $validated['booker_telp'],
                            'booking_code' => $bookingCode,
                            'status' => 'pending',
                            'notes' => 'From package: ' . $package->name_package,
                        ]);
                    }

                    // Simpan addon untuk package ini
                    if (!empty($validated['addon_id'])) {
                        foreach ($validated['addon_id'] as $addonId) {
                            if (in_array($addonId, $usedAddonIds)) continue;

                            $addon = Addon::find($addonId);
                            if (!$addon) continue;

                            BookPackageAddon::create([
                                'id_book' => $booking->id,
                                'id_package' => $packageId,
                                'id_addons' => $addonId,
                                'booking_code' => $bookingCode,
                            ]);

                            $addonQty = $validated['quantity'][$addonId] ?? 1;
                            $totalPrice += $addon->finalPrice * $addonQty;

                            $usedAddonIds[] = $addonId;
                        }
                    }


                }
            }

            // 3️⃣ Booking Product
if (!empty($validated['product_id'])) {
    foreach ($validated['product_id'] as $productId) {
        $product = Product::find($productId);
        if (!$product) continue;

        $quantity = $validated['quantity'][$productId] ?? 1;
                $productPrice = $product->finalPrice * $quantity;
                $totalPrice += $productPrice;

        $bookProduct = BookProduct::create([
            'id_book' => $booking->id,
            'id_user' => $user->id,
            'id_product' => $productId,
            'checkin_appointment_start_datetime' => $validated['checkin_appointment_start'],
            'checkout_appointment_end_datetime' => $validated['checkout_appointment_end'],
            'amount' => $quantity,
            'booker_name' => $validated['booker_name'],
            'booker_email' => $validated['booker_email'],
            'booker_telp' => $validated['booker_telp'],
            'booking_code' => $bookingCode,
            'total_price' => $productPrice,
            'notes' => $validated['requests'] ?? null,
        ]);

                // Tambahkan addon untuk product
                if (!empty($validated['addon_id'])) {
                    foreach ($validated['addon_id'] as $addonId) {
                        if (in_array($addonId, $usedAddonIds)) continue;

                        $addon = Addon::find($addonId);
                        if (!$addon) continue;

                        $addonQty = $validated['quantity'][$addonId] ?? 1;

                        BookProductAddon::create([
                            'id_book' => $bookProduct->id,
                            'id_product' => $productId,
                            'id_addons' => $addonId,
                            'quantity' => $addonQty,
                            'price' => $addon->finalPrice,
                        ]);

                        $totalPrice += $addon->finalPrice * $addonQty;
                        $usedAddonIds[] = $addonId;
                    }
                }

    }
                }

            // 4️⃣ Addon berdiri sendiri
if (!empty($validated['addon_id'])) {
    foreach ($validated['addon_id'] as $addonId) {
        if (in_array($addonId, $usedAddonIds)) continue;

        $addon = Addon::find($addonId);
        if (!$addon) continue;

        $qty = $validated['quantity'][$addonId] ?? 1;
        $addonPrice = $addon->finalPrice * $qty;
        $totalPrice += $addonPrice;

            BookAddon::create([
            'id_book' => $booking->id,
            'id_user' => $user->id,
            'id_addon' => $addonId,
            'checkin_appointment_start' => $validated['checkin_appointment_start'],
            'checkout_appointment_end' => $validated['checkout_appointment_end'],
            'amount' => $qty,
            'total_price' => $addonPrice,
            'booker_name' => $validated['booker_name'],
            'booker_email' => $validated['booker_email'],
            'booker_telp' => $validated['booker_telp'],
            'booking_code' => $bookingCode . '-' . Str::random(4),
            'status' => 'pending',
            'notes' => $validated['requests'] ?? null,
        ]);
        
    }
    
}

            

            // 5️⃣ Update total_price master booking
            $booking->update(['total_price' => $totalPrice]);

            // 6️⃣ Kirim notifikasi email - DISABLED for now
            // try {
            //     if ($user) {
            //         $user->notify(new EmailNotification($booking));
            //     }
            // } catch (\Exception $e) {
            //     \Log::warning('Email notification failed: ' . $e->getMessage());
            // }

            DB::commit();

            // 7️⃣ Set status to 'book' to decrease stock immediately
            $booking->update(['status' => 'book']);
            
            // Also update all related bookings to 'book' status
            BookPackage::where('id_book', $booking->id)->update(['status' => 'book']);
            BookProduct::where('id_book', $booking->id)->update(['status' => 'book']);
            \App\Models\BookAddon::where('id_book', $booking->id)->update(['status' => 'book']);

            return redirect()->route('user.payment', $booking->id)
                ->with('success', 'Booking berhasil dibuat! Silakan lanjutkan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pemesanan Anda.')->withInput();
        }

        
    }

        /**
         * Super-admin / Admin bookings index (manage all booking types)
         */
        public function index(Request $request)
        {
            $query = Booking::with(['user', 'packages', 'products', 'addons']);

            // Filters
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('type')) {
                $type = $request->input('type');
                if ($type === 'package') {
                    $query->whereHas('packages');
                } elseif ($type === 'product') {
                    $query->whereHas('products');
                } elseif ($type === 'addon') {
                    $query->whereHas('addons');
                }
            }

            if ($request->filled('search')) {
                $s = $request->input('search');
                $query->where(function ($q) use ($s) {
                    $q->where('booking_code', 'like', "%{$s}%")
                      ->orWhere('booker_name', 'like', "%{$s}%")
                      ->orWhere('booker_email', 'like', "%{$s}%");
                });
            }

            if ($request->filled('date_from')) {
                $from = $request->input('date_from');
                $query->whereDate('created_at', '>=', $from);
            }
            if ($request->filled('date_to')) {
                $to = $request->input('date_to');
                $query->whereDate('created_at', '<=', $to);
            }

            $bookings = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

            return view('super_admin.transaction.index', compact('bookings'));
        }

    
    public function history()
    {
        $bookings = Booking::where('id_user', Auth::id())
            ->with([
                    'packages.package',
                    'packages.bookPackageAddons.addon',
                    'products.product',
                    'products.bookProductAddons.addon',
                    'addons.addon'
                 ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.history', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        // Allow access if user is authenticated and owns the booking, or if it's a guest booking (no auth required)
        // Also allow if logged in user has the same email as the booker (handles guest-to-logged-in transition)
        if (Auth::check() && $booking->id_user !== Auth::id() && $booking->booker_email !== Auth::user()->email) {
            abort(403);
        }

        $booking->load(['packages.package', 'packages.bookPackageAddons.addon', 'products.product', 'products.bookProductAddons.addon', 'addons.addon', 'reviews']);

        // Ensure total_price is not null to avoid view errors
        if (is_null($booking->total_price)) {
            $booking->total_price = 0;
        }

        return view('user.detail_history', compact('booking'));
    }

    public function showDetail(Booking $booking)
    {
        return $this->show($booking);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,checked_in,checked_out,maintenance,completed,cancelled',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $validated['status']]);

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }

    public function approve(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking tidak dalam status pending.');
        }

        $booking->status = 'confirmed';
        $booking->save();

        return back()->with('success', 'Booking #' . $booking->id . ' berhasil dikonfirmasi.');
    }

    public function reject(Booking $booking)
    {
        if (!in_array($booking->status, ['pending', 'book'])) {
            return back()->with('error', 'Booking tidak dalam status yang dapat ditolak.');
        }

        $booking->status = 'cancelled';
        $booking->save();

        return back()->with('success', 'Booking #' . $booking->id . ' berhasil ditolak (Cancelled).');
    }

    public function showDetailAdmin(Booking $booking)
    {
        $booking->load(['user', 'packages', 'products', 'addons']);
        return view('super_admin.transaction.detail', compact('booking'));
    }

    public function support(Booking $booking)
    {
        if ($booking->id_user !== Auth::id()) {
            abort(403);
        }

        $booking->load(['packages', 'products', 'addons']);
        return view('user.support', compact('booking'));
    }

    public function submitSupport(Request $request, Booking $booking)
    {
        if ($booking->id_user !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'priority' => 'required|in:low,normal,high,urgent',
            'message' => 'required|string|min:10',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
        ]);

        return redirect()->route('user.detail_history', $booking->id)
            ->with('success', 'Your support request has been submitted successfully.');
    }

    public function cancel(Booking $booking)
    {
        if ($booking->id_user !== Auth::id()) {
            abort(403);
        }

        // Allow cancel from book, confirmed, or paid (user paid then decides to cancel)
        if (!in_array($booking->status, ['book', 'confirmed', 'paid'])) {
            return back()->with('error', 'Booking tidak dapat dibatalkan pada status ini.');
        }

        $booking->update(['status' => 'cancelled']);
        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    /**
     * User requests a refund (after cancelling or when paid)
     */
    public function requestRefund(Request $request, Booking $booking)
    {
        // Allow only owner / matching email
        if (Auth::check() && $booking->id_user !== Auth::id() && $booking->booker_email !== Auth::user()->email) {
            abort(403);
        }

        // Allow refund request when booking was paid or already cancelled
        if (!in_array($booking->status, ['paid', 'cancelled'])) {
            return back()->with('error', 'Refund hanya dapat diminta untuk booking yang telah dibayar atau telah dibatalkan.');
        }

        // Check refund policy for all items in the booking
        $refundAllowed = true;

        // Check packages
        if ($booking->packages->count() > 0) {
            foreach ($booking->packages as $bookPackage) {
                if ($bookPackage->package->refund_policy == 'tidak mendukung') {
                    $refundAllowed = false;
                    break;
                }
                // Check package addons
                if ($bookPackage->bookPackageAddons->count() > 0) {
                    foreach ($bookPackage->bookPackageAddons as $packageAddon) {
                        if ($packageAddon->addon->refund_policy == 'tidak mendukung') {
                            $refundAllowed = false;
                            break 2;
                        }
                    }
                }
            }
        }

        // Check products
        if ($refundAllowed && $booking->products->count() > 0) {
            foreach ($booking->products as $bookProduct) {
                if ($bookProduct->product->refund_policy == 'tidak mendukung') {
                    $refundAllowed = false;
                    break;
                }
                // Check product addons
                if ($bookProduct->bookProductAddons->count() > 0) {
                    foreach ($bookProduct->bookProductAddons as $productAddon) {
                        if ($productAddon->addon->refund_policy == 'tidak mendukung') {
                            $refundAllowed = false;
                            break 2;
                        }
                    }
                }
            }
        }

        // Check standalone addons
        if ($refundAllowed && $booking->addons->count() > 0) {
            foreach ($booking->addons as $bookAddon) {
                if ($bookAddon->addon->refund_policy == 'tidak mendukung') {
                    $refundAllowed = false;
                    break;
                }
            }
        }

        if (!$refundAllowed) {
            return back()->with('error', 'Refund tidak tersedia untuk booking ini karena salah satu item tidak mendukung kebijakan refund.');
        }

        $booking->update(['status' => 'payment_return']);

        return back()->with('success', 'Permintaan pengembalian dana berhasil dikirim. Tim admin akan memprosesnya.');
    }

    /**
     * Admin verifies payment and marks booking completed
     */
    public function verifyPayment(Booking $booking)
    {
        if ($booking->status !== 'paid') {
            return back()->with('error', 'Booking tidak dalam status paid.');
        }

        $booking->update(['status' => 'completed']);

        return back()->with('success', 'Pembayaran diverifikasi. Booking ditandai sebagai completed.');
    }

    /**
     * Admin processes refund (payment return) and marks as completed
     */
    public function processRefund(Booking $booking)
    {
        if ($booking->status !== 'payment_return') {
            return back()->with('error', 'Booking tidak dalam status permintaan pengembalian dana.');
        }

        // Here you would integrate with payment gateway / refund logic.
        // For now, we mark booking as completed after refund processed.
        $booking->update(['status' => 'completed']);

        return back()->with('success', 'Pengembalian dana diproses dan booking ditandai sebagai completed.');
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return back()->with('success', 'Booking berhasil dihapus.');
    }

    public function payment(Booking $booking)
    {
        // Allow access if user is authenticated and owns the booking, or if it's a guest booking (no auth required)
        // Also allow if logged in user has the same email as the booker (handles guest-to-logged-in transition)
        if (Auth::check() && $booking->id_user !== Auth::id() && $booking->booker_email !== Auth::user()->email) {
            abort(403);
        }

        $booking->load([
            'packages.package',
            'packages.bookPackageAddons.addon',
            'products.product',
            'products.bookProductAddons.addon',
            'addons.addon'
        ]);

        return view('user.payment', compact('booking'));
    }

    public function confirmPayment(Booking $booking)
    {
        // Allow access if user is authenticated and owns the booking, or if it's a guest booking (no auth required)
        // Also allow if logged in user has the same email as the booker (handles guest-to-logged-in transition)
        if (Auth::check() && $booking->id_user !== Auth::id() && $booking->booker_email !== Auth::user()->email) {
            abort(403);
        }

        if (!in_array($booking->status, ['book', 'pending'])) {
            return back()->with('error', 'Booking sudah diproses.');
        }

        $booking->update(['status' => 'paid']);

        return redirect()->route('user.detail_history', $booking->id)
            ->with('success', 'Payment berhasil! Booking Anda telah dibayar.');
    }

    public function activateAccount($token)
    {
        $user = \App\Models\User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token aktivasi tidak valid.');
        }

        if (!is_null($user->password)) {
            return redirect()->route('login')->with('error', 'Akun sudah diaktifkan.');
        }

        return view('user.activate', compact('user', 'token'));
    }

    public function setPassword(Request $request, $token)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::where('activation_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Token aktivasi tidak valid.');
        }

        if (!is_null($user->password)) {
            return redirect()->route('login')->with('error', 'Akun sudah diaktifkan.');
        }

        $user->update([
            'password' => bcrypt($validated['password']),
            'activation_token' => null,
        ]);

        Auth::login($user);

        return redirect()->route('user.history')->with('success', 'Akun berhasil diaktifkan! Selamat datang.');
    }
}
