<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Package;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Product;
use App\Models\Addon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Exception; // Tambahkan ini jika belum ada

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
        $products = Product::all();
        $addons = Addon::all();
        // Hanya mengirim produk dan addon yang diperlukan
        return view('admin.packages.create', compact('products', 'addons')); 
    }

    /**
     * Menyimpan paket baru dengan multi-select produk dan addon.
     */
    public function store(Request $request)
    {
        // 1. Validasi Data
        $validator = Validator::make($request->all(), [
            'name_package' => 'required|string|max:255',
            'products' => 'required|array|min:1', // Harus ada minimal 1 produk
            'products.*' => 'uuid|exists:products,id', // Setiap item harus UUID valid
            'addons' => 'nullable|array',
            'addons.*' => 'uuid|exists:addons,id',
            'product_pax' => 'required|array', // Menerima array PAX untuk produk
            'addon_pax' => 'nullable|array', // Menerima array PAX untuk addon
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price_publish' => 'required|numeric|min:0', // Harga yang diinput/override
            'price_real' => 'required|numeric|min:0', // Harga akumulasi dari JS
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after_or_equal:start_publish',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $productsIds = $data['products'];
        $addonsIds = $data['addons'] ?? [];
        
        // 2. Format Products dan Addons menjadi JSON
        // Ambil data produk yang dipilih, beserta harga dan pax-nya
        $selectedProducts = Product::whereIn('id', $productsIds)
            ->get()
            ->map(function ($product) use ($request) {
                $pax = $request->input("product_pax.{$product->id}") ?? ($product->pax ?? 1);
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'pax' => (int) $pax,
                    'sub_total' => $product->price * $pax,
                ];
            })->toArray();
        
        // Ambil data addons yang dipilih
        $selectedAddons = Addon::whereIn('id', $addonsIds)
            ->get()
            ->map(function ($addon) use ($request) {
                $pax = $request->input("addon_pax.{$addon->id}") ?? ($addon->pax ?? 1);
                return [
                    'id' => $addon->id,
                    'name' => $addon->addons,
                    'price' => $addon->price,
                    'pax' => (int) $pax,
                    'sub_total' => $addon->price * $pax,
                ];
            })->toArray();

        // 3. Persiapkan data untuk disimpan
        $packageData = [
            'name_package' => $data['name_package'],
            'description' => $data['description'],
            'price_publish' => $data['price_publish'],
            'price_real' => $data['price_real'],
            'start_publish' => $data['start_publish'],
            'end_publish' => $data['end_publish'] ?? null,
            'is_active' => $data['is_active'],
            // Simpan data Products dan Addons sebagai JSON string
            'products_data' => json_encode($selectedProducts),
            'addons_data' => json_encode($selectedAddons),
        ];

        // 4. Tangani Pengunggahan Gambar
        $uploadedPath = null;
        if ($request->hasFile('image')) {
            try {
                $uploadedPath = $request->file('image')->store('packages', 'public');
                $packageData['image'] = $uploadedPath;
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal mengunggah gambar: ' . $e->getMessage())->withInput();
            }
        }

        // 5. Buat Slug
        $slug = Str::slug($packageData['name_package']);
        $originalSlug = $slug;
        $count = 1;
        while (Package::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        $packageData['slug'] = $slug;

        try {
            // 6. Simpan ke Database
            Package::create($packageData);
            return redirect()->route('admin.packages')->with('success', 'Paket berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Jika penyimpanan gagal, hapus gambar yang sudah terunggah
            if ($uploadedPath) {
                Storage::disk('public')->delete($uploadedPath);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan paket: ' . $e->getMessage())->withInput();
        }
    }

    public function packagesUpdate(Package $package)
    {
        // Ambil semua produk dan addon untuk opsi multi-select
        $products = Product::all();
        $addons = Addon::all();

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

        // Kirim data yang diperlukan ke view edit
        return view('admin.packages.edit', compact(
            'package',
            'products',
            'addons',
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
            'products' => 'required|array|min:1', 
            'products.*' => 'uuid|exists:products,id', 
            'addons' => 'nullable|array',
            'addons.*' => 'uuid|exists:addons,id',
            'product_pax' => 'required|array', 
            'addon_pax' => 'nullable|array', 
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'price_publish' => 'required|numeric|min:0',
            'price_real' => 'required|numeric|min:0', 
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after_or_equal:start_publish',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $productsIds = $data['products'];
        $addonsIds = $data['addons'] ?? []; // Default array kosong
        
        // 2. Format Products dan Addons (Data PAX)
        
        // Ambil data produk yang dipilih, beserta harga dan pax-nya
        $selectedProducts = Product::whereIn('id', $productsIds)
            ->get()
            ->map(function ($product) use ($request) {
                // Ambil PAX dari input. Default 1 jika tidak ada.
                $pax = $request->input("product_pax.{$product->id}") ?? 1;
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'pax' => (int) $pax,
                    'sub_total' => $product->price * $pax,
                ];
            })->toArray();
        
        // Ambil data addons yang dipilih
        $selectedAddons = Addon::whereIn('id', $addonsIds)
            ->get()
            ->map(function ($addon) use ($request) {
                // Ambil PAX dari input. Default 1 jika tidak ada.
                $pax = $request->input("addon_pax.{$addon->id}") ?? 1;
                return [
                    'id' => $addon->id,
                    'name' => $addon->addons,
                    'price' => $addon->price,
                    'pax' => (int) $pax,
                    'sub_total' => $addon->price * $pax,
                ];
            })->toArray();

        // 3. Persiapkan data untuk update
        $packageData = [
            'name_package' => $data['name_package'],
            'description' => $data['description'],
            'price_publish' => $data['price_publish'],
            'price_real' => $data['price_real'],
            'start_publish' => $data['start_publish'],
            'end_publish' => $data['end_publish'] ?? null,
            'is_active' => $data['is_active'],
            
            // HAPUS json_encode(). Kirim array PHP karena Model Cast akan meng-encode-nya.
            'products_data' => $selectedProducts,
            'addons_data' => $selectedAddons,
        ];

        // 4. Tangani Pengunggahan Gambar
        if ($request->hasFile('image')) {
            try {
                // Hapus gambar lama jika ada
                if ($package->image) {
                    Storage::disk('public')->delete($package->image);
                }
                // Unggah gambar baru
                $uploadedPath = $request->file('image')->store('packages', 'public');
                $packageData['image'] = $uploadedPath;
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal mengunggah gambar: ' . $e->getMessage())->withInput();
            }
        }

        // 5. Buat atau perbarui Slug
        $slug = Str::slug($packageData['name_package']);
        $originalSlug = $slug;
        $count = 1;
        // Cek slug yang sama, KECUALI paket yang sedang diedit
        while (Package::where('slug', $slug)->where('id', '!=', $package->id)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        $packageData['slug'] = $slug;

        try {
            // 6. Simpan Pembaruan ke Database
            $package->update($packageData);

            return redirect()->route('admin.packages')->with('success', 'Paket berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui paket: ' . $e->getMessage())->withInput();
        }
    }

    
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

    public function settings()
    {
        return view('admin.settings');
    }
}