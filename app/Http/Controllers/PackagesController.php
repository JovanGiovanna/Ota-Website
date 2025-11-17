<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Addon;

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
        $products = Product::all();
        $addons = Addon::all();
        return view('super_admin.packages.create', compact('products', 'addons'));    
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
        $products = Product::all();
        $addons = Addon::all();
        return view('super_admin.packages.edit', compact('package', 'products', 'addons'));
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
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'price_publish' => 'required|numeric|min:0',
            'price_real' => 'nullable|numeric|min:0',
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after_or_equal:start_publish',
            'is_active' => 'boolean',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|uuid|exists:products,id',
            'products.*.amount' => 'required|integer|min:1',
            'addons' => 'required|array|min:1',
            'addons.*.id' => 'required|uuid|exists:addons,id',
            'addons.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // 2. Prepare JSON data for products and addons
        $data['products_data'] = $data['products'];
        $data['addons_data'] = $data['addons'];
        unset($data['products'], $data['addons']);

        // 3. Tangani Pengunggahan Gambar
        $imagePaths = [];
        if ($request->hasFile('images')) {
            try {
                foreach ($request->file('images') as $file) {
                    $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('packages', $imageName, 'public');
                    $imagePaths[] = 'packages/' . $imageName;
                }
                $data['image'] = json_encode($imagePaths);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal mengunggah gambar: ' . $e->getMessage())->withInput();
            }
        }

        // 4. Buat Slug
        $slug = Str::slug($data['name_package']);
        $originalSlug = $slug;
        $count = 1;
        while (Package::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        $data['slug'] = $slug;

        try {
            // 5. Simpan ke Database
            Package::create($data);
            return redirect()->route('super_admin.packages')->with('success', 'Paket berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Jika penyimpanan gagal, hapus gambar yang sudah terunggah
            foreach ($imagePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
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
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'price_publish' => 'required|numeric|min:0',
            'price_real' => 'nullable|numeric|min:0',
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after_or_equal:start_publish',
            'is_active' => 'boolean',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|uuid|exists:products,id',
            'products.*.amount' => 'required|integer|min:1',
            'addons' => 'required|array|min:1',
            'addons.*.id' => 'required|uuid|exists:addons,id',
            'addons.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $oldImages = $package->image ? json_decode($package->image, true) : [];

        // 2. Prepare JSON data for products and addons
        $data['products_data'] = $data['products'];
        $data['addons_data'] = $data['addons'];
        unset($data['products'], $data['addons']);

        // 3. Tangani Pengunggahan/Penggantian Gambar
        $imagePaths = $oldImages ?: [];

        // Handle image removal
        if ($request->has('remove_images') && is_array($request->remove_images)) {
            foreach ($request->remove_images as $index) {
                if (isset($imagePaths[$index])) {
                    $imageToRemove = $imagePaths[$index];
                    if (Storage::disk('public')->exists($imageToRemove)) {
                        Storage::disk('public')->delete($imageToRemove);
                    }
                    unset($imagePaths[$index]);
                }
            }
            // Reindex array
            $imagePaths = array_values($imagePaths);
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            try {
                foreach ($request->file('images') as $file) {
                    $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('packages', $imageName, 'public');
                    $imagePaths[] = 'packages/' . $imageName;
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal mengunggah gambar baru: ' . $e->getMessage())->withInput();
            }
        }

        // Update image field only if there are changes
        if ($request->hasFile('images') || $request->has('remove_images')) {
            $data['image'] = !empty($imagePaths) ? json_encode($imagePaths) : null;
        }

        // 4. Perbarui Slug jika nama paket berubah
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
            // 5. Perbarui Database
            $package->update($data);
            return redirect()->route('super_admin.packages')->with('success', 'Paket berhasil diperbarui!');
        } catch (\Exception $e) {
            // Logika fallback: jika update DB gagal, hapus gambar baru yang mungkin terunggah
            foreach ($imagePaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
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
                $images = json_decode($package->image, true);
                if (is_array($images)) {
                    foreach ($images as $image) {
                        if (Storage::disk('public')->exists($image)) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                }
            }

            $package->delete();
            return redirect()->route('super_admin.packages')->with('success', 'Paket berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus paket: ' . $e->getMessage());
        }
    }

    /**
     * Show package detail for user
     *
     * @param  \App\Models\Package  $package
     * @return \Illuminate\View\View
     */
    public function showDetail(Package $package)
    {
        $package->load(['vendorInfo', 'reviews.user']);

        $isInWishlist = false;
        if (Auth::check()) {
            $isInWishlist = Auth::user()->wishlists()
                ->where('wishable_type', Package::class)
                ->where('wishable_id', $package->id)
                ->exists();
        }

        return view('user.package_detail', compact('package', 'isInWishlist'));
    }
}