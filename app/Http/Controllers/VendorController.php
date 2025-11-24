<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\VendorInfo;
use App\Models\Booking;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Addon; 
use App\Models\Category;

class VendorController extends Controller
{
    public function profile()
    {
        $vendor = Auth::guard('vendor')->user();
        $vendorInfo = VendorInfo::where('id_vendor', $vendor->id)->first();

        return view('vendor.profile', compact('vendor', 'vendorInfo'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendor,email,' . Auth::guard('vendor')->id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        /** @var \App\Models\Vendor $vendor */
        $vendor = Auth::guard('vendor')->user();
        $vendor->update($request->only(['name', 'email']));

        VendorInfo::updateOrCreate(
            ['id_vendor' => $vendor->id],
            $request->only(['phone', 'address', 'description'])
        );

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function index()
    {
        $vendors = Vendor::with('vendorInfo')->paginate(10);

        return view('super_admin.vendors', compact('vendors'));
    }

    public function create()
    {
        return view('super_admin.vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendor,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $vendor = Vendor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        VendorInfo::create([
            'id_vendor' => $vendor->id,
            'id_city' => null,
            'phone' => $request->phone,
            'address' => $request->address,
            'description' => $request->description,
            'desc' => $request->description,
            'coordinate_latitude' => null,
            'coordinate_longitude' => null,
            'landmark_description' => null,
        ]);

        return redirect()->route('super_admin.vendors')->with('success', 'Vendor created successfully');
    }

    public function edit($id)
    {
        $vendor = Vendor::with('vendorInfo')->findOrFail($id);

        return view('super_admin.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendor,email,' . $vendor->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $vendor->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->is_active,
        ]);

        if ($request->filled('password')) {
            $vendor->update(['password' => bcrypt($request->password)]);
        }

        VendorInfo::updateOrCreate(
            ['id_vendor' => $vendor->id],
            $request->only(['phone', 'address', 'description'])
        );

        return redirect()->route('super_admin.vendors')->with('success', 'Vendor updated successfully');
    }

    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();

        return redirect()->route('super_admin.vendors')->with('success', 'Vendor deleted successfully');
    }

    public function bookings()
    {
        $vendor = Auth::guard('vendor')->user();
        $bookings = Booking::where('vendor_id', $vendor->id)->with('user')->paginate(10);

        return view('vendor.bookings', compact('bookings'));
    }

    public function services()
    {
        $vendor = Auth::guard('vendor')->user();
        // Assuming services are related to vendor
        $services = []; // Replace with actual service model query

        return view('vendor.services', compact('services'));
    }

    public function storeService(Request $request)
    {
        // Implement service creation logic
        return redirect()->back()->with('success', 'Service created successfully');
    }

    public function updateService(Request $request, $serviceId)
    {
        // Implement service update logic
        return redirect()->back()->with('success', 'Service updated successfully');
    }

    public function deleteService($serviceId)
    {
        // Implement service deletion logic
        return redirect()->back()->with('success', 'Service deleted successfully');
    }

    public function pricing()
    {
        $vendor = Auth::guard('vendor')->user();
        // Assuming pricing data
        $pricing = []; // Replace with actual pricing model query

        return view('vendor.pricing', compact('pricing'));
    }

    public function updatePricing(Request $request)
    {
        // Implement pricing update logic
        return redirect()->back()->with('success', 'Pricing updated successfully');
    }

    public function availability()
    {
        $vendor = Auth::guard('vendor')->user();
        // Assuming availability data
        $availability = []; // Replace with actual availability model query

        return view('vendor.availability', compact('availability'));
    }

    public function updateAvailability(Request $request)
    {
        // Implement availability update logic
        return redirect()->back()->with('success', 'Availability updated successfully');
    }

    public function analytics()
    {
        $vendor = Auth::guard('vendor')->user();
        // Assuming analytics data
        $analytics = [
            'totalBookings' => Booking::where('vendor_id', $vendor->id)->count(),
            'totalRevenue' => Booking::where('vendor_id', $vendor->id)->sum('total_price'),
            'activeServices' => 0, // Replace with actual count
            'averageRating' => 0, // Replace with actual calculation
        ];

        return view('vendor.analytics', compact('analytics'));
    }

    // Vendor-specific methods for super admin
    public function vendorProducts($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $products = \App\Models\Product::where('id_vendor', $vendorId)->with('category')->paginate(10);

        return view('super_admin.vendors.products', compact('vendor', 'products'));
    }

    public function vendorAddons($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $addons = \App\Models\Addon::where('id_vendor', $vendorId)->paginate(10);

        return view('super_admin.vendors.addons', compact('vendor', 'addons'));
    }

    // DISESUAIKAN: Menambahkan kolom diskon
    public function vendorAddonDetails($vendorId, $addonId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $addon = \App\Models\Addon::where('id_vendor', $vendorId)->where('id', $addonId)->firstOrFail();

        return response()->json([
            'addons' => $addon->addons,
            'basic_price' => $addon->basic_price, 
            'nta' => $addon->nta,           
            'tax_rate' => $addon->tax_rate,      
            'status' => $addon->status,
            'publish' => $addon->publish,
            'desc' => $addon->desc,
            // --- KOLOM DISKON BARU ---
            'discount_type' => $addon->discount_type,
            'discount_value' => $addon->discount_value,
            'discount_expires_at' => $addon->discount_expires_at,
            // --------------------------
        ]);
    }

    public function vendorProductDetail($vendorId, $productId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $product = \App\Models\Product::where('id_vendor', $vendorId)->where('id', $productId)->with('category')->firstOrFail();

        return view('super_admin.vendors.product_detail', compact('vendor', 'product'));
    }

    public function vendorAddonDetail($vendorId, $addonId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $addon = \App\Models\Addon::where('id_vendor', $vendorId)->where('id', $addonId)->firstOrFail();

        return view('super_admin.vendors.addon_detail', compact('vendor', 'addon'));
    }

    public function vendorProfile($vendorId)
    {
        $vendor = Vendor::with('vendorInfo')->findOrFail($vendorId);

        return view('super_admin.vendors.profile', compact('vendor'));
    }

    public function vendorTransactionProducts($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        // Get transactions for vendor's products - using Detail_Booking which relates to products
        $transactions = \App\Models\Detail_Booking::whereHas('product', function($query) use ($vendorId) {
            $query->where('id_vendor', $vendorId);
        })->with(['booking.user', 'product'])->paginate(10);

        return view('super_admin.vendors.transaction_products', compact('vendor', 'transactions'));
    }

    public function vendorTransactionAddons($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        // Get transactions for vendor's addons
        $transactions = \App\Models\BookAddon::whereHas('addon', function($query) use ($vendorId) {
            $query->where('id_vendor', $vendorId);
        })->with(['user', 'addon'])->paginate(10);

        return view('super_admin.vendors.transaction_addons', compact('vendor', 'transactions'));
    }

    // Vendor-specific methods for vendor dashboard
    public function vendorProductsDashboard()
    {
        $vendor = Auth::guard('vendor')->user() ?? Auth::guard('super_admin')->user();
        $products = \App\Models\Product::where('id_vendor', $vendor->id)->with('category')->paginate(10);

        return view('vendor.products', compact('products'));
    }

    public function vendorAddonsDashboard()
    {
        $vendor = Auth::guard('vendor')->user() ?? Auth::guard('super_admin')->user();
        $addons = \App\Models\Addon::where('id_vendor', $vendor->id)->paginate(10);

        return view('vendor.addons', compact('addons'));
    }

    public function vendorTransactionProductsDashboard()
    {
        $vendor = Auth::guard('vendor')->user();
        // Assuming transactions are related to vendor products
        $transactions = []; // Replace with actual transaction model query

        return view('vendor.transaction_products', compact('transactions'));
    }

    public function vendorTransactionAddonsDashboard()
    {
        $vendor = Auth::guard('vendor')->user();
        // Assuming transactions are related to vendor addons
        $transactions = []; // Replace with actual transaction model query

        return view('vendor.transaction_addons', compact('transactions'));
    }

    // Product CRUD methods (sudah disesuaikan di code asli Anda)
    public function createProduct()
    {
        $categories = \App\Models\Category::all();
        return view('vendor.products.create', compact('categories'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'id_category' => 'required|exists:categories,id',
            'basic_price' => 'required|numeric|min:0', 
            'nta' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100', 
            
            // --- VALIDASI DISKON BARU (PRODUCT) ---
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_expires_at' => 'nullable|date|after:today',
            // ---------------------------
            
            'images' => 'nullable|array|max:5', 
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', 
            'description' => 'nullable|string',
            'pax' => 'required|integer|min:1',
            'jumlah' => 'required|integer|min:1',
            'max_adults' => 'nullable|integer|min:0',
            'max_children' => 'nullable|integer|min:0',
            'status' => 'nullable|in:available,unavailable,draft',
        ]);

        $vendor = Auth::guard('vendor')->user();

        $data = $request->except('images'); 
        $data['id_vendor'] = $vendor->id;
        $imagePaths = [];

        // PROSES UPLOAD MULTIPLE IMAGES
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('products', 'public');
            }
        }
        
        $data['images'] = $imagePaths; 
        $data['tax_rate'] = $request->input('tax_rate', 0.00);
        
        if (!$request->filled('discount_expires_at')) {
            $data['discount_expires_at'] = null;
        }

        Product::create($data);

        return redirect()->route('vendor.products')->with('success', 'Product created successfully');
    }

    public function editProduct($id)
    {
        $vendor = Auth::guard('vendor')->user();
        $product = Product::where('id', $id)->where('id_vendor', $vendor->id)->firstOrFail();
        $categories = \App\Models\Category::all();

        return view('vendor.products.edit', compact('product', 'categories'));
    }

    public function updateProduct(Request $request, $id)
    {
        $vendor = Auth::guard('vendor')->user();
        $product = Product::where('id', $id)->where('id_vendor', $vendor->id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'id_category' => 'required|exists:categories,id',
            'basic_price' => 'required|numeric|min:0', 
            'nta' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100', 
            
            // --- VALIDASI DISKON BARU (PRODUCT) ---
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_expires_at' => 'nullable|date|after:today',
            // ---------------------------
            
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer|min:0',
            'description' => 'nullable|string',
            'pax' => 'required|integer|min:1',
            'jumlah' => 'required|integer|min:1',
            'max_adults' => 'nullable|integer|min:0',
            'max_children' => 'nullable|integer|min:0',
            'status' => 'nullable|in:available,unavailable,draft',
        ]);

        $data = $request->except(['images', 'remove_images']);
        $imagePaths = $product->images ?? [];

        // Handle image removal (logic sudah benar)
        if ($request->has('remove_images') && is_array($request->remove_images)) {
            $imagesToRemove = $request->remove_images;
            $newImagePaths = [];

            foreach ($imagePaths as $index => $imagePath) {
                if (!in_array($index, $imagesToRemove)) {
                    $newImagePaths[] = $imagePath;
                } else {
                    if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                         Storage::disk('public')->delete($imagePath);
                    }
                }
            }
            $imagePaths = $newImagePaths;
        }

        // Handle new image uploads (logic sudah benar)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('products', 'public');
            }
        }

        $data['images'] = $imagePaths;
        $data['tax_rate'] = $request->input('tax_rate', $product->tax_rate ?? 0.00);
        
        if (!$request->filled('discount_expires_at')) {
            $data['discount_expires_at'] = null;
        }

        $product->update($data);

        return redirect()->route('vendor.products')->with('success', 'Product updated successfully');
    }

    public function destroyProduct($id)
    {
        $vendor = Auth::guard('vendor')->user();
        $product = \App\Models\Product::where('id', $id)->where('id_vendor', $vendor->id)->firstOrFail();

        // Delete images if exist (multiple images)
        if (is_array($product->images)) {
            foreach ($product->images as $imagePath) {
                if ($imagePath && \Storage::disk('public')->exists($imagePath)) {
                    \Storage::disk('public')->delete($imagePath);
                }
            }
        }

        $product->delete();

        return redirect()->route('vendor.products')->with('success', 'Product deleted successfully (Soft Deleted)');
    }

    // Addon CRUD methods
    public function createAddon()
    {
        return view('vendor.addons.create');
    }

    /**
     * Menyimpan addon baru. (DISESUAIKAN UNTUK DISKON)
     */
    public function storeAddon(Request $request)
    {
        $rules = [
            'addons'        => 'required|string|max:255',
            'basic_price'   => 'required|numeric|min:0', 
            'nta'           => 'required|numeric|min:0', 
            'tax_rate'      => 'nullable|numeric|min:0|max:100', 
            'desc'          => 'nullable|string|max:500',
            'status'        => 'sometimes|string|in:available,unavailable,draft',
            'publish'       => 'sometimes|boolean',
            'pax'           => 'sometimes|integer|min:1',
            
            // --- VALIDASI DISKON BARU (ADDON) ---
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_expires_at' => 'nullable|date|after:today',
            // -------------------------------------

            'images'        => 'nullable|array|max:5',
            'images.*'      => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        if (Auth::guard('super_admin')->check()) {
            $rules['id_vendor'] = 'required|uuid|exists:vendor,id';
        }

        $request->validate($rules);

        // Ambil semua data yang relevan, termasuk diskon
        $data = $request->only([
            'addons', 'basic_price', 'nta', 'tax_rate', 'desc', 'status', 'publish', 'pax',
            'discount_type', 'discount_value', 'discount_expires_at' // KOLOM DISKON BARU
        ]);

        // Set id_vendor based on user type
        if (Auth::guard('super_admin')->check()) {
            $data['id_vendor'] = $request->id_vendor;
        } else {
            $vendor = Auth::guard('vendor')->user();
            $data['id_vendor'] = $vendor->id;
        }
        
        $imagePaths = [];
        $uploadedPaths = []; 

        DB::beginTransaction();
        try {
            // PROSES UPLOAD FILE MULTIPLE
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('addons', 'public');
                    $uploadedPaths[] = $path; 
                    $imagePaths[] = $path;
                }
            } 
            
            $data['images'] = $imagePaths;
            $data['tax_rate'] = $request->input('tax_rate', 0.00); 

            // Handle tanggal kadaluarsa diskon yang mungkin kosong
            if (!$request->filled('discount_expires_at')) {
                $data['discount_expires_at'] = null;
            }

            Addon::create($data);

            DB::commit();

            return redirect()->route('vendor.addons')->with('success', 'Addon created successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($uploadedPaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return redirect()->back()->withInput()->with('error', 'Gagal membuat addon: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui addon tertentu (Web/Blade View). (DISESUAIKAN UNTUK DISKON)
     */
    public function updateAddon(Request $request, $id)
    {
        $rules = [
            'addons'        => 'sometimes|required|string|max:255',
            'basic_price'   => 'sometimes|required|numeric|min:0', 
            'nta'           => 'sometimes|required|numeric|min:0', 
            'tax_rate'      => 'nullable|numeric|min:0|max:100', 
            'desc'          => 'nullable|string|max:500',
            'status'        => 'sometimes|string|in:available,unavailable,draft',
            'publish'       => 'sometimes|boolean',
            'pax'           => 'sometimes|integer|min:1',
            
            // --- VALIDASI DISKON BARU (ADDON) ---
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_expires_at' => 'nullable|date|after:today',
            // -------------------------------------

            'images'        => 'nullable|array|max:5',
            'images.*'      => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer|min:0',
        ];

        if (Auth::guard('super_admin')->check()) {
            $rules['id_vendor'] = 'required|uuid|exists:vendor,id';
        }

        $request->validate($rules);

        // Set vendor based on user type
        if (Auth::guard('super_admin')->check()) {
            $addon = Addon::findOrFail($id);
        } else {
            $vendor = Auth::guard('vendor')->user();
            $addon = Addon::where('id', $id)->where('id_vendor', $vendor->id)->first();
        }

        if (!$addon) {
            return redirect()->route('vendor.addons')->with('error', 'Addon tidak ditemukan atau akses ditolak');
        }

        // Ambil semua data yang relevan, termasuk diskon
        $data = $request->only([
            'addons', 'basic_price', 'nta', 'tax_rate', 'desc', 'status', 'publish', 'pax',
            'discount_type', 'discount_value', 'discount_expires_at' // KOLOM DISKON BARU
        ]);

        if (Auth::guard('super_admin')->check() && $request->filled('id_vendor')) {
            $data['id_vendor'] = $request->id_vendor;
        }

        $imagePaths = $addon->images ?? [];
        $uploadedPaths = []; 

        DB::beginTransaction();
        try {
            // Handle image removal (logic sudah benar)
            if ($request->has('remove_images') && is_array($request->remove_images)) {
                 $imagesToRemove = $request->remove_images;
                 $newImagePaths = [];
                 foreach ($imagePaths as $index => $imagePath) {
                     if (!in_array($index, $imagesToRemove)) {
                         $newImagePaths[] = $imagePath;
                     } else {
                         if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                              Storage::disk('public')->delete($imagePath);
                         }
                     }
                 }
                 $imagePaths = $newImagePaths;
            }

            // PROSES UPLOAD MULTIPLE IMAGES
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('addons', 'public');
                    $uploadedPaths[] = $path; 
                    $imagePaths[] = $path;
                }
            }

            $data['images'] = $imagePaths;
            $data['tax_rate'] = $request->input('tax_rate', $addon->tax_rate ?? 0.00); 
            
            // Handle tanggal kadaluarsa diskon yang mungkin kosong
            if (!$request->filled('discount_expires_at')) {
                $data['discount_expires_at'] = null;
            }

            $addon->update($data);

            DB::commit();

            return redirect()->route('vendor.addons')->with('success', 'Addon updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($uploadedPaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui addon: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form edit addon tertentu.
     */
    public function editAddon($id)
    {
        // Allow super_admin to edit any addon, vendor only their own
        if (Auth::guard('super_admin')->check()) {
            $addon = Addon::findOrFail($id);
        } else {
            $vendor = Auth::guard('vendor')->user();
            $addon = Addon::where('id', $id)->where('id_vendor', $vendor->id)->firstOrFail();
        }

        return view('vendor.addons.edit', compact('addon'));
    }

    /**
     * Menghapus addon tertentu (Soft Delete + Hapus File Fisik).
     */
    public function destroyAddon($id)
    {
        // Allow super_admin to delete any addon, vendor only their own
        if (Auth::guard('super_admin')->check()) {
            $addon = Addon::findOrFail($id);
        } else {
            $vendor = Auth::guard('vendor')->user();
            $addon = Addon::where('id', $id)->where('id_vendor', $vendor->id)->firstOrFail();
        }

        // Cek apakah ada file gambar (multiple images), dan hapus dari storage sebelum soft delete
        if (is_array($addon->images)) {
            foreach ($addon->images as $imagePath) {
                if ($imagePath && !filter_var($imagePath, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
            }
        }

        $addon->delete();

        return redirect()->route('vendor.addons')->with('success', 'Addon deleted successfully (Soft Deleted)');
    }
}