<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\{Booking, Package, Product, Addon, Detail_Booking};

class BookingsController extends Controller
{
    /**
     * Superadmin: Menampilkan semua booking dengan relasi.
     */
    public function index()
    {
        $bookings = Booking::with(['user', 'packages', 'products', 'addons'])
            ->latest()
            ->paginate(10);

        return view('super_admin.transaction_packages', compact('bookings'));
    }

    /**
     * Form booking (user).
     */
    public function create()
    {
        $packages = Package::where('status', 'available')->where('publish', true)->get();
        $products = Product::where('status', 'available')->get();
        $addons   = Addon::where('status', 'available')->where('publish', true)->get();

        return view('user.form_booker', compact('packages', 'products', 'addons'));
    }

    /**
     * Simpan booking (Package + Product + Addons) - Flexible combination.
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        try {
            $validated = $request->validate([
                'booking_types' => 'required|array|min:1',
                'booking_types.*' => 'in:package,product,addon',

                // Package - optional if selected
                'id_package' => 'nullable|array',
                'id_package.*' => 'exists:packages,id',

                // Products - optional if selected
                'product_id' => 'nullable|array',
                'product_id.*' => 'exists:products,id',

                // Addons - optional if selected
                'addon_id' => 'nullable|array',
                'addon_id.*' => 'exists:addons,id',
                'quantity' => 'nullable|array',
                'quantity.*' => 'integer|min:1',

                // Common fields
                'booker_name' => 'required|string|max:255',
                'booker_email' => 'required|email',
                'booker_telp' => 'required|string|max:20',
                'checkin_appointment_start' => 'required|date',
                'checkout_appointment_end' => 'required|date|after:checkin_appointment_start',
                'duration_days' => 'required|integer|min:1',
                'amount' => 'required|numeric|min:0',
                'requests' => 'nullable|string',
            ]);

            // Validate that selected types have corresponding data
            if (in_array('package', $validated['booking_types']) && empty($validated['id_package'])) {
                throw ValidationException::withMessages(['id_package' => 'Please select at least one package.']);
            }
            if (in_array('product', $validated['booking_types']) && empty($validated['product_id'])) {
                throw ValidationException::withMessages(['product_id' => 'Please select at least one product.']);
            }
            if (in_array('addon', $validated['booking_types']) && empty($validated['addon_id'])) {
                throw ValidationException::withMessages(['addon_id' => 'Please select at least one addon.']);
            }

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        // Calculate total price
        $totalPrice = 0;

        // Calculate package price
        if (!empty($validated['id_package'])) {
            foreach ($validated['id_package'] as $packageId) {
                $package = Package::find($packageId);
                $totalPrice += $package->price_publish * $validated['duration_days'];
            }
        }

        // Calculate product price
        if (!empty($validated['product_id'])) {
            foreach ($validated['product_id'] as $productId) {
                $product = Product::find($productId);
                $totalPrice += $product->price;
            }
        }

        // Calculate addon price
        $addonQuantities = [];
        if (!empty($validated['addon_id'])) {
            foreach ($validated['addon_id'] as $addonId) {
                $addon = Addon::find($addonId);
                $quantity = $validated['quantity'][$addonId] ?? 1;
                $addonQuantities[$addonId] = $quantity;
                $totalPrice += $addon->price * $quantity;
            }
        }

        // Buat Booking utama
        $booking = Booking::create([
            'id' => Str::uuid(), // Generate UUID for id
            'id_user' => Auth::id(),
            'booker_name' => $validated['booker_name'],
            'booker_email' => $validated['booker_email'],
            'booker_telp' => $validated['booker_telp'],
            'checkin_appointment_start' => $validated['checkin_appointment_start'],
            'checkout_appointment_end' => $validated['checkout_appointment_end'],
            'duration_days' => $validated['duration_days'],
            'amount' => $validated['amount'],
            'total_price' => $totalPrice,
            'status' => 'pending',
            'note' => $validated['requests'] ?? null,
        ]);

        // Simpan packages (many-to-many through BookPackageAddon)
        if (!empty($validated['id_package'])) {
            foreach ($validated['id_package'] as $packageId) {
                \App\Models\BookPackageAddon::create([
                    'id_book' => $booking->id,
                    'id_package' => $packageId,
                    'id_addons' => null, // For packages only
                ]);
            }
        }

        // Simpan products (many-to-many through BookProduct)
        if (!empty($validated['product_id'])) {
            foreach ($validated['product_id'] as $productId) {
                \App\Models\BookProduct::create([
                    'id_book' => $booking->id,
                    'id_product' => $productId,
                    'amount' => 1, // Default amount for products
                    'total_price' => Product::find($productId)->price,
                ]);
            }
        }

        // Simpan addons (many-to-many through BookPackageAddon)
        if (!empty($validated['addon_id'])) {
            foreach ($validated['addon_id'] as $addonId) {
                \App\Models\BookPackageAddon::create([
                    'id_book' => $booking->id,
                    'id_package' => null, // For addons only
                    'id_addons' => $addonId,
                ]);
            }
        }

        $selectedTypes = implode(', ', $validated['booking_types']);
        $msg = 'Booking berhasil dibuat! Tipe: ' . $selectedTypes . '. Durasi: ' . $validated['duration_days'] . ' hari. Total Rp ' .
               number_format($totalPrice, 0, ',', '.');

        return redirect()->route('user.history')->with('success', $msg);
    }

    /**
     * Riwayat booking user.
     */
    public function history()
    {
        $bookings = Booking::where('id_user', Auth::id())
            ->with(['packages', 'products', 'addons'])
            ->latest()
            ->paginate(10);

        return view('user.history', compact('bookings'));
    }

    /**
     * Detail booking.
     */
    public function show(Booking $booking)
    {
        // Ensure the booking belongs to the authenticated user
        if ($booking->id_user !== Auth::id()) {
            abort(403);
        }

        $booking->load(['packages', 'products', 'addons']);

        return view('user.detail_history', compact('booking'));
    }

    /**
     * Detail booking (alias for show method).
     */
    public function showDetail(Booking $booking)
    {
        return $this->show($booking);
    }

    /**
     * Admin - ubah status booking.
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,confirmed,checked_in,completed,cancelled',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $validated['status']]);

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }

    /**
     * Cancel booking.
     */
    public function cancel(Booking $booking)
    {
        // Ensure the booking belongs to the authenticated user
        if ($booking->id_user !== Auth::id()) {
            abort(403);
        }

        // Only allow cancellation for pending or confirmed bookings
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Booking tidak dapat dibatalkan pada status ini.');
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }

    /**
     * Hapus booking (opsional).
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();

        return back()->with('success', 'Booking berhasil dihapus.');
    }
}
