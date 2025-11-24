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
        $products = Product::paginate(5); 
        $addons = Addon::paginate(5);
    
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
        $vendorInfos = \App\Models\VendorInfo::with('vendor')->get();

        // 📝 Pastikan products_data dan addons_data adalah array
        $selectedProductsData = $package->products_data ?? [];
        if (is_string($selectedProductsData)) {
            $selectedProductsData = json_decode($selectedProductsData, true) ?: [];
        }

        $selectedAddonsData = $package->addons_data ?? [];
        if (is_string($selectedAddonsData)) {
            $selectedAddonsData = json_decode($selectedAddonsData, true) ?: [];
        }

        // Normalize the products data to get the currently selected product IDs and their pax
        $selectedProductsData = collect($selectedProductsData)->map(function ($product) {
            // Menggunakan 'nta' atau 'basic_price' sebagai fallback untuk harga unit
            $price = $product['nta'] ?? $product['basic_price'] ?? 0; 
            return [
                'id' => $product['id'] ?? '',
                'name' => $product['name'] ?? 'Unknown Product',
                'price' => $price,
                'pax' => $product['pax'] ?? 1,
            ];
        })->toArray();

        // Normalize the addons data to get the currently selected addon IDs and their pax
        $selectedAddonsData = collect($selectedAddonsData)->map(function ($addon) {
            // Menggunakan 'nta' atau 'basic_price' sebagai fallback untuk harga unit
            $price = $addon['nta'] ?? $addon['basic_price'] ?? 0;
            return [
                'id' => $addon['id'] ?? '',
                // Addon name bisa berada di key 'addons' atau 'name' tergantung format penyimpanan
                'name' => $addon['name'] ?? $addon['addons'] ?? 'Unknown Addon', 
                'price' => $price,
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
            
            'pax_paid_input' => 'required|numeric|min:0', // Harga Jual Per Pax
            'nta' => 'nullable|numeric|min:0', // NTA Total Paket
            
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after_or_equal:start_publish',
            'is_active' => 'boolean',
            
            'products' => 'required|array|min:1',
            'products.*' => 'uuid|exists:products,id',
            'product_pax' => 'required|array',
            'product_pax.*' => 'required|integer|min:1',
            
            'addons' => 'nullable|array', // Mengubah menjadi nullable
            'addons.*' => 'uuid|exists:addons,id',
            'addon_pax' => 'nullable|array',
            'addon_pax.*' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        
        // 2. Prepare JSON data for products and addons (Mengambil data lengkap dari DB)
        $productsData = [];
        $totalNTA = 0;
        
        $selectedProducts = Product::whereIn('id', $data['products'])->get()->keyBy('id');
        
        foreach ($data['products'] as $productId) {
            $product = $selectedProducts->get($productId);
            if ($product) {
                $pax = $data['product_pax'][$productId] ?? 1;
                // Ambil NTA atau basic_price
                $unitPrice = $product->nta ?? ($product->basic_price ?? 0); 
                $subTotal = $unitPrice * $pax;
                $totalNTA += $subTotal;
                
                $productsData[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'images' => $product->images,
                    'nta' => $unitPrice, 
                    'pax' => $pax,
                    'sub_total' => $subTotal,
                ];
            }
        }
        $data['products_data'] = $productsData;

        $addonsData = [];
        if (!empty($data['addons'])) {
            $selectedAddons = Addon::whereIn('id', $data['addons'])->get()->keyBy('id');
            
            foreach ($data['addons'] as $addonId) {
                $addon = $selectedAddons->get($addonId);
                if ($addon) {
                    $pax = $data['addon_pax'][$addonId] ?? 1;
                    // Ambil NTA atau basic_price
                    $unitPrice = $addon->nta ?? ($addon->basic_price ?? 0);
                    $subTotal = $unitPrice * $pax;
                    $totalNTA += $subTotal;
                    
                    $addonsData[] = [
                        'id' => $addon->id,
                        'name' => $addon->addons,
                        'desc' => $addon->desc,
                        'images' => $addon->images,
                        'nta' => $unitPrice, 
                        'pax' => $pax,
                        'sub_total' => $subTotal,
                    ];
                }
            }
        }
        $data['addons_data'] = $addonsData;

        // Set kolom harga
        $data['pax_paid'] = $data['pax_paid_input']; // Harga Jual Per Pax
        $data['nta'] = $totalNTA; // NTA Total Paket (Dihitung dari total produk & addon)

        // Bersihkan data yang tidak dibutuhkan untuk model
        unset(
            $data['products'], 
            $data['product_pax'], 
            $data['addons'], 
            $data['addon_pax'], 
            $data['pax_paid_input']
        );
        // ❌ Kolom ini sudah dihapus dari validasi/data: price_real, discount_percentage

        // 3. Tangani Pengunggahan Gambar (Logika sama)
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

        // 4. Buat Slug (Logika sama)
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
            
            // 🔄 Kolom Harga Baru: Hanya Pax Paid dan NTA
            'pax_paid_input' => 'required|numeric|min:0', // Harga Jual Per Pax
            'nta' => 'nullable|numeric|min:0', // NTA Total Paket (akan dihitung ulang)
            
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after_or_equal:start_publish',
            'is_active' => 'boolean',
            
            'products' => 'required|array|min:1',
            'products.*' => 'uuid|exists:products,id',
            'product_pax' => 'required|array',
            'product_pax.*' => 'required|integer|min:1',
            
            'addons' => 'nullable|array', // Mengubah menjadi nullable
            'addons.*' => 'uuid|exists:addons,id',
            'addon_pax' => 'nullable|array',
            'addon_pax.*' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $oldImages = $package->images ?: [];
        
        // 2. Prepare JSON data for products and addons (Mengambil data lengkap dari DB)
        $productsData = [];
        $totalNTA = 0;
        
        $selectedProducts = Product::whereIn('id', $data['products'])->get()->keyBy('id');
        
        foreach ($data['products'] as $productId) {
            $product = $selectedProducts->get($productId);
            if ($product) {
                $pax = $data['product_pax'][$productId] ?? 1;
                // Ambil NTA atau basic_price
                $unitPrice = $product->nta ?? ($product->basic_price ?? 0); 
                $subTotal = $unitPrice * $pax;
                $totalNTA += $subTotal;
                
                $productsData[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'images' => $product->images,
                    'nta' => $unitPrice, 
                    'pax' => $pax,
                    'sub_total' => $subTotal,
                ];
            }
        }
        $data['products_data'] = $productsData;

        $addonsData = [];
        if (!empty($data['addons'])) {
            $selectedAddons = Addon::whereIn('id', $data['addons'])->get()->keyBy('id');
            
            foreach ($data['addons'] as $addonId) {
                $addon = $selectedAddons->get($addonId);
                if ($addon) {
                    $pax = $data['addon_pax'][$addonId] ?? 1;
                    // Ambil NTA atau basic_price
                    $unitPrice = $addon->nta ?? ($addon->basic_price ?? 0);
                    $subTotal = $unitPrice * $pax;
                    $totalNTA += $subTotal;
                    
                    $addonsData[] = [
                        'id' => $addon->id,
                        'name' => $addon->addons,
                        'desc' => $addon->desc,
                        'images' => $addon->images,
                        'nta' => $unitPrice, 
                        'pax' => $pax,
                        'sub_total' => $subTotal,
                    ];
                }
            }
        }
        $data['addons_data'] = $addonsData;

        // Set kolom harga
        $data['pax_paid'] = $data['pax_paid_input']; // Harga Jual Per Pax
        $data['nta'] = $totalNTA; // NTA Total Paket (Dihitung ulang)

        // Bersihkan data yang tidak dibutuhkan untuk model
        unset(
            $data['products'], 
            $data['product_pax'], 
            $data['addons'], 
            $data['addon_pax'], 
            $data['pax_paid_input']
        );
        // ❌ Kolom ini sudah dihapus dari validasi/data: price_real, discount_percentage

        // 3. Tangani Pengunggahan/Penggantian Gambar (Logika sama)
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
        $newUploadedPaths = [];
        if ($request->hasFile('images')) {
            try {
                foreach ($request->file('images') as $file) {
                    $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('packages', $imageName, 'public');
                    $newPath = 'packages/' . $imageName;
                    $imagePaths[] = $newPath;
                    $newUploadedPaths[] = $newPath; // Simpan path baru untuk cleanup jika gagal
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal mengunggah gambar baru: ' . $e->getMessage())->withInput();
            }
        }

        // Update images field only if there are changes
        if ($request->hasFile('images') || $request->has('remove_images')) {
            $data['images'] = !empty($imagePaths) ? $imagePaths : null;
        }

        // 4. Perbarui Slug jika nama paket berubah (Logika sama)
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
            foreach ($newUploadedPaths as $path) {
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