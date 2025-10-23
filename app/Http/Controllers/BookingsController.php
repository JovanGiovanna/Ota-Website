<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Addon;
use App\Models\Kamar;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\Detail_Booking;
use App\Models\BookingFasilitas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log; // Tambahkan ini di bagian atas file

class BookingsController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'package', 'detailBookings.product', 'addons'])->paginate(10);

        return view('super_admin.transaction_packages', compact('bookings'));
    }

    /**
     * Show booking form.
     */
public function create(Request $request)
{
    $kamars = Kamar::all(); // ← tambah ini
    $addons = Addon::all(); // Pastikan Addon::all() mengembalikan data
    $selectedKamar = $request->query('kamar_id') ? Kamar::find($request->query('kamar_id')) : null;

    return view('user.booking_form', compact('kamars', 'selectedKamar','addons'));
}



public function createfasilitas(Request $request)
{
    // Mengambil semua data Fasilitas utama

    
    // Mengambil data Add-ons
    $addons = Addon::all();
    
    // Mengambil Fasilitas yang dipilih dari parameter URL 'facility_id'
    // PERBAIKAN: Menggunakan 'facility_id' sesuai URL
   
    // Mengirimkan data ke view 'user.booking_fasilitas'
    return view('user.booking_fasilitas', compact('facilities', 'selectedfasilitas', 'addons'));
}


/**
 * Menyimpan data booking ke database.
 */
public function store(Request $request)
{
    // 1. Pengecekan Autentikasi
    if (!Auth::check()) {
        return $request->wantsJson()
            ? response()->json(['success' => false, 'message' => 'Login dulu'], 401)
            : redirect()->route('login');
    }

    // 2. Validasi Input
    try {
        $validated = $request->validate([
            'id_package' => 'required|exists:packages,id',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email',
            'booker_telp' => 'required|string',
            'checkin_appointment_start' => 'required|date',
            'checkout_appointment_end' => 'required|date|after:checkin_appointment_start',
            'duration_days' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,confirmed,checked_in,completed,cancelled',
            'note' => 'nullable|string',
            'detail_bookings' => 'required|array|min:1',
            'detail_bookings.*.product_id' => 'required|exists:products,id',
            'detail_bookings.*.booker_name' => 'required|string|max:255',
            'detail_bookings.*.adults' => 'required|integer|min:1',
            'detail_bookings.*.children' => 'required|integer|min:0',
            'detail_bookings.*.special_request' => 'nullable|string',
            'addons' => 'nullable|array',
            'addons.*' => 'exists:addons,id',
        ]);
    } catch (ValidationException $e) {
        return back()->withErrors($e->errors())->withInput();
    }

    // 3. Buat Entitas Booking Utama
    $booking = Booking::create([
        'id_user' => Auth::id(),
        'id_package' => $validated['id_package'],
        'booker_name' => $validated['booker_name'],
        'booker_email' => $validated['booker_email'],
        'booker_telp' => $validated['booker_telp'],
        'checkin_appointment_start' => $validated['checkin_appointment_start'],
        'checkout_appointment_end' => $validated['checkout_appointment_end'],
        'duration_days' => $validated['duration_days'],
        'amount' => $validated['amount'],
        'total_price' => $validated['total_price'],
        'status' => $validated['status'],
        'note' => $validated['note'],
    ]);

    // 4. Simpan Detail Booking
    foreach ($validated['detail_bookings'] as $detailData) {
        Detail_Booking::create([
            'booking_id' => $booking->id,
            'product_id' => $detailData['product_id'],
            'booker_name' => $detailData['booker_name'],
            'adults' => $detailData['adults'],
            'children' => $detailData['children'],
            'special_request' => $detailData['special_request'] ?? null,
        ]);
    }

    // 5. Attach Add-ons menggunakan Relasi Many-to-Many
    $addonsToAttach = [];
    if (!empty($validated['addons'])) {
        foreach (array_unique($validated['addons']) as $addon_id) {
            $addonsToAttach[$addon_id] = ['quantity' => 1];
        }
        $booking->addons()->attach($addonsToAttach);
    }

    // 6. Pesan dan Respons
    $msg = 'Booking berhasil dibuat! Durasi: ' . $validated['duration_days'] . ' hari. Total Rp. ' . number_format($validated['total_price'], 0, ',', '.');
    if (!empty($addonsToAttach)) {
        $addonNames = Addon::whereIn('id', array_keys($addonsToAttach))->pluck('name')->toArray();
        $msg .= ' (dengan addons: ' . implode(', ', $addonNames) . ')';
    }

    return $request->wantsJson()
        ? response()->json(['success' => true, 'message' => $msg, 'data' => $booking], 201)
        : redirect()->route('booking.history')->with('success', $msg);
}


public function storefasilitas(Request $request)
{
    // 1. Pengecekan Autentikasi
    if (!Auth::check()) {
        return $request->wantsJson()
            ? response()->json(['success'=>false,'message'=>'Login dulu'], 401)
            : redirect()->route('login');
    }

    // 2. Validasi Input
    try {
        $validated = $request->validate([
            // Tidak perlu diubah, karena input form masih 'fasilitas_ids'
            'fasilitas_ids'     => 'required|array|min:1',
            'fasilitas_ids.*'   => 'exists:fasilitas,id',
            'Nama_Tamu'         => 'required|string|max:255',
            'arrival_time'      => 'required|date_format:H:i',
            'check_in'          => 'required|date|after_or_equal:today',
            'check_out'         => 'required|date|after:check_in',
            'Phone'             => 'required|string',
            'Special_Request'   => 'nullable|string',
            'addons'            => 'nullable|array',
            'addons.*'          => 'exists:addons,id',
        ]);
    } catch (ValidationException $e) {
        return back()->withErrors($e->errors())->withInput();
    }

    // Hitung Durasi
    $durasi = Carbon::parse($validated['check_out'])->diffInDays(Carbon::parse($validated['check_in']));
    $durasi = max(1, $durasi); // Minimal 1 hari
    
    $total_harga = 0;
    $fasilitasDetailData = []; 
    $addonsToAttach = []; 

    // 3. Pengecekan Ketersediaan dan Perhitungan Harga Kamar
    foreach (array_unique($validated['fasilitas_ids']) as $fasilitasId) {
        

       
     
        
        // Data untuk Detail Fasilitas
        $fasilitasDetailData[] = [
            'fasilitas_id'      => $fasilitasId,
            'Nama_Tamu'         => $validated['Nama_Tamu'],
            'durasi'            => $durasi,
            'dewasa'            => $fasilitas->max_adults ?? 1, 
            'anak'              => $fasilitas->max_children ?? 0, 
            'Special Request'   => $validated['Special_Request'],
        ];
    }

    // 4. Perhitungan Harga Add-ons dan persiapan Attach
    if (!empty($validated['addons'])) {
        foreach (array_unique($validated['addons']) as $addon_id) {
            $addon = Addon::findOrFail($addon_id);
            $total_harga += $addon->price; 
            $addonsToAttach[$addon->id] = ['quantity' => 1]; 
        }
    }
    
    // 5. Buat Entitas Booking Utama
    // Tambahkan baris 'Email' ke list fillable di model BookingFasilitas.php
    $booking = BookingFasilitas::create([
        'user_id'      => Auth::id(),
        'Email'        => Auth::user()->email,
        'Phone'        => $validated['Phone'],
        'arrival_time' => $validated['arrival_time'],
        'check_in'     => $validated['check_in'],
        'check_out'    => $validated['check_out'],
        'durasi'       => $durasi,
        'total_harga'  => $total_harga, 
        'status'       => 'diproses',
    ]);

    // 6. Simpan Detail Booking (Kamar)
    foreach ($fasilitasDetailData as $detailData) {
        $booking->detailFasilitas()->create($detailData);
    }
    
    // 7. Attach Add-ons menggunakan Relasi Many-to-Many
    if (!empty($addonsToAttach)) {
        $booking->addons()->attach($addonsToAttach);
    }

    // 8. Pesan dan Respons
    $msg = 'Booking berhasil dibuat! Durasi: '.$durasi.' hari. Total Rp. '. number_format($total_harga, 0, ',', '.');
    if (!empty($addonsToAttach)) {
        $addonNames = Addon::whereIn('id', array_keys($addonsToAttach))->pluck('name')->toArray();
        $msg .= ' (dengan addons: '.implode(', ', $addonNames).')';
    }

    return $request->wantsJson()
        ? response()->json(['success'=>true,'message'=>$msg,'data'=>$booking], 201)
        : redirect()->route('booking.history')->with('success',$msg);
}
    /**
     * Cancel booking kamar.
     */
    public function cancelBooking($booking_id)
    {
        $booking = Booking::findOrFail($booking_id);
        if ($booking->user_id !== Auth::id()) return redirect()->back()->with('error','Tidak berhak membatalkan booking ini.');
        if (in_array($booking->status,['cancelled','pending'])) return redirect()->back()->with('error','Booking sudah dibatalkan atau menunggu persetujuan.');

        $booking->status = 'pending_cancel';
        $booking->save();

        return redirect()->back()->with('success','Permintaan pembatalan booking terkirim.');
    }

    /**
     * Cancel booking fasilitas.
     */
    public function cancelBookingFasilitas($id)
    {
        $booking = BookingFasilitas::findOrFail($id);
        if ($booking->user_id !== Auth::id()) return redirect()->back()->with('error','Tidak berhak membatalkan booking ini.');
        if (in_array($booking->status,['cancelled','pending'])) return redirect()->back()->with('error','Booking sudah dibatalkan atau menunggu persetujuan.');

        $booking->status = 'pending_cancel';
        $booking->save();

        return redirect()->back()->with('success','Permintaan pembatalan booking fasilitas terkirim.');
    }

    /**
     * Booking history.
     */
public function history()
{
    $userId = Auth::id();
    $bookingsKamar = Booking::where('user_id', $userId)
                             ->with('detailBookings.kamar') 
                             ->orderBy('created_at', 'desc')
                             ->get();
                             
    $bookingsFasilitas = BookingFasilitas::where('user_id', $userId)
                                          ->with('fasilitas')
                                          ->orderBy('created_at', 'desc')
                                          ->get();
                                          
    return view('user.booking_history', compact('bookingsKamar', 'bookingsFasilitas'));
}

/**
 * Menampilkan detail booking universal (Kamar atau Fasilitas).
 * @param string $type Tipe booking ('kamar' atau 'fasilitas')
 * @param int $id ID dari booking yang dicari
 */
public function showDetail($type, $id)
{
    // 1. NORMALISASI TYPE
    $type = strtolower($type); 
    
    $userId = Auth::id();
    $bookingKamar = null;
    $bookingFasilitas = null; // $bookingFasilitas diinisialisasi sebagai null
    $typeLabel = null;
    $dataFound = false;

    if ($type === 'kamar') {
        // Mengambil Booking Kamar dengan relasi detailnya
        $bookingKamar = Booking::with('detailBookings.kamar')
            ->where('user_id', $userId)
            ->findOrFail($id);
        $typeLabel = 'Kamar';
        $dataFound = true;

    } elseif ($type === 'fasilitas') {
        // MENGAMBIL BOOKING FASILITAS DENGAN RELASI NESTED: detailFasilitas -> fasilitas
        $bookingFasilitas = BookingFasilitas::with('detailFasilitas.fasilitas') // <--- PERUBAHAN KRUSIAL
            ->where('user_id', $userId)
            ->findOrFail($id);
        $typeLabel = 'Fasilitas';
        $dataFound = true;

    } else {
        abort(404, 'Tipe booking tidak valid.');
    }

    // 2. PENGECEKAN KEBERADAAN (Safety Check) - findOrFail sudah menangani sebagian besar kasus.
    // ... (Logika pengecekan tetap sama) ...
    // Di Laravel, findOrFail akan menghentikan eksekusi jika tidak ditemukan, 
    // sehingga pengecekan dataFound di sini adalah opsional.
    
    // Mengirim kedua objek booking ke view tunggal
    return view('user.booking_detail', compact('bookingKamar', 'bookingFasilitas', 'type', 'typeLabel'));
}

    
/**
 * Admin: list all bookings.
 */
public function adminIndex()
{
    // 🛑 PERBAIKAN UTAMA: 
    // Mengganti 'kamar' dengan relasi bertingkat yang benar: 'detailBookings.kamar'
    $bookingsKamar = Booking::with(['user', 'detailBookings.kamar'])
                            ->orderBy('created_at', 'desc')
                            ->get();
                            
    // Kode untuk fasilitas booking tetap sama
    $bookingsFasilitas = BookingFasilitas::with(['user', 'fasilitas'])
                                          ->orderBy('created_at', 'desc')
                                          ->get();
                                          
    return view('admin.booking.index', compact('bookingsKamar', 'bookingsFasilitas'));
}

    /**
     * Admin: Approve / Reject cancel kamar.
     */
    public function approveCancel($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'cancelled';
        $booking->save();
        return redirect()->back()->with('success','Booking berhasil dibatalkan oleh admin.');
    }

    public function rejectCancel($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'diproses';
        $booking->save();
        return redirect()->back()->with('success','Permintaan cancel ditolak, status dikembalikan ke diproses.');
    }

    /**
     * Admin: Approve / Reject cancel fasilitas.
     */
    public function approveCancelFasilitas($id)
    {
        $booking = BookingFasilitas::findOrFail($id);
        $booking->status = 'cancelled';
        $booking->save();
        return redirect()->back()->with('success','Booking fasilitas berhasil dibatalkan oleh admin.');
    }

    public function rejectCancelFasilitas($id)
    {
        $booking = BookingFasilitas::findOrFail($id);
        $booking->status = 'diproses';
        $booking->save();
        return redirect()->back()->with('success','Permintaan cancel fasilitas ditolak, status dikembalikan ke diproses.');
    }
// App/Http/Controllers/BookingController.php

/**
 * Admin: Check-in Kamar.
 */
public function checkin($id)
{
    // 1. Mengambil Booking dengan relasi bertingkat ke DetailBooking dan Kamar
    $booking = Booking::with('detailBookings.kamar')->findOrFail($id);

    // 2. Cek Status Booking
    if ($booking->status !== 'diproses') {
        return back()->with('error', 'Booking tidak bisa check-in karena statusnya bukan "diproses".');
    }

    // 3. Cek Ketersediaan Kamar secara iteratif
    // Kita harus memastikan SEMUA kamar di booking ini tersedia
    foreach ($booking->detailBookings as $detail) {
        $kamar = $detail->kamar;

        // Cek apakah jumlah kamar di inventaris sudah habis
        // Catatan: Asumsi $kamar->jumlah adalah stok kamar yang tersedia saat ini.
        if ($kamar->jumlah < 1) { 
            return back()->with('error', 'Check-in GAGAL: Kamar ' . $kamar->name . ' tidak tersedia (stok habis).');
        }
    }
    
    // 4. Jika SEMUA kamar tersedia, lakukan transaksi Check-in
    
    try {
        // A. Update Status Booking
        $booking->status = 'checkin';
        $booking->save();

        // B. Kurangi Jumlah/Stok Kamar yang Dipakai
        $successCount = 0;
        foreach ($booking->detailBookings as $detail) {
            $kamar = $detail->kamar;
            
            // Mengurangi stok kamar sebanyak 1 unit (asumsi 1 detail booking = 1 kamar)
            // Jika Anda memiliki kolom quantity di detail booking, gunakan $detail->quantity
            $kamar->jumlah -= 1; 
            $kamar->save();
            $successCount++;
        }
        
        return back()->with('success', "Check-in berhasil. {$successCount} jenis kamar telah dikurangi stoknya.");

    } catch (\Exception $e) {
        // Handle jika ada error saat save ke database
        // Anda mungkin ingin menambahkan logic rollback di sini jika menggunakan transaksi database
        return back()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
    }
}

// App/Http/Controllers/BookingController.php

/**
 * Admin: Checkout Kamar.
 */
public function checkout($id)
{
    // 1. Ambil Booking dan relasi Kamar melalui DetailBooking
    $booking = Booking::with('detailBookings.kamar')->findOrFail($id);

    // 2. Cek Status Booking
    if ($booking->status !== 'checkin') {
        return back()->with('error', 'Checkout GAGAL: Booking tidak dalam status "checkin".');
    }
    
    // 3. Update Status Booking menjadi 'checkout'
    $booking->status = 'checkout';
    $booking->save();
    
    // 4. Kembalikan Stok Kamar
    $restoredCount = 0;
    
    // Karena satu booking bisa memiliki banyak kamar (melalui DetailBooking), kita harus mengulanginya
    foreach ($booking->detailBookings as $detail) {
        $kamar = $detail->kamar;
        
        // Asumsi: Kita mengembalikan 1 unit kamar per DetailBooking.
        // Jika Anda menggunakan kolom 'quantity' di DetailBooking, gunakan: $kamar->jumlah += $detail->quantity;
        $kamar->jumlah += 1; 
        $kamar->save();
        $restoredCount++;
    }

    return back()->with('success', "Checkout berhasil. Booking telah selesai dan stok {$restoredCount} jenis kamar telah dikembalikan.");
}

    /**
     * Admin: Set selesai kamar.
     */
    public function setSelesaiKamar($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'selesai';
        $booking->save();
        return redirect()->back()->with('success','Booking kamar ditandai selesai.');
    }

    /**
     * Admin: Set selesai fasilitas.
     */
    public function setSelesaiFasilitas($id)
    {
        $booking = BookingFasilitas::findOrFail($id);
        $booking->status = 'selesai';
        $booking->save();
        return redirect()->back()->with('success','Booking fasilitas ditandai selesai.');
    }

    /**
     * Admin: Set maintenance kamar.
     */
    public function setMaintenanceKamar($id)
    {
        $booking = Booking::findOrFail($id);
        if ($booking->status === 'selesai') {
            $booking->status = 'maintenance';
            $booking->save();
            return back()->with('success', 'Booking kamar masuk maintenance.');
        }
        return back()->with('error', 'Booking kamar tidak bisa diubah ke maintenance.');
    }

    /**
     * Admin: Set maintenance fasilitas.
     */
    public function setMaintenanceFasilitas($id)
    {
        $bookingFasilitas = BookingFasilitas::findOrFail($id);
        if ($bookingFasilitas->status === 'selesai') {
            $bookingFasilitas->status = 'maintenance';
            $bookingFasilitas->save();
            return back()->with('success', 'Booking fasilitas masuk maintenance.');
        }
        return back()->with('error', 'Booking fasilitas tidak bisa diubah ke maintenance.');
    }

// App/Http/Controllers/BookingController.php

/**
 * Admin: Done maintenance kamar.
 */
public function setMaintenanceDoneKamar($id)
{
    // 1. Ambil Booking dengan relasi Kamar melalui DetailBooking (Fix Relasi)
    $booking = Booking::with('detailBookings.kamar')->findOrFail($id);

    // 2. Cek Status Booking
    if ($booking->status !== 'maintenance') {
        return back()->with('error', 'Booking kamar tidak dalam status maintenance.');
    }

    // 3. Update Status Booking menjadi 'selesai'
    $booking->status = 'selesai';
    $booking->save();

    // 4. Kembalikan Stok Kamar
    $restoredCount = 0;

    // Ulangi semua kamar yang terlibat dalam booking maintenance ini
    foreach ($booking->detailBookings as $detail) {
        $kamar = $detail->kamar;
        
        // 💡 PENTING: Tambahkan kembali stok kamar yang sebelumnya dikurangi untuk maintenance
        $kamar->jumlah += 1; 
        $kamar->save();
        $restoredCount++;
    }

    // 5. Berikan feedback yang akurat
    return back()->with('success', "Maintenance kamar selesai. {$restoredCount} jenis kamar telah ditambahkan kembali ke stok.");
}

    /**
     * Admin: Done maintenance fasilitas.
     */
    public function setMaintenanceDoneFasilitas($id)
    {
        $bookingFasilitas = BookingFasilitas::findOrFail($id);
        if ($bookingFasilitas->status === 'maintenance') {
            $bookingFasilitas->status = 'selesai';
            $bookingFasilitas->save();
            return back()->with('success', 'Booking fasilitas selesai maintenance.');
        }
        return back()->with('error', 'Booking fasilitas tidak dalam status maintenance.');
    }

    /**
 * Admin: Check-in Fasilitas.
 */
public function checkinFasilitas($id)
{
    $booking = BookingFasilitas::with('fasilitas')->findOrFail($id);

    if ($booking->status !== 'diproses') {
        return back()->with('error', 'Booking fasilitas tidak bisa check-in.');
    }

    $booking->status = 'checkin';
    $booking->save();

    return back()->with('success', 'Check-in fasilitas berhasil.');
}

/**
 * Admin: Checkout Fasilitas.
 */
public function checkoutFasilitas($id)
{
    $booking = BookingFasilitas::findOrFail($id);

    if ($booking->status !== 'checkin') {
        return back()->with('error','Booking fasilitas tidak bisa checkout.');
    }

    $booking->status = 'checkout';
    $booking->save();

    return back()->with('success','Checkout fasilitas berhasil.');
}


}
