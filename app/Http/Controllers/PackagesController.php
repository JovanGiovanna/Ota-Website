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
     * Menampilkan form untuk membuat Package baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('super_admin.packages.create');
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
     * Menampilkan form untuk mengedit Package tertentu.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\View\View
     */
    public function edit(Package $package)
    {
        return view('super_admin.packages.edit', compact('package'));
    }
    
    // ------------------------------------------------------------------

    /**
     * Menyimpan Package yang baru dibuat di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // 2. Tangani Pengunggahan Gambar
        if ($request->hasFile('image')) {
            try {
                $path = $request->file('image')->store('packages', 'public');
                $data['image'] = $path;
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal mengunggah gambar: ' . $e->getMessage())->withInput();
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
            Package::create($data);
            return redirect()->route('super_admin.packages')->with('success', 'Paket berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Jika penyimpanan gagal, hapus gambar yang sudah terunggah
            if (isset($data['image'])) {
                Storage::disk('public')->delete($data['image']);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan paket: ' . $e->getMessage())->withInput();
        }
    }

// ------------------------------------------------------------------

    /**
     * Memperbarui Package tertentu di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\RedirectResponse
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
            return redirect()->back()->withErrors($validator)->withInput();
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
                return redirect()->back()->with('error', 'Gagal mengunggah gambar baru: ' . $e->getMessage())->withInput();
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
            return redirect()->route('super_admin.packages')->with('success', 'Paket berhasil diperbarui!');
        } catch (\Exception $e) {
            // Logika fallback: jika update DB gagal, hapus gambar baru yang mungkin terunggah
            if (isset($data['image']) && $data['image'] !== $oldImage) {
                Storage::disk('public')->delete($data['image']);
            }
            return redirect()->back()->with('error', 'Gagal memperbarui paket: ' . $e->getMessage())->withInput();
        }
    }

// ------------------------------------------------------------------

    /**
     * Menghapus Package tertentu dari database.
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Package $package)
    {
        try {
            // Hapus file gambar terkait sebelum menghapus record dari database
            if ($package->image) {
                Storage::disk('public')->delete($package->image);
            }

            $package->delete();
            return redirect()->route('super_admin.packages')->with('success', 'Paket berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus paket: ' . $e->getMessage());
        }
    }
}