<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Models\Package;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Product;
use App\Models\Addon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Exception; // Tambahkan ini jika belum ada
use App\Models\VendorInfo;

class DashboardController extends Controller
{
    public function index()
    {
        // -------------------------
        // Stats Cards
        // -------------------------
        $totalPackages = Package::count();
        $totalBooking = Booking::count();
        $totalUsers = User::count();
        $totalRevenue = Booking::sum('total_price');

        // -------------------------
        // 1️⃣ Booking Kamar per Bulan
        // -------------------------
        $bookings = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month');

        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        $bookingData = [];
        foreach(range(1,12) as $m) {
            $bookingData[] = $bookings[$m] ?? 0;
        }

        // -------------------------
        // 2️⃣ Pendapatan per Bulan
        // -------------------------
        $pendapatan = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_price) as total')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month');

        $pendapatanData = [];
        foreach(range(1,12) as $m) {
            $pendapatanData[] = $pendapatan[$m] ?? 0;
        }

        // -------------------------
        // 3️⃣ Status Booking (Doughnut Chart)
        // -------------------------
        $statusDataRaw = Booking::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusLabels = $statusDataRaw->keys()->toArray();
        $statusData = $statusDataRaw->values()->toArray();


        // -------------------------
        // Return ke view
        // -------------------------
        return view('admin.dashboard', compact(
            'totalPackages','totalBooking','totalUsers','totalRevenue',
            'months','bookingData','pendapatanData',
            'statusLabels','statusData',
        ));
    }

    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    public function bookings()
    {
        $bookings = Booking::with('user')->paginate(10);
        return view('admin.transaction.index', compact('bookings'));
    }
    
    public function packages()
    {
        $packages = Package::paginate(10);
        return view('admin.packages', compact('packages'));
    }
    
    public function packagesCreate()
    {
    $products = Product::paginate(5);
    $addons = Addon::paginate(5);
    $vendorInfos = VendorInfo::with('vendor')->get(); // Get all vendor info with vendor relationship
        // Hanya mengirim produk dan addon yang diperlukan
        return view('admin.packages.create', compact('products', 'addons', 'vendorInfos'));
    }

    /**
     * Menyimpan paket baru dengan multi-select produk dan addon.
     */
// ... di dalam class DashboardController



public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name_package' => 'required|string|max:255',
        'description' => 'nullable|string',
        'images' => 'required|array|min:1|max:10',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        'price_publish_input' => 'nullable|numeric|min:0',
        'discount_percentage' => 'required|integer|min:0|max:100',

        'start_publish' => 'required|date',
        'end_publish' => 'nullable|date|after_or_equal:start_publish',
        'is_active' => 'required|boolean',
        'id_vendor_info' => 'required|uuid|exists:vendor_info,id',

        'products' => 'required|array|min:1',
        'products.*' => 'uuid|exists:products,id',
        'product_pax' => 'required|array',
        'product_pax.*' => 'required|integer|min:1',

        'addons' => 'nullable|array',
        'addons.*' => 'uuid|exists:addons,id',
        'addon_pax' => 'nullable|array',
        'addon_pax.*' => 'nullable|integer|min:1',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $data = $validator->validated();
    $productsIds = $data['products'];
    $addonsIds = $data['addons'] ?? []; 
    $totalRealPrice = 0;
    
    $uploadedImagePaths = []; 

    DB::beginTransaction();
    try {
        
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('packages', 'public');
                $uploadedImagePaths[] = $path;
            }
        }

        $productsData = [];
        $selectedProducts = Product::whereIn('id', $productsIds)->get(); 
        foreach ($selectedProducts as $product) {
            $pax = $data['product_pax'][$product->id] ?? 1;
            $subTotal = $product->price * $pax;
            $totalRealPrice += $subTotal;

            $productsData[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'pax' => (int) $pax,
                'sub_total' => $subTotal,
            ];
        }

        $addonsData = [];
        $selectedAddons = Addon::whereIn('id', $addonsIds)->get();
        foreach ($selectedAddons as $addon) {
            $pax = $data['addon_pax'][$addon->id] ?? 1;
            $subTotal = $addon->price * $pax;
            $totalRealPrice += $subTotal;

            $addonsData[] = [
                'id' => $addon->id,
                'name' => $addon->addons,
                'price' => $addon->price,
                'pax' => (int) $pax,
                'sub_total' => $subTotal,
            ];
        }
        
        $discount = $data['discount_percentage'];
        $pricePublishCalculated = $totalRealPrice * (1 - $discount / 100);
        
        $finalPricePublish = $data['price_publish_input'] ?? $pricePublishCalculated;


        $slug = Str::slug($data['name_package']);
        $originalSlug = $slug;
        $count = 1;
        while (Package::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        Package::create([
            'name_package' => $data['name_package'],
            'slug' => $slug,
            'description' => $data['description'],
            'images' => $uploadedImagePaths,
            'price_publish' => round($finalPricePublish),
            'price_real' => $totalRealPrice,
            'discount_percentage' => $discount,
            'start_publish' => $data['start_publish'],
            'end_publish' => $data['end_publish'] ?? null,
            'is_active' => $data['is_active'],
            'id_vendor_info' => $data['id_vendor_info'],
            'products_data' => $productsData,
            'addons_data' => $addonsData,
        ]);

        DB::commit();
        return redirect()->route('admin.packages')->with('success', 'Paket berhasil ditambahkan!');

    } catch (\Exception $e) {
        DB::rollBack();
        
        if (!empty($uploadedImagePaths)) {
            Storage::disk('public')->delete($uploadedImagePaths);
        }
        
        \Log::error('Package store failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal menyimpan paket: ' . $e->getMessage())->withInput();
    }
}

    public function packagesUpdate(Package $package)
    {
        // Ambil semua produk dan addon untuk opsi multi-select
        $products = Product::paginate(5);
        $addons = Addon::paginate(5);
        $vendorInfos = VendorInfo::with('vendor')->get(); // Get all vendor info with vendor relationship

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

        // Ensure images is an array
        $currentImages = $package->images ?? [];
        if (is_string($currentImages)) {
            $currentImages = json_decode($currentImages, true) ?: [];
        }
        $currentImages = $currentImages ?? [];

        // Kirim data yang diperlukan ke view edit
        return view('admin.packages.edit', compact(
            'package',
            'products',
            'addons',
            'vendorInfos',
            'selectedProductIds',
            'selectedAddonIds',
            'selectedProductsData',
            'selectedAddonsData'
        ));
    }

   // ... di dalam class DashboardController

public function update(Request $request, Package $package)
{
    // 1. Validasi Data
    $validator = Validator::make($request->all(), [
        'name_package' => 'required|string|max:255',
        'description' => 'nullable|string',
        'images' => 'nullable|array|min:1|max:10',
        'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',

        // Harga dan Diskon
        'price_publish_input' => 'nullable|numeric|min:0', // Input harga publish optional/manual
        'discount_percentage' => 'required|integer|min:0|max:100', // Wajib diisi (0-100)

        'start_publish' => 'required|date',
        'end_publish' => 'nullable|date|after_or_equal:start_publish',
        'is_active' => 'required|boolean', // Diubah dari 'boolean' ke 'required|boolean'
        'id_vendor_info' => 'required|uuid|exists:vendor_info,id',

        // Produk dan PAX
        'products' => 'required|array|min:1',
        'products.*' => 'uuid|exists:products,id',
        'product_pax' => 'required|array',
        'product_pax.*' => 'required|integer|min:1',

        // Addon dan PAX
        'addons' => 'nullable|array',
        'addons.*' => 'uuid|exists:addons,id',
        'addon_pax' => 'nullable|array',
        'addon_pax.*' => 'nullable|integer|min:1',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $data = $validator->validated();
    $productsIds = $data['products'];
    $addonsIds = $data['addons'] ?? [];
    $totalRealPrice = 0; 
    
    DB::beginTransaction();
    try {
        // --- 2. Proses Produk dan Hitung Harga Real ---
        $productsData = [];
        $selectedProducts = Product::whereIn('id', $productsIds)->get();
        foreach ($selectedProducts as $product) {
            $pax = $data['product_pax'][$product->id] ?? 1;
            $subTotal = $product->price * $pax;
            $totalRealPrice += $subTotal;

            $productsData[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'pax' => (int) $pax,
                'sub_total' => $subTotal,
            ];
        }

        // --- 3. Proses Addon dan Tambahkan ke Harga Real ---
        $addonsData = [];
        $selectedAddons = Addon::whereIn('id', $addonsIds)->get();
        foreach ($selectedAddons as $addon) {
            $pax = $data['addon_pax'][$addon->id] ?? 1;
            $subTotal = $addon->price * $pax;
            $totalRealPrice += $subTotal;

            $addonsData[] = [
                'id' => $addon->id,
                'name' => $addon->addons,
                'price' => $addon->price,
                'pax' => (int) $pax,
                'sub_total' => $subTotal,
            ];
        }

        // --- 4. Hitung Harga Publish Berdasarkan Diskon ---
        $discount = $data['discount_percentage'];
        $pricePublishCalculated = $totalRealPrice * (1 - $discount / 100);
        
        // Gunakan harga kalkulasi, atau harga manual dari input jika diisi
        $finalPricePublish = $data['price_publish_input'] ?? $pricePublishCalculated;

        // --- 5. Persiapkan Data Update ---
        $packageData = [
            'name_package' => $data['name_package'],
            'description' => $data['description'],
            'price_publish' => $finalPricePublish, // Hasil hitungan/input
            'price_real' => $totalRealPrice,       // Hasil hitungan
            'discount_percentage' => $discount,     // Dari input
            'start_publish' => $data['start_publish'],
            'end_publish' => $data['end_publish'] ?? null,
            'is_active' => $data['is_active'],
            'id_vendor_info' => $data['id_vendor_info'],
            'products_data' => $productsData,
            'addons_data' => $addonsData,
        ];

        // --- 6. Handle Images ---
        $currentImages = $package->images ?? [];
        if (is_string($currentImages)) {
            $currentImages = json_decode($currentImages, true) ?: [];
        }
        $currentImages = $currentImages ?? [];

        // Handle image removal
        if ($request->has('remove_images') && is_array($request->remove_images)) {
            foreach ($request->remove_images as $imagePathToRemove) {
                $index = array_search($imagePathToRemove, $currentImages);
                if ($index !== false) {
                    if (Storage::disk('public')->exists($imagePathToRemove)) {
                        Storage::disk('public')->delete($imagePathToRemove);
                    }
                    unset($currentImages[$index]);
                }
            }
            // Re-index array after removal
            $currentImages = array_values($currentImages);
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $currentImages[] = $image->store('packages', 'public');
            }
        }

        // Update images only if there were changes
        if ($request->hasFile('images') || $request->has('remove_images')) {
            $packageData['images'] = $currentImages;
        }

        // --- 7. Buat atau Perbarui Slug (Logika sama seperti kode Anda) ---
        $slug = Str::slug($packageData['name_package']);
        $originalSlug = $slug;
        $count = 1;
        while (Package::where('slug', $slug)->where('id', '!=', $package->id)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        $packageData['slug'] = $slug;

        // --- 8. Simpan Pembaruan ke Database ---
        $package->update($packageData);
        DB::commit();

        return redirect()->route('admin.packages')->with('success', 'Paket berhasil diperbarui!');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Package update failed: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal memperbarui paket: ' . $e->getMessage())->withInput();
    }
}
// ...

    
    public function analytics()
    {
        // Analytics data
        $analytics = [
            'totalBookings' => Booking::count(),
            'totalRevenue' => Booking::sum('total_price'),
            'totalUsers' => User::count(),
            // 'totalCategories' => Category::count(), // Error karena Model Category tidak terdefinisi
        ];

        return view('admin.analytics', compact('analytics'));
    }

    public function destroy(Package $package)
    {
        // Handle image deletion
        $images = $package->images ?? [];
        if (is_string($images)) {
            $images = json_decode($images, true) ?: [];
        }
        $images = $images ?? [];

        // Delete images from storage
        foreach ($images as $imagePath) {
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        // Delete the package
        $package->delete();

        return redirect()->route('admin.packages')->with('success', 'Paket berhasil dihapus!');
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
