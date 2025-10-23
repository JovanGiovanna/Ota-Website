<?php

namespace App\Http\Controllers;

use App\Models\VendorInfo;
use App\Models\Vendor;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class VendorInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendorInfos = VendorInfo::with(['vendor', 'city'])->get();

        $result = $vendorInfos->map(function ($vendorInfo) {
            return [
                'id' => $vendorInfo->id,
                'id_vendor' => $vendorInfo->id_vendor,
                'id_city' => $vendorInfo->id_city,
                'name_corporate' => $vendorInfo->name_corporate,
                'desc' => $vendorInfo->desc,
                'coordinate_latitude' => $vendorInfo->coordinate_latitude,
                'coordinate_longitude' => $vendorInfo->coordinate_longitude,
                'landmark_description' => $vendorInfo->landmark_description,
                'created_at' => $vendorInfo->created_at,
                'updated_at' => $vendorInfo->updated_at,
                'vendor' => $vendorInfo->vendor ? [
                    'id' => $vendorInfo->vendor->id,
                    'name' => $vendorInfo->vendor->name,
                    'email' => $vendorInfo->vendor->email,
                ] : null,
                'city' => $vendorInfo->city ? [
                    'id' => $vendorInfo->city->id,
                    'name' => $vendorInfo->city->name,
                    'province_id' => $vendorInfo->city->province_id,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
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
                'id_vendor' => 'required|exists:vendor,id',
                'id_city' => 'required|exists:city,id',
                'name_corporate' => 'required|string|max:255',
                'desc' => 'required|string',
                'coordinate_latitude' => 'required|numeric|between:-90,90',
                'coordinate_longitude' => 'required|numeric|between:-180,180',
                'landmark_description' => 'nullable|string|max:500',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        // 3. Cek apakah vendor sudah memiliki info
        $existing = VendorInfo::where('id_vendor', $validated['id_vendor'])->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor already has info. Use update instead.'
            ], 409);
        }

        // 4. Buat Vendor Info
        $vendorInfo = VendorInfo::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vendor info created successfully',
            'data' => $vendorInfo
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $vendorInfo = VendorInfo::with(['vendor', 'city'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $vendorInfo->id,
                'id_vendor' => $vendorInfo->id_vendor,
                'id_city' => $vendorInfo->id_city,
                'name_corporate' => $vendorInfo->name_corporate,
                'desc' => $vendorInfo->desc,
                'coordinate_latitude' => $vendorInfo->coordinate_latitude,
                'coordinate_longitude' => $vendorInfo->coordinate_longitude,
                'landmark_description' => $vendorInfo->landmark_description,
                'created_at' => $vendorInfo->created_at,
                'updated_at' => $vendorInfo->updated_at,
                'vendor' => $vendorInfo->vendor ? [
                    'id' => $vendorInfo->vendor->id,
                    'name' => $vendorInfo->vendor->name,
                    'email' => $vendorInfo->vendor->email,
                ] : null,
                'city' => $vendorInfo->city ? [
                    'id' => $vendorInfo->city->id,
                    'name' => $vendorInfo->city->name,
                    'province_id' => $vendorInfo->city->province_id,
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

        $vendorInfo = VendorInfo::findOrFail($id);

        // 2. Validasi Input
        try {
            $validated = $request->validate([
                'id_city' => 'sometimes|exists:city,id',
                'name_corporate' => 'sometimes|string|max:255',
                'desc' => 'sometimes|string',
                'coordinate_latitude' => 'sometimes|numeric|between:-90,90',
                'coordinate_longitude' => 'sometimes|numeric|between:-180,180',
                'landmark_description' => 'nullable|string|max:500',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        // 3. Update Vendor Info
        $vendorInfo->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Vendor info updated successfully',
            'data' => $vendorInfo
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

        $vendorInfo = VendorInfo::findOrFail($id);
        $vendorInfo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vendor info deleted successfully'
        ]);
    }

    /**
     * Get vendor info by vendor ID.
     */
    public function getByVendor($vendorId)
    {
        $vendorInfo = VendorInfo::with(['city'])->where('id_vendor', $vendorId)->first();

        if (!$vendorInfo) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor info not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $vendorInfo->id,
                'id_vendor' => $vendorInfo->id_vendor,
                'id_city' => $vendorInfo->id_city,
                'name_corporate' => $vendorInfo->name_corporate,
                'desc' => $vendorInfo->desc,
                'coordinate_latitude' => $vendorInfo->coordinate_latitude,
                'coordinate_longitude' => $vendorInfo->coordinate_longitude,
                'landmark_description' => $vendorInfo->landmark_description,
                'created_at' => $vendorInfo->created_at,
                'updated_at' => $vendorInfo->updated_at,
                'city' => $vendorInfo->city ? [
                    'id' => $vendorInfo->city->id,
                    'name' => $vendorInfo->city->name,
                    'province_id' => $vendorInfo->city->province_id,
                ] : null,
            ]
        ]);
    }

    /**
     * Get vendor infos by city ID.
     */
    public function getByCity($cityId)
    {
        $vendorInfos = VendorInfo::with(['vendor'])->where('id_city', $cityId)->get();

        $result = $vendorInfos->map(function ($vendorInfo) {
            return [
                'id' => $vendorInfo->id,
                'id_vendor' => $vendorInfo->id_vendor,
                'id_city' => $vendorInfo->id_city,
                'name_corporate' => $vendorInfo->name_corporate,
                'desc' => $vendorInfo->desc,
                'coordinate_latitude' => $vendorInfo->coordinate_latitude,
                'coordinate_longitude' => $vendorInfo->coordinate_longitude,
                'landmark_description' => $vendorInfo->landmark_description,
                'created_at' => $vendorInfo->created_at,
                'updated_at' => $vendorInfo->updated_at,
                'vendor' => $vendorInfo->vendor ? [
                    'id' => $vendorInfo->vendor->id,
                    'name' => $vendorInfo->vendor->name,
                    'email' => $vendorInfo->vendor->email,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Show the vendor info form for web.
     */
    public function showInfoForm()
    {
        $vendor = Auth::guard('vendor')->user();

        if (!$vendor) {
            return redirect()->route('vendor.login');
        }

        // Check if vendor already has info
        $vendorInfo = VendorInfo::where('id_vendor', $vendor->id)->first();

        if ($vendorInfo) {
            // If already has info, redirect to dashboard
            return redirect()->route('vendor.dashboard');
        }

        return view('vendor.vendorinfo');
    }

    /**
     * Store vendor info from web form.
     */
    public function storeInfo(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        if (!$vendor) {
            return redirect()->route('vendor.login');
        }

        // Check if vendor already has info
        $existing = VendorInfo::where('id_vendor', $vendor->id)->first();

        if ($existing) {
            return redirect()->route('vendor.dashboard')->with('info', 'Informasi vendor sudah lengkap.');
        }

        // Validate input
        $validated = $request->validate([
            'id_city' => 'required|exists:city,id',
            'name_corporate' => 'required|string|max:255',
            'desc' => 'required|string',
            'coordinate_latitude' => 'required|numeric|between:-90,90',
            'coordinate_longitude' => 'required|numeric|between:-180,180',
            'landmark_description' => 'nullable|string|max:500',
        ]);

        $validated['id_vendor'] = $vendor->id;

        try {
            VendorInfo::create($validated);

            return redirect()->route('vendor.dashboard')->with('success', 'Informasi vendor berhasil disimpan. Selamat datang di dashboard!');
        } catch (\Exception $e) {
            Log::error('Vendor Info Web Store Failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan informasi vendor. Silakan coba lagi.']);
        }
    }
}
