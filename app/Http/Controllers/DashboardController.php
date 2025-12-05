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
use Exception;
use App\Models\VendorInfo;

class DashboardController extends Controller
{
    public function index()
    {
        // ... (Metode index tetap sama) ...
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
        // Prepare view variables and return to super admin dashboard view
        // -------------------------
        // Map local variable names to what the dashboard view expects
        $revenueData = $pendapatanData;
        // Provide a default facilityData (12 months zeros) if not available
        $facilityData = array_fill(0, 12, 0);

        return view('super_admin.dashboard', compact(
            'totalPackages','totalBooking','totalUsers','totalRevenue',
            'months','bookingData','revenueData',
            'statusData','facilityData'
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
        // Mengambil VendorInfo karena mungkin diperlukan di view, meskipun tidak disimpan di tabel packages
        $vendorInfos = VendorInfo::with('vendor')->get(); 
        return view('admin.packages.create', compact('products', 'addons', 'vendorInfos'));
    }

    /**
     * Menyimpan paket baru dengan multi-select produk dan addon.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_package' => 'required|string|max:255',
            'description' => 'nullable|string',
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',

            // ✅ PAX PAID (Harga Jual Per Pax Manual)
            'pax_paid_input' => 'nullable|numeric|min:0',

            // ❌ DIHAPUS: 'discount_percentage'
            // 'discount_percentage' => 'required|integer|min:0|max:100',

            'nta' => 'required|numeric|min:0', // ✅ VALIDASI NTA (Nett Total Package Price) dari hidden input
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after_or_equal:start_publish',
            'is_active' => 'required|boolean',

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
        $totalNTA = 0; // Nett Total Package Price
        $totalPax = 0;
        
        $uploadedImagePaths = []; 

        DB::beginTransaction();
        try {
            
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('packages', 'public');
                    $uploadedImagePaths[] = $path;
                }
            }

            // 1. Hitung Total NTA (Nett Total Package Price) dan siapkan data produk
            $productsData = [];
            $selectedProducts = Product::whereIn('id', $productsIds)->get(); 
            foreach ($selectedProducts as $product) {
                $pax = $data['product_pax'][$product->id] ?? 1;
                $productPrice = $product->nta ?? $product->basic_price ?? 0;
                
                $subTotal = $productPrice * $pax;
                $totalNTA += $subTotal;
                $totalPax += $pax;

                $productsData[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'nta' => $productPrice, 
                    'pax' => (int) $pax,
                    'sub_total' => $subTotal,
                ];
            }

            // 2. Tambahkan Addons ke Total NTA
            $addonsData = [];
            $selectedAddons = Addon::whereIn('id', $addonsIds)->get();
            foreach ($selectedAddons as $addon) {
                $pax = $data['addon_pax'][$addon->id] ?? 1;
                $addonPrice = $addon->nta ?? $addon->basic_price ?? 0; 

                $subTotal = $addonPrice * $pax;
                $totalNTA += $subTotal;
                // Asumsi: Addon tidak menambah total pax paket
                // $totalPax += $pax; 

                $addonsData[] = [
                    'id' => $addon->id,
                    'name' => $addon->addons,
                    'nta' => $addonPrice, 
                    'pax' => (int) $pax,
                    'sub_total' => $subTotal,
                ];
            }
            
            // 💡 Perhitungan Harga Pax Paid (Harga Jual Per Pax)
            // Karena diskon dihapus, Total Harga Jual = Total NTA
            $totalPricePublishCalculated = $totalNTA;
            
            // Harga Jual Per Pax = Total NTA / Total Pax
            $paxPaidCalculated = ($totalPax > 0) 
                                ? $totalPricePublishCalculated / $totalPax
                                : $totalPricePublishCalculated; 
            
            // Gunakan input manual dari user jika tersedia (pax_paid_input), jika tidak, gunakan hasil hitungan
            // Kita menggunakan 'nta' yang dikirim dari hidden field (yang dihitung di JS view)
            $finalNTA = $data['nta']; 
            $finalPaxPaid = $data['pax_paid_input'] ?? $paxPaidCalculated;

            // 4. Slug Unik
            $slug = Str::slug($data['name_package']);
            $originalSlug = $slug;
            $count = 1;
            while (Package::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            // 5. Simpan Data
            Package::create([
                'name_package' => $data['name_package'],
                'slug' => $slug,
                'description' => $data['description'],
                'images' => $uploadedImagePaths,

                // 🔄 Penyesuaian Kolom
                'nta' => $finalNTA, // Nett Total Package Price
                'pax_paid' => round($finalPaxPaid, 2), // Harga Jual Per Pax (Final)
                'tax_rate' => $data['tax_rate'] ?? 0,
                'start_publish' => $data['start_publish'],
                'end_publish' => $data['end_publish'] ?? null,
                'is_active' => $data['is_active'],
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
        // ... (Metode packagesUpdate tetap sama) ...
        // Ambil semua produk dan addon untuk opsi multi-select
        $products = Product::paginate(5);
        $addons = Addon::paginate(5);
        $vendorInfos = VendorInfo::with('vendor')->get(); 

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

        // Normalize the products data to ensure necessary keys exist
        $selectedProductsData = collect($selectedProductsData)->map(function ($product) {
            return [
                'id' => $product['id'] ?? '',
                'name' => $product['name'] ?? 'Unknown Product',
                'nta' => $product['nta'] ?? $product['price'] ?? 0, 
                'pax' => $product['pax'] ?? 1,
            ];
        })->toArray();

        // Normalize the addons data to ensure necessary keys exist
        $selectedAddonsData = collect($selectedAddonsData)->map(function ($addon) {
            return [
                'id' => $addon['id'] ?? '',
                'name' => $addon['name'] ?? $addon['addons'] ?? 'Unknown Addon',
                'nta' => $addon['nta'] ?? $addon['price'] ?? 0, 
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


    public function update(Request $request, Package $package)
    {
        // 1. Validasi Data
        $validator = Validator::make($request->all(), [
            'name_package' => 'required|string|max:255',
            'description' => 'nullable|string',
            'images' => 'nullable|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',

            // ✅ PAX PAID (Harga Jual Per Pax Manual)
            'pax_paid_input' => 'nullable|numeric|min:0',

            // ❌ DIHAPUS: 'discount_percentage'
            // 'discount_percentage' => 'required|integer|min:0|max:100',

            'nta' => 'required|numeric|min:0', // ✅ VALIDASI NTA (Nett Total Package Price) dari hidden input
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after_or_equal:start_publish',
            'is_active' => 'required|boolean',

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
        $totalNTA = 0; // Nett Total Package Price
        $totalPax = 0;
        
        DB::beginTransaction();
        try {
            // --- 2. Proses Produk dan Hitung Total NTA ---
            $productsData = [];
            $selectedProducts = Product::whereIn('id', $productsIds)->get();
            foreach ($selectedProducts as $product) {
                $pax = $data['product_pax'][$product->id] ?? 1;
                $productPrice = $product->nta ?? $product->basic_price ?? 0;

                $subTotal = $productPrice * $pax;
                $totalNTA += $subTotal;
                $totalPax += $pax;

                $productsData[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'nta' => $productPrice, 
                    'pax' => (int) $pax,
                    'sub_total' => $subTotal,
                ];
            }

            // --- 3. Proses Addon dan Tambahkan ke Total NTA ---
            $addonsData = [];
            $selectedAddons = Addon::whereIn('id', $addonsIds)->get();
            foreach ($selectedAddons as $addon) {
                $pax = $data['addon_pax'][$addon->id] ?? 1;
                $addonPrice = $addon->nta ?? $addon->basic_price ?? 0;

                $subTotal = $addonPrice * $pax;
                $totalNTA += $subTotal;
                // Asumsi: Addon tidak menambah total pax paket

                $addonsData[] = [
                    'id' => $addon->id,
                    'name' => $addon->addons,
                    'nta' => $addonPrice, 
                    'pax' => (int) $pax,
                    'sub_total' => $subTotal,
                ];
            }

            // 💡 Perhitungan Harga Pax Paid (Harga Jual Per Pax)
            // Karena diskon dihapus, Total Harga Jual = Total NTA
            $totalPricePublishCalculated = $totalNTA;
            
            // Harga Jual Per Pax = Total NTA / Total Pax
            $paxPaidCalculated = ($totalPax > 0) 
                                ? $totalPricePublishCalculated / $totalPax
                                : $totalPricePublishCalculated; 
            
            // Gunakan input manual dari user jika tersedia (pax_paid_input), jika tidak, gunakan hasil hitungan
            // Kita menggunakan 'nta' yang dikirim dari hidden field (yang dihitung di JS view)
            $finalNTA = $data['nta']; 
            $finalPaxPaid = $data['pax_paid_input'] ?? $paxPaidCalculated;

            // --- 5. Persiapkan Data Update ---
            $packageData = [
                'name_package' => $data['name_package'],
                'description' => $data['description'],

                // 🔄 Penyesuaian Kolom
                'nta' => $finalNTA, // Nett Total Package Price
                'pax_paid' => round($finalPaxPaid, 2), // Harga Jual Per Pax (Final)
                'tax_rate' => $data['tax_rate'] ?? 0,
                'start_publish' => $data['start_publish'],
                'end_publish' => $data['end_publish'] ?? null,
                'is_active' => $data['is_active'],
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

            // --- 7. Buat atau Perbarui Slug ---
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
    
    // ... (Metode lainnya tetap sama) ...
    
    public function analytics()
    {
        // Analytics data
        $analytics = [
            'totalBookings' => Booking::count(),
            'totalRevenue' => Booking::sum('total_price'),
            'totalUsers' => User::count(),
            // Tambahkan use App\Models\Category; di atas jika Anda ingin menggunakan ini
            // 'totalCategories' => \App\Models\Category::count(), 
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