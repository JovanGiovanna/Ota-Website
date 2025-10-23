<?php

namespace App\Http\Controllers;

use App\Models\BookPackageAddon;
use App\Models\Booking;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BookPackageAddonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookPackageAddons = BookPackageAddon::with(['booking', 'addon'])->paginate(10);

        return view('super_admin.transaction_addons', compact('bookPackageAddons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Pengecekan Autentikasi
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Login dulu'], 401);
        }

        // 2. Validasi Input
        try {
            $validated = $request->validate([
                'id_book' => 'required|exists:bookings,id',
                'id_addons' => 'required|exists:addons,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        // 3. Cek apakah kombinasi sudah ada
        $existing = BookPackageAddon::where('id_book', $validated['id_book'])
            ->where('id_addons', $validated['id_addons'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Book package addon already exists for this booking and addon combination'
            ], 409);
        }

        // 4. Buat Book Package Addon
        $bookPackageAddon = BookPackageAddon::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Book package addon created successfully',
            'data' => $bookPackageAddon
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $bookPackageAddon = BookPackageAddon::with(['booking', 'addon'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $bookPackageAddon->id,
                'id_book' => $bookPackageAddon->id_book,
                'id_addons' => $bookPackageAddon->id_addons,
                'created_at' => $bookPackageAddon->created_at,
                'updated_at' => $bookPackageAddon->updated_at,
                'booking' => $bookPackageAddon->booking ? [
                    'id' => $bookPackageAddon->booking->id,
                    'booker_name' => $bookPackageAddon->booking->booker_name,
                    'booker_email' => $bookPackageAddon->booking->booker_email,
                    'status' => $bookPackageAddon->booking->status,
                ] : null,
                'addon' => $bookPackageAddon->addon ? [
                    'id' => $bookPackageAddon->addon->id,
                    'addons' => $bookPackageAddon->addon->addons,
                    'desc' => $bookPackageAddon->addon->desc,
                    'price' => $bookPackageAddon->addon->price,
                ] : null,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // 1. Pengecekan Autentikasi
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Login dulu'], 401);
        }

        $bookPackageAddon = BookPackageAddon::findOrFail($id);

        // 2. Validasi Input
        try {
            $validated = $request->validate([
                'id_book' => 'sometimes|exists:bookings,id',
                'id_addons' => 'sometimes|exists:addons,id',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        // 3. Cek apakah kombinasi baru sudah ada (jika ada perubahan)
        if (isset($validated['id_book']) && isset($validated['id_addons'])) {
            $existing = BookPackageAddon::where('id_book', $validated['id_book'])
                ->where('id_addons', $validated['id_addons'])
                ->where('id', '!=', $id)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Book package addon already exists for this booking and addon combination'
                ], 409);
            }
        }

        // 4. Update Book Package Addon
        $bookPackageAddon->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Book package addon updated successfully',
            'data' => $bookPackageAddon
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // 1. Pengecekan Autentikasi
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Login dulu'], 401);
        }

        $bookPackageAddon = BookPackageAddon::findOrFail($id);
        $bookPackageAddon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Book package addon deleted successfully'
        ]);
    }

    /**
     * Get book package addons by booking ID.
     */
    public function getByBooking($bookingId)
    {
        $bookPackageAddons = BookPackageAddon::with(['addon'])
            ->where('id_book', $bookingId)
            ->get();

        $result = $bookPackageAddons->map(function ($bookPackageAddon) {
            return [
                'id' => $bookPackageAddon->id,
                'id_book' => $bookPackageAddon->id_book,
                'id_addons' => $bookPackageAddon->id_addons,
                'created_at' => $bookPackageAddon->created_at,
                'updated_at' => $bookPackageAddon->updated_at,
                'addon' => $bookPackageAddon->addon ? [
                    'id' => $bookPackageAddon->addon->id,
                    'addons' => $bookPackageAddon->addon->addons,
                    'desc' => $bookPackageAddon->addon->desc,
                    'price' => $bookPackageAddon->addon->price,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Get book package addons by addon ID.
     */
    public function getByAddon($addonId)
    {
        $bookPackageAddons = BookPackageAddon::with(['booking'])
            ->where('id_addons', $addonId)
            ->get();

        $result = $bookPackageAddons->map(function ($bookPackageAddon) {
            return [
                'id' => $bookPackageAddon->id,
                'id_book' => $bookPackageAddon->id_book,
                'id_addons' => $bookPackageAddon->id_addons,
                'created_at' => $bookPackageAddon->created_at,
                'updated_at' => $bookPackageAddon->updated_at,
                'booking' => $bookPackageAddon->booking ? [
                    'id' => $bookPackageAddon->booking->id,
                    'booker_name' => $bookPackageAddon->booking->booker_name,
                    'booker_email' => $bookPackageAddon->booking->booker_email,
                    'status' => $bookPackageAddon->booking->status,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
