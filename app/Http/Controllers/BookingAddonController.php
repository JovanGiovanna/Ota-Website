<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BookingAddonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookingAddons = Booking::with('addons')->get()->map(function ($booking) {
            return [
                'booking_id' => $booking->id,
                'addons' => $booking->addons->map(function ($addon) {
                    return [
                        'addon_id' => $addon->id,
                        'name' => $addon->name,
                        'harga' => $addon->pivot->harga ?? $addon->price,
                        'jumlah' => $addon->pivot->jumlah ?? $addon->pivot->quantity,
                        'subtotal' => $addon->pivot->subtotal ?? ($addon->price * ($addon->pivot->quantity ?? 1)),
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $bookingAddons
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'addon_id' => 'required|exists:addons,id',
            'quantity' => 'required|integer|min:1',
            'harga' => 'nullable|numeric|min:0',
            'jumlah' => 'nullable|integer|min:1',
            'subtotal' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = Booking::findOrFail($request->booking_id);
        $addon = Addon::findOrFail($request->addon_id);

        // Attach addon to booking with pivot data
        $booking->addons()->attach($addon->id, [
            'quantity' => $request->quantity,
            'harga' => $request->harga ?? $addon->price,
            'jumlah' => $request->jumlah ?? $request->quantity,
            'subtotal' => $request->subtotal ?? ($addon->price * $request->quantity),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking addon created successfully',
            'data' => [
                'booking_id' => $booking->id,
                'addon_id' => $addon->id,
                'quantity' => $request->quantity,
                'harga' => $request->harga ?? $addon->price,
                'jumlah' => $request->jumlah ?? $request->quantity,
                'subtotal' => $request->subtotal ?? ($addon->price * $request->quantity),
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($bookingId)
    {
        $booking = Booking::with('addons')->findOrFail($bookingId);

        $addons = $booking->addons->map(function ($addon) {
            return [
                'addon_id' => $addon->id,
                'name' => $addon->name,
                'harga' => $addon->pivot->harga ?? $addon->price,
                'jumlah' => $addon->pivot->jumlah ?? $addon->pivot->quantity,
                'subtotal' => $addon->pivot->subtotal ?? ($addon->price * ($addon->pivot->quantity ?? 1)),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'booking_id' => $booking->id,
                'addons' => $addons
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $bookingId, $addonId)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'sometimes|integer|min:1',
            'harga' => 'nullable|numeric|min:0',
            'jumlah' => 'nullable|integer|min:1',
            'subtotal' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $booking = Booking::findOrFail($bookingId);
        $addon = Addon::findOrFail($addonId);

        // Update pivot data
        $booking->addons()->updateExistingPivot($addon->id, [
            'quantity' => $request->quantity ?? DB::raw('quantity'),
            'harga' => $request->harga ?? DB::raw('harga'),
            'jumlah' => $request->jumlah ?? DB::raw('jumlah'),
            'subtotal' => $request->subtotal ?? DB::raw('subtotal'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking addon updated successfully',
            'data' => [
                'booking_id' => $booking->id,
                'addon_id' => $addon->id,
                'quantity' => $request->quantity,
                'harga' => $request->harga,
                'jumlah' => $request->jumlah,
                'subtotal' => $request->subtotal,
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($bookingId, $addonId)
    {
        $booking = Booking::findOrFail($bookingId);
        $addon = Addon::findOrFail($addonId);

        $booking->addons()->detach($addon->id);

        return response()->json([
            'success' => true,
            'message' => 'Booking addon deleted successfully'
        ]);
    }
}
