<?php

namespace App\Http\Controllers;

use App\Models\VendorInfo;
use App\Models\Vendor;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                  ->orWhere('description', 'like', '%' . $search . '%')
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
            'description' => 'required|string',
            'phone' => 'required|string|max:20',
            'coordinate_latitude' => 'required|numeric|between:-90,90',
            'coordinate_longitude' => 'required|numeric|between:-180,180',
            'landmark_description' => 'nullable|string|max:500',
        ]);

        VendorInfo::create($validated);

        return redirect()->route('super_admin.vendor_details')->with('success', 'Vendor detail created successfully.');
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

        Log::info('VendorInfo update request data:', $request->all());

        $validated = $request->validate([
            'id_vendor' => 'required|exists:vendor,id',
            'id_city' => 'required|exists:city,id',
            'name_corporate' => 'required|string|max:255',
            'description' => 'required|string',
            'phone' => 'required|string|max:20',
            'coordinate_latitude' => 'required|numeric|between:-90,90',
            'coordinate_longitude' => 'required|numeric|between:-180,180',
            'landmark_description' => 'nullable|string|max:500',
            'is_verified' => 'boolean',
        ]);

        Log::info('Validated data:', $validated);

        $result = $vendorInfo->update($validated);

        Log::info('Update result:', ['success' => $result, 'is_verified_after' => $vendorInfo->fresh()->is_verified]);

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
     * Show the vendor info form for web (vendor side).
     */
    public function showInfoForm()
    {
        $vendor = Auth::guard('vendor')->user();

        if (!$vendor) {
            return redirect()->route('vendor.login');
        }

        $vendorInfo = VendorInfo::where('id_vendor', $vendor->id)->first();

        if ($vendorInfo) {
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

        $existing = VendorInfo::where('id_vendor', $vendor->id)->first();

        if ($existing) {
            return redirect()->route('vendor.dashboard')->with('info', 'Informasi vendor sudah lengkap.');
        }

        $validated = $request->validate([
            'id_city' => 'required|exists:city,id',
            'name_corporate' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'description' => 'required|string',
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

        $vendorInfo = VendorInfo::where('id_vendor', $vendor->id)->first();

        if (!$vendorInfo) {
            return redirect()->route('vendor.info')->with('info', 'Silakan lengkapi informasi vendor terlebih dahulu.');
        }

        $validated = $request->validate([
            'id_city' => 'required|exists:city,id',
            'name_corporate' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'description' => 'required|string',
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
     * Export vendor details to CSV/PDF/Excel (web).
     */
    public function export(Request $request)
    {
        $query = VendorInfo::with(['vendor', 'city']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_corporate', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
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

        $format = $request->get('format', 'excel');

        if ($format === 'pdf') {
            return $this->exportPDF($query);
        } elseif ($format === 'csv') {
            return $this->exportCSV($query);
        } else {
            return $this->exportExcel($query);
        }
    }

    private function exportExcel($query)
    {
        $filename = 'vendor_details_' . date('Y-m-d_H-i-s') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\VendorInfoExport($query), $filename);
    }

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

            fputcsv($file, [
                'Vendor Name',
                'Vendor Email',
                'Corporate Name',
                'Description',
                'City',
                'Status',
                'Phone',
                'Latitude',
                'Longitude',
                'Landmark Description',
                'Created At',
                'Updated At'
            ]);

            foreach ($vendorInfos as $vendorInfo) {
                fputcsv($file, [
                    $vendorInfo->vendor->name ?? '',
                    $vendorInfo->vendor->email ?? '',
                    $vendorInfo->name_corporate ?? '',
                    $vendorInfo->description ?? '',
                    $vendorInfo->city->name ?? '',
                    $vendorInfo->vendor->is_active ? 'Active' : 'Inactive',
                    $vendorInfo->phone ?? '',
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

    private function exportPDF($query)
    {
        $vendorInfos = $query->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.vendor_info_pdf', compact('vendorInfos'));
        $filename = 'vendor_details_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($filename);
    }
}
