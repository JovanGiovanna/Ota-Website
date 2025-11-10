<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
// Pastikan Anda mengimpor semua Model yang diperlukan (termasuk BookPackageAddon dan BookProduct jika diperlukan)
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

        return view('admin.transaction.packages', compact('bookings'));
    }

    // --- FUNGSI BARU UNTUK INDEX PAKET SAJA ---

    /**
     * Admin: Menampilkan hanya booking yang mengandung Package (terlepas dari status).
     * Digunakan untuk rute packages/IndexPackages.
     */
    public function indexPackagesOnly()
    {
        // Ambil booking yang memiliki relasi dengan packages
        $bookings = Booking::whereHas('packages')
            ->with(['user', 'packages', 'products', 'addons'])
            ->latest()
            ->paginate(10);

        // Anda mungkin perlu membuat view baru (misalnya super_admin.packages.index)
        return view('admin.transaction.packages', compact('bookings')); 
    }

    public function indexProductsOnly()
    {
        // Ambil booking yang memiliki relasi dengan products
        // DAN TIDAK memiliki relasi dengan packages atau addons (BookPackageAddon)
        $bookings = Booking::whereHas('products')
            ->whereDoesntHave('packages')
            ->whereDoesntHave('addons') // Asumsi 'addons' merujuk ke relasi BookPackageAddon dengan id_addons IS NOT NULL
            ->with(['user', 'products']) // Hanya muat relasi yang relevan untuk produk
            ->latest()
            ->paginate(10);

        // Catatan: Anda perlu membuat view baru di sini
        return view('admin.transaction.product', compact('bookings'));
    }

    public function indexAddonsOnly()
    {
        // Ambil booking yang memiliki relasi dengan addons (melalui BookPackageAddon dengan id_addons IS NOT NULL)
        // DAN TIDAK memiliki relasi dengan packages atau products
        $bookings = Booking::whereHas('addons')
            ->whereDoesntHave('packages')
            ->whereDoesntHave('products')
            ->with(['user', 'addons']) // Hanya muat relasi yang relevan untuk addons
            ->latest()
            ->paginate(10);

        // Catatan: Anda perlu membuat view baru di sini
        return view('admin.transaction.addons', compact('bookings'));
    }

    /**
     * Admin: Menampilkan booking packages yang statusnya PENDING untuk approval.
     * Digunakan untuk rute packages/approval.
     */
    public function indexPackageApproval()
    {
        // Ambil booking yang memiliki relasi dengan packages DAN statusnya 'pending'
        $bookings = Booking::whereHas('packages')
            ->where('status', 'pending')
            ->with(['user', 'packages', 'products', 'addons'])
            ->latest()
            ->paginate(10);

        // Anda mungkin perlu membuat view baru (misalnya super_admin.packages.approval)
        return view('super_admin.packages.approval', compact('bookings'));
    }

    // --- FUNGSI LAMA/LAINNYA ---

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

        // Generate sequential booking code
        $lastBooking = Booking::orderBy('created_at', 'desc')->first();
        $nextNumber = 1;
        if ($lastBooking && $lastBooking->booking_code) {
            $lastNumber = (int) str_replace('BK-', '', $lastBooking->booking_code);
            $nextNumber = $lastNumber + 1;
        }
        $bookingCode = 'BK-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        // Buat Booking utama
        $booking = Booking::create([
            'id' => Str::uuid(), // Generate UUID for id
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
                $quantity = $validated['quantity'][$addonId] ?? 1;
                $addon = Addon::find($addonId);

                \App\Models\BookPackageAddon::create([
                    'id_book' => $booking->id,
                    'id_package' => null, // For addons only
                    'id_addons' => $addonId,
                    'quantity' => $quantity,
                ]);

                // Also save to book_addons table for direct addon bookings
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
                    'booking_code' => 'BK-' . strtoupper(Str::random(8)),
                    'status' => 'pending',
                    'notes' => $validated['requests'] ?? null,
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

        $booking->load(['packages', 'products', 'addons', 'reviews']);

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
            'status' => 'required|string|in:pending,confirmed,checked_in,checked_out,maintenance,completed,cancelled',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $validated['status']]);

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }

    /**
     * Superadmin/Admin: Mengubah status booking menjadi 'confirmed' (Disetujui).
     */
    public function approve(Booking $booking)
    {
        // Guard: Pastikan hanya booking 'pending' yang bisa di-approve
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking tidak dalam status pending.');
        }

        // Lakukan perubahan status
        $booking->status = 'confirmed';
        // $booking->approved_by = Auth::id(); // Opsional
        $booking->save();

        // TODO: Kirim notifikasi/email ke user bahwa booking telah dikonfirmasi

        return back()->with('success', 'Booking #'.$booking->id.' berhasil dikonfirmasi.');
    }

    /**
     * Superadmin/Admin: Mengubah status booking menjadi 'cancelled' (Ditolak).
     */
    public function reject(Booking $booking)
    {
        // Guard: Pastikan hanya booking 'pending' yang bisa di-reject
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking tidak dalam status pending.');
        }

        // Lakukan perubahan status
        $booking->status = 'cancelled'; // Menggunakan 'cancelled' sebagai status ditolak
        // $booking->rejected_by = Auth::id(); // Opsional
        $booking->save();

        // TODO: Kirim notifikasi/email ke user bahwa booking telah dibatalkan/ditolak

        return back()->with('success', 'Booking #'.$booking->id.' berhasil ditolak (Cancelled).');
    }
    
    /**
     * Admin/Superadmin: Menampilkan detail booking.
     */
    public function showDetailAdmin(Booking $booking)
    {
        // Pastikan relasi dimuat (packages, products, addons, user)
        $booking->load(['user', 'packages', 'products', 'addons']);
        
        // Perhatikan path view yang Anda gunakan: 'admin.transaction.detail'
        return view('admin.transaction.detail', compact('booking')); 
    }

    /**
     * Contact support page for booking.
     */
    public function support(Booking $booking)
    {
        // Ensure the booking belongs to the authenticated user
        if ($booking->id_user !== Auth::id()) {
            abort(403);
        }

        $booking->load(['packages', 'products', 'addons']);

        return view('user.support', compact('booking'));
    }

    /**
     * Submit support request.
     */
    public function submitSupport(Request $request, Booking $booking)
    {
        // Ensure the booking belongs to the authenticated user
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

        // ... (Logic to save/send support request) ...

        return redirect()->route('user.detail_history', $booking->id)
            ->with('success', 'Your support request has been submitted successfully. Our team will get back to you within 24 hours.');
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