<?php

namespace App\Http\Controllers;

use App\Models\BookProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; 

class BookProductController extends Controller
{
    /**
     * Menampilkan daftar semua pemesanan.
     */
    public function index()
    {
        $bookings = BookProduct::with(['user', 'product'])->paginate(10);
        return response()->json($bookings);
    }

    /**
     * Menyimpan pemesanan baru ke database.
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'id_product' => 'required|uuid|exists:products,id',
        'checkin_appointment_start_datetime' => 'required|date',
        'checkout_appointment_end_datetime' => 'required|date|after:checkin_appointment_start_datetime',
        'amount' => 'required|numeric|min:1',
        'booker_name' => 'required|string|max:150',
        'booker_email' => 'required|email|max:150',
        'booker_telp' => 'nullable|string|max:20',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {

        // ambil product untuk hitung harga
        $product = \App\Models\Product::find($request->id_product);
        $totalPrice = $product ? $product->price * $request->amount : 0;

        // generate booking code
        $last = BookProduct::orderBy('created_at', 'desc')->first();
        $nextNumber = $last ? ((int) substr($last->booking_code, 3)) + 1 : 1;
        $bookingCode = 'BP-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        $booking = BookProduct::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'id_user' => Auth::id(),
            'id_product' => $request->id_product,
            'checkin_appointment_start_datetime' => $request->checkin_appointment_start_datetime,
            'checkout_appointment_end_datetime' => $request->checkout_appointment_end_datetime,
            'amount' => $request->amount,
            'booker_name' => $request->booker_name,
            'booker_email' => $request->booker_email,
            'booker_telp' => $request->booker_telp,
            'booking_code' => $bookingCode,
            'total_price' => $totalPrice,
        ]);

        return response()->json([
            'message' => 'Booking produk berhasil.',
            'data' => $booking
        ], 201);

    } catch (\Exception $e) {

        return response()->json([
            'message' => 'Gagal membuat booking.',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Menampilkan detail pemesanan tertentu.
     */
    public function show(string $id)
    {
        $booking = BookProduct::with(['user', 'product'])->find($id);

        if (!$booking) {
            return response()->json(['message' => 'Pemesanan tidak ditemukan.'], 404);
        }

        return response()->json($booking);
    }
    
    // ... Tambahkan method update dan destroy jika diperlukan ...
}