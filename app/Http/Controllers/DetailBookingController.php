<?php

namespace App\Http\Controllers;

use App\Models\Detail_Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DetailBookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $detailBookings = Detail_Booking::with(['booking', 'product'])->paginate(10);

        return view('super_admin.transaction_products', compact('detailBookings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'kamar_id' => 'required|exists:kamars,id',
            'Nama_Tamu' => 'required|string|max:255',
            'dewasa' => 'required|integer|min:1',
            'anak' => 'required|integer|min:0',
            'Special_Request' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $detailBooking = Detail_Booking::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Detail booking created successfully',
            'data' => $detailBooking
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $detailBooking = Detail_Booking::with(['booking', 'kamar'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $detailBooking
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $detailBooking = Detail_Booking::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'booking_id' => 'sometimes|exists:bookings,id',
            'kamar_id' => 'sometimes|exists:kamars,id',
            'Nama_Tamu' => 'sometimes|string|max:255',
            'dewasa' => 'sometimes|integer|min:1',
            'anak' => 'sometimes|integer|min:0',
            'Special_Request' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $detailBooking->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Detail booking updated successfully',
            'data' => $detailBooking
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $detailBooking = Detail_Booking::findOrFail($id);
        $detailBooking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Detail booking deleted successfully'
        ]);
    }
}
