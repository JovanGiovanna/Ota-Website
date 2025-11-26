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
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

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

        DB::beginTransaction();

        try {
            $bookingCode = 'BK-' . now()->format('YmdHis') . '-' . rand(100,999);

            // 1️⃣ Buat master booking
            $booking = Booking::create([
                'id_user' => Auth::id(),
                'booker_name' => $validated['booker_name'],
                'booker_email' => $validated['booker_email'],
                'booker_telp' => $validated['booker_telp'],
                'booking_code' => $bookingCode,
                'checkin_appointment_start' => $validated['checkin_appointment_start'],
                'checkout_appointment_end' => $validated['checkout_appointment_end'],
                'duration_days' => $validated['duration_days'],
                'amount' => $validated['amount'],
                'total_price' => 0, // akan dihitung
                'status' => 'pending',
                'note' => $validated['requests'] ?? null,
            ]);

            $totalPrice = 0;
            $usedAddonIds = []; // track addon yang sudah dipakai

            // 2️⃣ Booking Package
            if (!empty($validated['id_package'])) {
                foreach ($validated['id_package'] as $packageId) {
                    $package = Package::find($packageId);
                    if (!$package) continue;

                    $packagePrice = $package->nta * $validated['duration_days'];
                    $totalPrice += $packagePrice;

                    // Simpan package
                    $bookPackage = BookPackage::create([
                        'id' => (string) Str::uuid(), // UUID
                        'id_book' => $booking->id,
                        'id_user' => Auth::id(),
                        'id_package' => $packageId,
                        'booker_name' => $validated['booker_name'],
                        'booker_email' => $validated['booker_email'],
                        'booker_telp' => $validated['booker_telp'],
                        'booking_code' => $bookingCode,
                        'checkin_appointment_start' => $validated['checkin_appointment_start'],
                        'checkout_appointment_end' => $validated['checkout_appointment_end'],
                        'total_price' => $packagePrice,
                        'status' => 'pending',
                        'notes' => $validated['requests'] ?? null,
                    ]);

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
            'id_user' => Auth::id(),
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
            'id_user' => Auth::id(),
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

            // 6️⃣ Kirim notifikasi email
            $user = Auth::user();
            if ($user) {
                $user->notify(new EmailNotification($booking));
            }

            DB::commit();

            return redirect()->route('user.history')
                ->with('success', 'Booking berhasil dibuat! Total: Rp ' . number_format($totalPrice,0,',','.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pemesanan Anda.')->withInput();
        }

        
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
            ->latest()
            ->paginate(10);

        return view('user.history', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        if ($booking->id_user !== Auth::id()) {
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
