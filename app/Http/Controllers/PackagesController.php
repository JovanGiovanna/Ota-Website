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
        $vendorInfos = \App\Models\VendorInfo::with('vendor')->get();
        return view('super_admin.packages.create', compact('products', 'addons', 'vendorInfos'));
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
        $vendorInfos = \App\Models\VendorInfo::with('vendor')->get();

        // Ensure products_data and addons_data are arrays (handle JSON strings)
        $selectedProductsData = $package->products_data;
        if (is_string($selectedProductsData)) {
            $selectedProductsData = json_decode($selectedProductsData, true) ?: [];
        }
        $selectedProductsData = $selectedProductsData ?? [];

        $selectedAddonsData = $package->addons_data;
        if (is_string($selectedAddonsData)) {
            $selectedAddonsData = json_decode($selectedAddonsData, true) ?: [];
        }
        $selectedAddonsData = $selectedAddonsData ?? [];

        // Normalize the products data to ensure 'name' key exists
        $selectedProductsData = collect($selectedProductsData)->map(function ($product) {
            return [
                'id' => $product['id'] ?? '',
                'name' => $product['name'] ?? 'Unknown Product',
                'price' => $product['price'] ?? 0,
                'pax' => $product['pax'] ?? 1,
            ];
        })->toArray();

        // Normalize the addons data to ensure 'name' key exists
        $selectedAddonsData = collect($selectedAddonsData)->map(function ($addon) {
            return [
                'id' => $addon['id'] ?? '',
                'name' => $addon['name'] ?? $addon['addons'] ?? 'Unknown Addon',
                'price' => $addon['price'] ?? 0,
                'pax' => $addon['pax'] ?? 1,
            ];
        })->toArray();

        // Ambil ID produk dan addon yang sudah terpilih untuk pre-select di form
        $selectedProductIds = collect($selectedProductsData)->pluck('id')->toArray();
        $selectedAddonIds = collect($selectedAddonsData)->pluck('id')->toArray();

        return view('super_admin.packages.edit', compact('package', 'products', 'addons', 'vendorInfos', 'selectedProductIds', 'selectedAddonIds', 'selectedProductsData', 'selectedAddonsData'));
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
            'price_publish_input' => 'required|numeric|min:0',
            'price_real' => 'nullable|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after_or_equal:start_publish',
            'id_vendor_info' => 'required|uuid|exists:vendor_info,id',
            'is_active' => 'boolean',
            'products' => 'required|array|min:1',
            'products.*' => 'uuid|exists:products,id',
            'product_pax' => 'required|array',
            'product_pax.*' => 'required|integer|min:1',
            'addons' => 'required|array|min:1',
            'addons.*' => 'uuid|exists:addons,id',
            'addon_pax' => 'required|array',
            'addon_pax.*' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // 2. Prepare JSON data for products and addons
        $productsData = [];
        foreach ($data['products'] as $productId) {
            $productsData[] = [
                'id' => $productId,
                'pax' => $data['product_pax'][$productId] ?? 1,
            ];
        }
        $data['products_data'] = $productsData;

        $addonsData = [];
        foreach ($data['addons'] as $addonId) {
            $addonsData[] = [
                'id' => $addonId,
                'pax' => $data['addon_pax'][$addonId] ?? 1,
            ];
        }
        $data['addons_data'] = $addonsData;

        // Set price_publish from input
        $data['price_publish'] = $data['price_publish_input'];

        unset($data['products'], $data['product_pax'], $data['addons'], $data['addon_pax'], $data['price_publish_input']);

        // 3. Tangani Pengunggahan Gambar
        $imagePaths = [];
        if ($request->hasFile('images')) {
            try {
                foreach ($request->file('images') as $file) {
                    $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('packages', $imageName, 'public');
                    $imagePaths[] = 'packages/' . $imageName;
                }
                $data['images'] = $imagePaths;
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
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'string',
            'price_publish_input' => 'required|numeric|min:0',
            'price_real' => 'nullable|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after_or_equal:start_publish',
            'id_vendor_info' => 'required|uuid|exists:vendor_info,id',
            'is_active' => 'boolean',
            'products' => 'required|array|min:1',
            'products.*' => 'uuid|exists:products,id',
            'product_pax' => 'required|array',
            'product_pax.*' => 'required|integer|min:1',
            'addons' => 'required|array|min:1',
            'addons.*' => 'uuid|exists:addons,id',
            'addon_pax' => 'required|array',
            'addon_pax.*' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $oldImages = $package->images ?: [];

        // 2. Prepare JSON data for products and addons
        $productsData = [];
        foreach ($data['products'] as $productId) {
            $productsData[] = [
                'id' => $productId,
                'pax' => $data['product_pax'][$productId] ?? 1,
            ];
        }
        $data['products_data'] = $productsData;

        $addonsData = [];
        foreach ($data['addons'] as $addonId) {
            $addonsData[] = [
                'id' => $addonId,
                'pax' => $data['addon_pax'][$addonId] ?? 1,
            ];
        }
        $data['addons_data'] = $addonsData;

        // Set price_publish from input
        $data['price_publish'] = $data['price_publish_input'];

        unset($data['products'], $data['product_pax'], $data['addons'], $data['addon_pax'], $data['price_publish_input']);

        // 3. Tangani Pengunggahan/Penggantian Gambar
        $imagePaths = $oldImages ?: [];

        // Handle image removal
        if ($request->has('remove_images') && is_array($request->remove_images)) {
            foreach ($request->remove_images as $imagePathToRemove) {
                $index = array_search($imagePathToRemove, $imagePaths);
                if ($index !== false) {
                    if (Storage::disk('public')->exists($imagePathToRemove)) {
                        Storage::disk('public')->delete($imagePathToRemove);
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

        // Update images field only if there are changes
        if ($request->hasFile('images') || $request->has('remove_images')) {
            $data['images'] = !empty($imagePaths) ? $imagePaths : null;
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
            if ($package->images && is_array($package->images)) {
                foreach ($package->images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
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