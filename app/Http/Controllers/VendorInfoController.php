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
    public function index(Request $request)
    {
        $query = VendorInfo::with(['vendor', 'city']);

        // Filter by search term
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_corporate', 'like', '%' . $search . '%')
                  ->orWhere('desc', 'like', '%' . $search . '%')
                  ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                      $vendorQuery->where('name', 'like', '%' . $search . '%')
                                  ->orWhere('email', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('city', function ($cityQuery) use ($search) {
                      $cityQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->whereHas('vendor', function ($vendorQuery) {
                    $vendorQuery->where('is_active', true);
                });
            } elseif ($status === 'inactive') {
                $query->whereHas('vendor', function ($vendorQuery) {
                    $vendorQuery->where('is_active', false);
                });
            }
        }

        // Filter by category (if needed, can be extended)
        if ($request->filled('category')) {
            // For now, we'll skip category filter as vendor_info doesn't directly relate to categories
            // This can be extended if needed
        }

        $vendorInfos = $query->paginate(10)->appends($request->query());

        return view('super_admin.vendor_details', compact('vendorInfos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendors = Vendor::all();
        $cities = City::all();

        return view('super_admin.vendor_details.create', compact('vendors', 'cities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_vendor' => 'required|exists:vendor,id',
            'id_city' => 'required|exists:city,id',
            'name_corporate' => 'required|string|max:255',
            'desc' => 'required|string',
            'coordinate_latitude' => 'required|numeric|between:-90,90',
            'coordinate_longitude' => 'required|numeric|between:-180,180',
            'landmark_description' => 'nullable|string|max:500',
        ]);

        VendorInfo::create($validated);

        return redirect()->route('super_admin.vendor_details')->with('success', 'Vendor detail created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $vendorInfo = VendorInfo::with(['vendor', 'city'])->findOrFail($id);

        return view('super_admin.vendor_details.show', compact('vendorInfo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $vendorInfo = VendorInfo::findOrFail($id);
        $vendors = Vendor::all();
        $cities = City::all();

        return view('super_admin.vendor_details.edit', compact('vendorInfo', 'vendors', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $vendorInfo = VendorInfo::findOrFail($id);

        $validated = $request->validate([
            'id_vendor' => 'required|exists:vendor,id',
            'id_city' => 'required|exists:city,id',
            'name_corporate' => 'required|string|max:255',
            'desc' => 'required|string',
            'coordinate_latitude' => 'required|numeric|between:-90,90',
            'coordinate_longitude' => 'required|numeric|between:-180,180',
            'landmark_description' => 'nullable|string|max:500',
        ]);

        $vendorInfo->update($validated);

        return redirect()->route('super_admin.vendor_details')->with('success', 'Vendor detail updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $vendorInfo = VendorInfo::findOrFail($id);
        $vendorInfo->delete();

        return redirect()->route('super_admin.vendor_details')->with('success', 'Vendor detail deleted successfully.');
    }

    /**
     * Store a newly created resource in storage (API).
     */
    public function storeApi(Request $request)
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
     * Display the specified resource (API).
     */
    public function showApi($id)
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
     * Update the specified resource in storage (API).
     */
    public function updateApi(Request $request, $id)
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
     * Remove the specified resource from storage (API).
     */
    public function destroyApi($id)
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
            'phone' => 'required|string|max:20',
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

    /**
     * Show the vendor info edit form for web.
     */
    public function editInfoForm()
    {
        $vendor = Auth::guard('vendor')->user();

        if (!$vendor) {
            return redirect()->route('vendor.login');
        }

        // Check if vendor has info
        $vendorInfo = VendorInfo::where('id_vendor', $vendor->id)->first();

        if (!$vendorInfo) {
            return redirect()->route('vendor.info')->with('info', 'Silakan lengkapi informasi vendor terlebih dahulu.');
        }

        $cities = City::all();

        return view('vendor.vendorinfo_edit', compact('vendorInfo', 'cities'));
    }

    /**
     * Update vendor info from web form.
     */
    public function updateInfo(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        if (!$vendor) {
            return redirect()->route('vendor.login');
        }

        // Check if vendor has info
        $vendorInfo = VendorInfo::where('id_vendor', $vendor->id)->first();

        if (!$vendorInfo) {
            return redirect()->route('vendor.info')->with('info', 'Silakan lengkapi informasi vendor terlebih dahulu.');
        }

        // Validate input
        $validated = $request->validate([
            'id_city' => 'required|exists:city,id',
            'name_corporate' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'desc' => 'required|string',
            'coordinate_latitude' => 'required|numeric|between:-90,90',
            'coordinate_longitude' => 'required|numeric|between:-180,180',
            'landmark_description' => 'nullable|string|max:500',
        ]);

        try {
            $vendorInfo->update($validated);

            return redirect()->route('vendor.profile')->with('success', 'Informasi vendor berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Vendor Info Web Update Failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Gagal memperbarui informasi vendor. Silakan coba lagi.']);
        }
    }

    /**
     * Export vendor details to Excel/CSV/PDF.
     */
    public function export(Request $request)
    {
        $query = VendorInfo::with(['vendor', 'city']);

        // Apply the same filters as the index method
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_corporate', 'like', '%' . $search . '%')
                  ->orWhere('desc', 'like', '%' . $search . '%')
                  ->orWhereHas('vendor', function ($vendorQuery) use ($search) {
                      $vendorQuery->where('name', 'like', '%' . $search . '%')
                                  ->orWhere('email', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('city', function ($cityQuery) use ($search) {
                      $cityQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->whereHas('vendor', function ($vendorQuery) {
                    $vendorQuery->where('is_active', true);
                });
            } elseif ($status === 'inactive') {
                $query->whereHas('vendor', function ($vendorQuery) {
                    $vendorQuery->where('is_active', false);
                });
            }
        }

        $format = $request->get('format', 'excel'); // Default to Excel

        if ($format === 'pdf') {
            return $this->exportPDF($query);
        } elseif ($format === 'csv') {
            return $this->exportCSV($query);
        } else {
            return $this->exportExcel($query);
        }
    }

    /**
     * Export to Excel using Laravel Excel.
     */
    private function exportExcel($query)
    {
        $filename = 'vendor_details_' . date('Y-m-d_H-i-s') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\VendorInfoExport($query), $filename);
    }

    /**
     * Export to CSV.
     */
    private function exportCSV($query)
    {
        $vendorInfos = $query->get();

        $filename = 'vendor_details_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($vendorInfos) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Vendor Name',
                'Vendor Email',
                'Corporate Name',
                'Description',
                'City',
                'Status',
                'Phone',
                'Address',
                'Latitude',
                'Longitude',
                'Landmark Description',
                'Created At',
                'Updated At'
            ]);

            // CSV data
            foreach ($vendorInfos as $vendorInfo) {
                fputcsv($file, [
                    $vendorInfo->vendor->name ?? '',
                    $vendorInfo->vendor->email ?? '',
                    $vendorInfo->name_corporate ?? '',
                    $vendorInfo->desc ?? '',
                    $vendorInfo->city->name ?? '',
                    $vendorInfo->vendor->is_active ? 'Active' : 'Inactive',
                    $vendorInfo->phone ?? '',
                    $vendorInfo->address ?? '',
                    $vendorInfo->coordinate_latitude ?? '',
                    $vendorInfo->coordinate_longitude ?? '',
                    $vendorInfo->landmark_description ?? '',
                    $vendorInfo->created_at ? $vendorInfo->created_at->format('Y-m-d H:i:s') : '',
                    $vendorInfo->updated_at ? $vendorInfo->updated_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF using DomPDF.
     */
    private function exportPDF($query)
    {
        $vendorInfos = $query->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.vendor_info_pdf', compact('vendorInfos'));

        $filename = 'vendor_details_' . date('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }
}
