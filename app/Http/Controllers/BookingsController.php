<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Models\{Booking, Package, Product, Addon, Detail_Booking};
use App\Notifications\EmailNotification;

class BookingsController extends Controller
{
    /** Superadmin: Menampilkan semua booking dengan relasi. */
    public function index()
    {
        $bookings = Booking::with(['user', 'packages', 'products', 'addons'])
            ->latest()
            ->paginate(10);

        return view('admin.transaction.packages', compact('bookings'));
    }

    /** Admin: Menampilkan hanya booking yang mengandung Package. */
    public function indexPackagesOnly()
    {
        $bookings = Booking::whereHas('packages')
            ->with(['user', 'packages', 'products', 'addons'])
            ->latest()
            ->paginate(10);

        return view('admin.transaction.packages', compact('bookings'));
    }

    public function indexProductsOnly()
    {
        $bookings = Booking::whereHas('products')
            ->whereDoesntHave('packages')
            ->whereDoesntHave('addons')
            ->with(['user', 'products'])
            ->latest()
            ->paginate(10);

        return view('admin.transaction.product', compact('bookings'));
    }

    public function indexAddonsOnly()
    {
        $bookings = Booking::whereHas('addons')
            ->whereDoesntHave('packages')
            ->whereDoesntHave('products')
            ->with(['user', 'addons'])
            ->latest()
            ->paginate(10);

        return view('admin.transaction.addons', compact('bookings'));
    }

    /** Admin: Menampilkan booking packages pending untuk approval. */
    public function indexPackageApproval()
    {
        $bookings = Booking::whereHas('packages')
            ->where('status', 'pending')
            ->with(['user', 'packages', 'products', 'addons'])
            ->latest()
            ->paginate(10);

        return view('super_admin.packages.approval', compact('bookings'));
    }

    /** Form booking (user). */
    public function create()
    {
        $packages = Package::where('status', 'available')->where('publish', true)->get();
        $products = Product::where('status', 'available')->get();
        $addons   = Addon::where('status', 'available')->where('publish', true)->get();

        return view('user.form_booker', compact('packages', 'products', 'addons'));
    }

    /** Simpan booking (Package + Product + Addons). */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        try {
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

            if (in_array('package', $validated['booking_types']) && empty($validated['id_package'])) {
                throw ValidationException::withMessages(['id_package' => 'Please select at least one package.']);
            }
            if (in_array('product', $validated['booking_types']) && empty($validated['product_id'])) {
                throw ValidationException::withMessages(['product_id' => 'Please select at least one product.']);
            }
            if (in_array('addon', $validated['booking_types']) && empty($validated['addon_id'])) {
                throw ValidationException::withMessages(['addon_id' => 'Please select at least one addon.']);
            }

            $totalPrice = 0;
            $addonQuantities = [];

            if (!empty($validated['id_package'])) {
                foreach ($validated['id_package'] as $packageId) {
                    $package = Package::find($packageId);
                    if ($package) {
                        $totalPrice += $package->price_publish * $validated['duration_days'];
                    }
                }
            }

            if (!empty($validated['product_id'])) {
                foreach ($validated['product_id'] as $productId) {
                    $product = Product::find($productId);
                    if ($product) {
                        $totalPrice += $product->price;
                    }
                }
            }

            if (!empty($validated['addon_id'])) {
                foreach ($validated['addon_id'] as $addonId) {
                    $addon = Addon::find($addonId);
                    $quantity = $validated['quantity'][$addonId] ?? 1;
                    if ($addon) {
                        $addonQuantities[$addonId] = $quantity;
                        $totalPrice += $addon->price * $quantity;
                    }
                }
            }

            DB::beginTransaction();

            $lastBooking = Booking::orderBy('created_at', 'desc')->first();
            $nextNumber = 1;
            if ($lastBooking && $lastBooking->booking_code) {
                $lastCodePart = substr($lastBooking->booking_code, 3);
                $lastNumber = is_numeric($lastCodePart) ? (int)$lastCodePart : 0;
                $nextNumber = $lastNumber + 1;
            }
            $bookingCode = 'BK-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            $booking = Booking::create([
                'id' => Str::uuid(),
                'id_user' => Auth::id(),
                'booker_name' => $validated['booker_name'],
                'booker_email' => $validated['booker_email'],
                'booker_telp' => $validated['booker_telp'],
                'booking_code' => $bookingCode,
                'checkin_appointment_start' => $validated['checkin_appointment_start'],
                'checkout_appointment_end' => $validated['checkout_appointment_end'],
                'duration_days' => $validated['duration_days'],
                'amount' => $validated['amount'],
                'total_price' => $totalPrice,
                'status' => 'pending',
                'note' => $validated['requests'] ?? null,
            ]);

            if (!empty($validated['id_package'])) {
                foreach ($validated['id_package'] as $packageId) {
                    \App\Models\BookPackageAddon::create([
                        'id_book' => $booking->id,
                        'id_package' => $packageId,
                        'id_addons' => null,
                    ]);
                }
            }

            if (!empty($validated['product_id'])) {
                foreach ($validated['product_id'] as $productId) {
                    \App\Models\BookProduct::create([
                        'id_book' => $booking->id,
                        'id_product' => $productId,
                        'amount' => 1,
                        'total_price' => Product::find($productId)->price ?? 0,
                    ]);
                }
            }

            if (!empty($validated['addon_id'])) {
                foreach ($validated['addon_id'] as $addonId) {
                    $quantity = $addonQuantities[$addonId] ?? 1;
                    $addon = Addon::find($addonId);

                    \App\Models\BookPackageAddon::create([
                        'id_book' => $booking->id,
                        'id_package' => null,
                        'id_addons' => $addonId,
                        'quantity' => $quantity,
                    ]);

                    \App\Models\BookAddon::create([
                        'id_user' => Auth::id(),
                        'id_addon' => $addonId,
                        'checkin_appointment_start' => $validated['checkin_appointment_start'],
                        'checkout_appointment_end' => $validated['checkout_appointment_end'],
                        'amount' => $quantity,
                        'total_price' => $addon->price * $quantity,
                        'booker_name' => $validated['booker_name'],
                        'booker_email' => $validated['booker_email'],
                        'booker_telp' => $validated['booker_telp'],
                        'booking_code' => $bookingCode,
                        'status' => 'pending',
                        'notes' => $validated['requests'] ?? null,
                    ]);
                }
            }

            $user = Auth::user();
            if ($user) {
                $user->notify(new EmailNotification($booking));
            }

            DB::commit();

            $selectedTypes = implode(', ', $validated['booking_types']);
            $msg = 'Pemesanan berhasil dibuat! Email konfirmasi telah dikirim ke ' . $user->email .
                '. Tipe: ' . $selectedTypes . '. Total Rp ' . number_format($totalPrice, 0, ',', '.');

            return redirect()->route('user.history')->with('success', $msg);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            \Log::error('Booking failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pemesanan Anda.')->withInput();
        }
    }

    public function history()
    {
        $bookings = Booking::where('id_user', Auth::id())
            ->with(['packages', 'products', 'addons'])
            ->latest()
            ->paginate(10);

        return view('user.history', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        if ($booking->id_user !== Auth::id()) {
            abort(403);
        }

        $booking->load(['packages', 'products', 'addons', 'reviews']);

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
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking tidak dalam status pending.');
        }

        $booking->status = 'cancelled';
        $booking->save();

        return back()->with('success', 'Booking #' . $booking->id . ' berhasil ditolak (Cancelled).');
    }

    public function showDetailAdmin(Booking $booking)
    {
        $booking->load(['user', 'packages', 'products', 'addons']);
        return view('admin.transaction.detail', compact('booking'));
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

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Booking tidak dapat dibatalkan pada status ini.');
        }

        $booking->update(['status' => 'cancelled']);
        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return back()->with('success', 'Booking berhasil dihapus.');
    }
}
