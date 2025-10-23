<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PackagesController extends Controller
{
    /**
     * Menampilkan daftar semua Package dalam view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $packages = Package::orderBy('created_at', 'desc')->paginate(10);

        return view('super_admin.packages', compact('packages'));
    }
    
    /**
     * Metode create hanya mengembalikan status sukses (tidak ada form view).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        return response()->json([
            'success' => true,
            'message' => 'Endpoint siap untuk menerima data POST.',
        ], 200);
    }
    
    /**
     * Menampilkan Package tertentu.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Package $package)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail paket berhasil diambil.',
            'data' => $package
        ], 200);
    }
    
    /**
     * Metode edit hanya mengembalikan data paket untuk diubah.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Package $package)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data paket siap untuk diedit.',
            'data' => $package
        ], 200);
    }
    
    // ------------------------------------------------------------------

    /**
     * Menyimpan Package yang baru dibuat di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. Validasi Data
        $validator = Validator::make($request->all(), [
            'name_package' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price_publish' => 'required|numeric|min:0',
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after_or_equal:start_publish',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        
        // 2. Tangani Pengunggahan Gambar
        if ($request->hasFile('image')) {
            try {
                $path = $request->file('image')->store('packages', 'public');
                $data['image'] = $path;
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengunggah gambar.',
                    'error_detail' => $e->getMessage()
                ], 500);
            }
        }

        // 3. Buat Slug
        $slug = Str::slug($data['name_package']);
        $originalSlug = $slug;
        $count = 1;
        while (Package::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        $data['slug'] = $slug;

        try {
            // 4. Simpan ke Database
            $package = Package::create($data);
            return response()->json([
                'success' => true,
                'message' => 'Paket berhasil ditambahkan!',
                'data' => $package
            ], 201); // HTTP 201 Created
        } catch (\Exception $e) {
            // Jika penyimpanan gagal, hapus gambar yang sudah terunggah
            if (isset($data['image'])) {
                Storage::disk('public')->delete($data['image']);
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan paket.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

// ------------------------------------------------------------------

    /**
     * Memperbarui Package tertentu di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Package $package)
    {
        // 1. Validasi Data
        $validator = Validator::make($request->all(), [
            'name_package' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|sometimes|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'price_publish' => 'required|numeric|min:0',
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after_or_equal:start_publish',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $oldImage = $package->image;

        // 2. Tangani Pengunggahan/Penggantian Gambar
        if ($request->hasFile('image')) {
            try {
                // Hapus gambar lama jika ada
                if ($oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
                // Simpan file baru
                $path = $request->file('image')->store('packages', 'public');
                $data['image'] = $path;
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengunggah gambar baru.',
                    'error_detail' => $e->getMessage()
                ], 500);
            }
        } 
        // Logika untuk menghapus gambar tanpa mengganti (jika ada input 'clear_image')
        elseif ($request->input('clear_image') && $oldImage) { 
            Storage::disk('public')->delete($oldImage);
            $data['image'] = null;
        }

        // 3. Perbarui Slug jika nama paket berubah
        if (isset($data['name_package']) && $data['name_package'] !== $package->name_package) {
            $slug = Str::slug($data['name_package']);
            $originalSlug = $slug;
            $count = 1;
            while (Package::where('slug', $slug)->where('id', '!=', $package->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $data['slug'] = $slug;
        }

        try {
            // 4. Perbarui Database
            $package->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Paket berhasil diperbarui!',
                'data' => $package
            ], 200);
        } catch (\Exception $e) {
            // Logika fallback: jika update DB gagal, hapus gambar baru yang mungkin terunggah
            if (isset($data['image']) && $data['image'] !== $oldImage) {
                Storage::disk('public')->delete($data['image']);
            }
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui paket.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

// ------------------------------------------------------------------

    /**
     * Menghapus Package tertentu dari database.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Package $package)
    {
        try {
            // Hapus file gambar terkait sebelum menghapus record dari database
            if ($package->image) {
                Storage::disk('public')->delete($package->image);
            }
            
            $package->delete();
            return response()->json([
                'success' => true,
                'message' => 'Paket berhasil dihapus!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus paket.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }
}