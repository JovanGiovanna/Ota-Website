<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\VendorInfo;
use App\Models\Booking;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Addon; 
use App\Models\Category;
use App\Models\BookProduct;
use App\Models\BookPackage;
use App\Models\Review;
use App\Helpers\SweetAlert;

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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendor,email,' . Auth::guard('vendor')->id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if (function_exists('alert')) {
                alert()->error('Validation Failed', 'Mohon periksa kembali form Anda');
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            /** @var \App\Models\Vendor $vendor */
            $vendor = Auth::guard('vendor')->user();
            $vendor->update($request->only(['name', 'email']));

            VendorInfo::updateOrCreate(
                ['id_vendor' => $vendor->id],
                $request->only(['phone', 'address', 'description'])
            );

            if (function_exists('alert')) {
                alert()->success('Success', 'Profil berhasil diperbarui');
            }
            return redirect()->back();
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Profil gagal diperbarui: ' . $e->getMessage());
            }
            return redirect()->back()
                ->withInput();
        }
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendor,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if (function_exists('alert')) {
                alert()->error('Validation Failed', 'Mohon periksa kembali form Anda');
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
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

            return SweetAlert::created('Vendor', route('super_admin.vendors'));
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Vendor gagal dibuat: ' . $e->getMessage());
            }
            return redirect()->back()
                ->withInput();
        }
    }

    public function edit($id)
    {
        $vendor = Vendor::with('vendorInfo')->findOrFail($id);

        return view('super_admin.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, $id)
    {
        $vendor = Vendor::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendor,email,' . $vendor->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            if (function_exists('alert')) {
                alert()->error('Validation Failed', 'Mohon periksa kembali form Anda');
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
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

            return SweetAlert::updated('Vendor', route('super_admin.vendors'));
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Vendor gagal diubah: ' . $e->getMessage());
            }
            return redirect()->back()
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            $vendor->delete();

            if (function_exists('alert')) {
                alert()->success('Success', 'Vendor "' . $vendor->name . '" berhasil dihapus');
            }
            return redirect()->route('super_admin.vendors');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Vendor gagal dihapus: ' . $e->getMessage());
            }
            return redirect()->back();
        }
    }

    public function bookings()
    {
        $vendor = Auth::guard('vendor')->user();
        $bookings = Booking::where('vendor_id', $vendor->id)->with('user')->orderBy('created_at', 'desc')->paginate(10);

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
        $products = \App\Models\Product::where('id_vendor', $vendorId)->with('category')->orderBy('created_at', 'desc')->paginate(10);

        return view('super_admin.vendors.products', compact('vendor', 'products'));
    }

    public function vendorAddons($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $addons = \App\Models\Addon::where('id_vendor', $vendorId)->orderBy('created_at', 'desc')->paginate(10);

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

    // Edit Product (Super Admin)
    public function editProductAdmin($vendorId, $productId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $product = Product::where('id_vendor', $vendorId)->findOrFail($productId);
        $categories = Category::all();

        return view('super_admin.vendors.product_edit', compact('vendor', 'product', 'categories'));
    }

    // Update Product (Super Admin)
    public function updateProductAdmin(Request $request, $vendorId, $productId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $product = Product::where('id_vendor', $vendorId)->findOrFail($productId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'id_category' => 'required|exists:categories,id',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'basic_price' => 'required|numeric|min:0',
            'nta' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_expires_at' => 'nullable|date',
            'status' => 'required|in:active,inactive'
        ]);

        $product->update($validated);

        return redirect()->route('super_admin.vendors.products', $vendor->id)
                       ->with('success', 'Product updated successfully');
    }

    // Delete Product (Super Admin)
    public function deleteProductAdmin($vendorId, $productId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $product = Product::where('id_vendor', $vendorId)->findOrFail($productId);
        
        // Delete images from storage
        if (is_array($product->images)) {
            foreach ($product->images as $image) {
                if ($image && Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
        }

        $product->delete();

        return redirect()->route('super_admin.vendors.products', $vendor->id)
                       ->with('success', 'Product deleted successfully');
    }

    // Edit Addon (Super Admin)
    public function editAddonAdmin($vendorId, $addonId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $addon = Addon::where('id_vendor', $vendorId)->findOrFail($addonId);

        return view('super_admin.vendors.addon_edit', compact('vendor', 'addon'));
    }

    // Update Addon (Super Admin)
    public function updateAddonAdmin(Request $request, $vendorId, $addonId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $addon = Addon::where('id_vendor', $vendorId)->findOrFail($addonId);

        $validated = $request->validate([
            'addons' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'basic_price' => 'required|numeric|min:0',
            'nta' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_expires_at' => 'nullable|date',
            'status' => 'required|in:active,inactive',
            'desc' => 'nullable|string'
        ]);

        $addon->update($validated);

        return redirect()->route('super_admin.vendors.addons', $vendor->id)
                       ->with('success', 'Addon updated successfully');
    }

    // Delete Addon (Super Admin)
    public function deleteAddonAdmin($vendorId, $addonId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        $addon = Addon::where('id_vendor', $vendorId)->findOrFail($addonId);
        
        // Delete images from storage if exists
        if (is_array($addon->images)) {
            foreach ($addon->images as $image) {
                if ($image && Storage::disk('public')->exists($image)) {
                    Storage::disk('public')->delete($image);
                }
            }
        }

        $addon->delete();

        return redirect()->route('super_admin.vendors.addons', $vendor->id)
                       ->with('success', 'Addon deleted successfully');
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
        })->with(['booking.user', 'product'])->orderBy('created_at', 'desc')->paginate(10);

        return view('super_admin.vendors.transaction_products', compact('vendor', 'transactions'));
    }

    public function vendorTransactionAddons($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        // Get transactions for vendor's addons
        $transactions = \App\Models\BookAddon::whereHas('addon', function($query) use ($vendorId) {
            $query->where('id_vendor', $vendorId);
        })->with(['user', 'addon'])->orderBy('created_at', 'desc')->paginate(10);

        return view('super_admin.vendors.transaction_addons', compact('vendor', 'transactions'));
    }

    // Vendor-specific methods for vendor dashboard
    public function vendorProductsDashboard()
    {
        $vendor = Auth::guard('vendor')->user() ?? Auth::guard('super_admin')->user();
        $products = \App\Models\Product::where('id_vendor', $vendor->id)->with('category')->orderBy('created_at', 'desc')->paginate(10);

        return view('vendor.products', compact('products'));
    }

    /**
     * Legacy route support: redirect vendor stock page to main products page.
     */
    public function vendorStock()
    {
        return redirect()->route('vendor.products');
    }

    public function vendorAddonsDashboard()
    {
        $vendor = Auth::guard('vendor')->user() ?? Auth::guard('super_admin')->user();
        $addons = \App\Models\Addon::where('id_vendor', $vendor->id)->orderBy('created_at', 'desc')->paginate(10);

        return view('vendor.addons', compact('addons'));
    }

    public function vendorTransactionProductsDashboard()
    {
        $vendor = Auth::guard('vendor')->user();
        
        // Get all products owned by this vendor
        $vendorProductIds = \App\Models\Product::where('id_vendor', $vendor->id)
            ->pluck('id')
            ->toArray();
        
        // Get BookProduct transactions for vendor's products, ordered newest first
        $transactions = \App\Models\BookProduct::with(['booking', 'product', 'booking.user'])
            ->whereIn('id_product', $vendorProductIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('vendor.transaction_products', compact('transactions'));
    }

    public function vendorTransactionAddonsDashboard()
    {
        $vendor = Auth::guard('vendor')->user();
        
        // Get all addons owned by this vendor
        $vendorAddonIds = \App\Models\Addon::where('id_vendor', $vendor->id)
            ->pluck('id')
            ->toArray();
        
        // Get BookAddon transactions for vendor's addons, ordered newest first
        $transactions = \App\Models\BookAddon::with(['booking', 'addon', 'booking.user'])
            ->whereIn('id_addon', $vendorAddonIds)
            ->orderBy('created_at', 'desc')
            ->get();

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
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_expires_at' => 'nullable|date|after:today',            
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
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_expires_at' => 'nullable|date|after:today',            
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
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'discount_fixed' => 'nullable|numeric|min:0',
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
            'discount_type', 'discount_expires_at' // KOLOM DISKON BARU
        ]);

        // Set id_vendor based on user type
        if (Auth::guard('super_admin')->check()) {
            $data['id_vendor'] = $request->id_vendor;
        } else {
            $vendor = Auth::guard('vendor')->user();
            $data['id_vendor'] = $vendor->id;
        }

        // Map discount fields to discount_value based on discount_type
        if ($request->filled('discount_type')) {
            if ($request->discount_type === 'percentage' && $request->filled('discount_rate')) {
                $data['discount_value'] = $request->discount_rate;
            } elseif ($request->discount_type === 'fixed' && $request->filled('discount_fixed')) {
                $data['discount_value'] = $request->discount_fixed;
            } else {
                $data['discount_value'] = 0.00;
            }
        } else {
            $data['discount_value'] = 0.00;
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
            'upsell'        => 'nullable|numeric|min:0',
            'tax_rate'      => 'nullable|numeric|min:0|max:100',
            'desc'          => 'nullable|string|max:500',
            'status'        => 'sometimes|string|in:available,unavailable,draft',
            'publish'       => 'sometimes|boolean',
            'pax'           => 'sometimes|integer|min:1',

            // --- VALIDASI DISKON BARU (ADDON) ---
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'discount_fixed' => 'nullable|numeric|min:0',
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
            'addons', 'basic_price', 'nta', 'upsell', 'tax_rate', 'desc', 'status', 'publish', 'pax',
            'discount_type', 'discount_expires_at' // KOLOM DISKON BARU
        ]);

        if (Auth::guard('super_admin')->check() && $request->filled('id_vendor')) {
            $data['id_vendor'] = $request->id_vendor;
        }

        // Map discount fields to discount_value based on discount_type
        if ($request->filled('discount_type')) {
            if ($request->discount_type === 'percentage' && $request->filled('discount_rate')) {
                $data['discount_value'] = $request->discount_rate;
            } elseif ($request->discount_type === 'fixed' && $request->filled('discount_fixed')) {
                $data['discount_value'] = $request->discount_fixed;
            } else {
                $data['discount_value'] = 0.00;
            }
        } else {
            $data['discount_value'] = 0.00;
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
            
            // Calculate price fields
            $nta = $data['nta'] ?? $addon->nta;
            $upsell = $data['upsell'] ?? $addon->upsell;
            $totalPrice = $nta + $upsell;
            $discountAmount = $data['discount_amount'] ?? $addon->discount_amount ?? 0;
            
            $data['total_price_before_discount'] = $totalPrice;
            $data['final_price'] = $totalPrice - $discountAmount;

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

    /**
     * Dashboard method with analytics data
     * Tracks product bookings, package bookings containing products, and cancellations
     */
    public function dashboard()
    {
        $vendor = Auth::guard('vendor')->user();

        // Initialize arrays for monthly data (last 6 months)
        $monthlyBookings = [];
        $monthlyCancellations = [];
        $monthlyRevenue = [];
        $labels = [];

        // Generate data for last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M');
            $labels[] = $month;

            // Get booking IDs for direct products
            $bookingIds = DB::table('book_products')
                ->join('products', 'book_products.id_product', '=', 'products.id')
                ->where('products.id_vendor', $vendor->id)
                ->whereMonth('book_products.created_at', $date->month)
                ->whereYear('book_products.created_at', $date->year)
                ->distinct('id_book')
                ->pluck('id_book')
                ->toArray();

            // Get booking IDs for packages from this vendor
            $packageBookingIds = DB::table('book_packages')
                ->join('packages', 'book_packages.id_package', '=', 'packages.id')
                ->join('vendor_info', 'packages.id_vendor_info', '=', 'vendor_info.id')
                ->where('vendor_info.id_vendor', $vendor->id)
                ->whereMonth('book_packages.created_at', $date->month)
                ->whereYear('book_packages.created_at', $date->year)
                ->distinct('id_book')
                ->pluck('id_book')
                ->toArray();

            $allBookingIds = array_unique(array_merge($bookingIds, $packageBookingIds));

            $monthlyBookings[] = Booking::whereIn('id', $allBookingIds)
                ->where('status', '!=', 'cancelled')
                ->count();

            // Count cancellations
            $cancellationBookingIds = DB::table('book_products')
                ->join('products', 'book_products.id_product', '=', 'products.id')
                ->where('products.id_vendor', $vendor->id)
                ->whereMonth('book_products.created_at', $date->month)
                ->whereYear('book_products.created_at', $date->year)
                ->distinct('id_book')
                ->pluck('id_book')
                ->toArray();

            $packageCancellationIds = DB::table('book_packages')
                ->join('packages', 'book_packages.id_package', '=', 'packages.id')
                ->join('vendor_info', 'packages.id_vendor_info', '=', 'vendor_info.id')
                ->where('vendor_info.id_vendor', $vendor->id)
                ->whereMonth('book_packages.created_at', $date->month)
                ->whereYear('book_packages.created_at', $date->year)
                ->distinct('id_book')
                ->pluck('id_book')
                ->toArray();

            $allCancellationIds = array_unique(array_merge($cancellationBookingIds, $packageCancellationIds));

            $monthlyCancellations[] = Booking::whereIn('id', $allCancellationIds)
                ->where('status', 'cancelled')
                ->count();

            // Calculate revenue (sum of completed bookings for direct products)
            $revenue = DB::table('book_products')
                ->join('products', 'book_products.id_product', '=', 'products.id')
                ->where('products.id_vendor', $vendor->id)
                ->whereMonth('book_products.created_at', $date->month)
                ->whereYear('book_products.created_at', $date->year)
                ->where('book_products.revenue_applied', true)
                ->sum('book_products.total_price');

            $monthlyRevenue[] = (int)$revenue;
        }

        // Calculate total stats
        $totalBookingIds = DB::table('book_products')
            ->join('products', 'book_products.id_product', '=', 'products.id')
            ->where('products.id_vendor', $vendor->id)
            ->distinct('id_book')
            ->pluck('id_book')
            ->toArray();

        $packageTotalBookingIds = DB::table('book_packages')
            ->join('packages', 'book_packages.id_package', '=', 'packages.id')
            ->join('vendor_info', 'packages.id_vendor_info', '=', 'vendor_info.id')
            ->where('vendor_info.id_vendor', $vendor->id)
            ->distinct('id_book')
            ->pluck('id_book')
            ->toArray();

        $allTotalBookingIds = array_unique(array_merge($totalBookingIds, $packageTotalBookingIds));

        $totalBookings = Booking::whereIn('id', $allTotalBookingIds)
            ->where('status', '!=', 'cancelled')
            ->count();

        $totalCancellations = Booking::whereIn('id', $allTotalBookingIds)
            ->where('status', 'cancelled')
            ->count();

        $totalRevenue = DB::table('book_products')
            ->join('products', 'book_products.id_product', '=', 'products.id')
            ->where('products.id_vendor', $vendor->id)
            ->where('book_products.revenue_applied', true)
            ->sum('book_products.total_price');

        $activeServices = Product::where('id_vendor', $vendor->id)
            ->where('status', 'active')
            ->count();

        // Calculate average rating from reviews
        $averageRating = Review::whereHas('product', function ($query) use ($vendor) {
            $query->where('id_vendor', $vendor->id);
        })->avg('rating') ?? 0;

        return view('vendor.dashboard', [
            'totalBookings' => $totalBookings,
            'totalCancellations' => $totalCancellations,
            'totalRevenue' => $totalRevenue,
            'activeServices' => $activeServices,
            'averageRating' => round($averageRating, 1),
            'monthlyBookings' => json_encode($monthlyBookings),
            'monthlyCancellations' => json_encode($monthlyCancellations),
            'monthlyRevenue' => json_encode($monthlyRevenue),
            'labels' => json_encode($labels),
        ]);
    }

    /**
     * Transactions report for vendor (filterable by product name and date range)
     */
    public function transactionReport(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        // Query for direct product bookings
        $productQuery = DB::table('book_products')
            ->join('bookings', 'book_products.id_book', '=', 'bookings.id')
            ->join('products', 'book_products.id_product', '=', 'products.id')
            ->leftJoin('users', 'bookings.id_user', '=', 'users.id')
            ->where('products.id_vendor', $vendor->id)
            ->select(
                'book_products.id as book_product_id',
                'bookings.id as booking_id',
                'bookings.booking_code as booking_code',
                DB::raw("'Product' as type"),
                'products.name as item_name',
                'users.name as customer_name',
                'bookings.created_at as transaction_date',
                'book_products.total_price as price',
                'book_products.amount as quantity',
                'bookings.status as booking_status'
            );

        // Query for package bookings from this vendor
        $packageQuery = DB::table('book_packages')
            ->join('bookings', 'book_packages.id_book', '=', 'bookings.id')
            ->join('packages', 'book_packages.id_package', '=', 'packages.id')
            ->join('vendor_info', 'packages.id_vendor_info', '=', 'vendor_info.id')
            ->leftJoin('users', 'bookings.id_user', '=', 'users.id')
            ->where('vendor_info.id_vendor', $vendor->id)
            ->select(
                'book_packages.id as book_product_id',
                'bookings.id as booking_id',
                'bookings.booking_code as booking_code',
                DB::raw("'Package' as type"),
                'packages.name_package as item_name',
                'users.name as customer_name',
                'bookings.created_at as transaction_date',
                'book_packages.total_price as price',
                DB::raw("1 as quantity"),
                'bookings.status as booking_status'
            );

        // Combine queries
        $combinedQuery = $productQuery->union($packageQuery);

        // Apply filters to the combined query
        $filteredQuery = DB::table(DB::raw("({$combinedQuery->toSql()}) as combined"))
            ->mergeBindings($combinedQuery);

        if ($request->filled('product_name')) {
            $filteredQuery->where('item_name', 'like', '%' . $request->input('product_name') . '%');
        }

        if ($request->filled('date_from')) {
            $filteredQuery->whereDate('transaction_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $filteredQuery->whereDate('transaction_date', '<=', $request->input('date_to'));
        }

        $filteredQuery->orderBy('transaction_date', 'desc');

        $transactions = $filteredQuery->paginate(20)->withQueryString();

        return view('vendor.transactions_report', [
            'transactions' => $transactions,
            'product_name' => $request->input('product_name'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ]);
    }

    /**
     * Export transactions report as CSV or PDF
     */
    public function transactionReportExport(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();

        // Query for direct product bookings
        $productQuery = DB::table('book_products')
            ->join('bookings', 'book_products.id_book', '=', 'bookings.id')
            ->join('products', 'book_products.id_product', '=', 'products.id')
            ->leftJoin('users', 'bookings.id_user', '=', 'users.id')
            ->where('products.id_vendor', $vendor->id)
            ->select(
                'bookings.id as booking_id',
                'bookings.booking_code as booking_code',
                DB::raw("'Product' as type"),
                'products.name as item_name',
                'users.name as customer_name',
                'bookings.created_at as transaction_date',
                'book_products.total_price as price',
                'book_products.amount as quantity',
                'bookings.status as booking_status'
            );

        // Query for package bookings containing vendor's products
        $packageQuery = DB::table('book_packages')
            ->join('bookings', 'book_packages.id_book', '=', 'bookings.id')
            ->join('packages', 'book_packages.id_package', '=', 'packages.id')
            ->join('package_products', 'packages.id', '=', 'package_products.id_package')
            ->join('products', 'package_products.id_product', '=', 'products.id')
            ->leftJoin('users', 'bookings.id_user', '=', 'users.id')
            ->where('products.id_vendor', $vendor->id)
            ->select(
                'bookings.id as booking_id',
                'bookings.booking_code as booking_code',
                DB::raw("'Package' as type"),
                'packages.name_package as item_name',
                'users.name as customer_name',
                'bookings.created_at as transaction_date',
                'book_packages.total_price as price',
                DB::raw("1 as quantity"),
                'bookings.status as booking_status'
            );

        // Combine queries
        $combinedQuery = $productQuery->union($packageQuery);

        // Apply filters to the combined query
        $filteredQuery = DB::table(DB::raw("({$combinedQuery->toSql()}) as combined"))
            ->mergeBindings($combinedQuery);

        if ($request->filled('product_name')) {
            $filteredQuery->where('item_name', 'like', '%' . $request->input('product_name') . '%');
        }
        if ($request->filled('date_from')) {
            $filteredQuery->whereDate('transaction_date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $filteredQuery->whereDate('transaction_date', '<=', $request->input('date_to'));
        }

        $rows = $filteredQuery->orderBy('transaction_date', 'desc')->get();

        $format = $request->input('format', 'csv');

        if ($format === 'pdf') {
            // Render PDF using barryvdh/laravel-dompdf if available, else fallback to simple HTML
            try {
                $pdfView = view('vendor.transactions_report_pdf', ['transactions' => $rows])->render();
                if (class_exists('\\Barryvdh\\DomPDF\\Facade\\Pdf')) {
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($pdfView);
                    return $pdf->download('transactions_report.pdf');
                } else {
                    // fallback: return HTML download as .html file
                    return response($pdfView, 200, [
                        'Content-Type' => 'text/html',
                        'Content-Disposition' => 'attachment; filename="transactions_report.html"',
                    ]);
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'PDF export failed: ' . $e->getMessage());
            }
        }

        // CSV export
        $filename = 'transactions_report_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['Booking ID', 'Type', 'Item', 'Customer', 'Transaction Date', 'Price', 'Quantity', 'Status'];

        $callback = function () use ($rows, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($rows as $r) {
                fputcsv($file, [
                    $r->booking_id,
                    $r->type,
                    $r->item_name,
                    $r->customer_name,
                    \Carbon\Carbon::parse($r->transaction_date)->format('Y-m-d H:i'),
                    number_format($r->price ?? 0, 2, '.', ''),
                    $r->quantity ?? 1,
                    $r->booking_status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ========== TOP-LEVEL PRODUCT & ADDON MANAGEMENT (SUPER ADMIN) ==========

    // List all products across vendors (top-level)
    public function allProducts()
    {
        $products = Product::with('vendor', 'category')->orderBy('created_at', 'desc')->paginate(5);
        return view('super_admin.products_list', compact('products'));
    }

    // Add stock (top-level) to a product
    public function addProductStockTop(Request $request, $productId)
    {
        $request->validate([
            'amount' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($productId);
        $amount = (int)$request->input('amount', 0);
        $product->jumlah = ($product->jumlah ?? 0) + $amount;
        $product->save();

        return redirect()->back()->with('success', "Added {$amount} to product stock.");
    }

    // Vendor: add stock to own product
    public function addProductStockVendor(Request $request, $productId)
    {
        $request->validate([
            'amount' => 'required|integer|min:1'
        ]);

        $vendor = Auth::guard('vendor')->user();
        $product = Product::where('id', $productId)->where('id_vendor', $vendor->id)->firstOrFail();

        $amount = (int)$request->input('amount', 0);
        $product->jumlah = ($product->jumlah ?? 0) + $amount;
        $product->save();

        return redirect()->back()->with('success', "Added {$amount} to product stock.");
    }

    // Show edit form for a top-level product
    public function editProductTop($productId)
    {
        $product = Product::findOrFail($productId);
        $categories = Category::all();
        return view('super_admin.products_edit', compact('product', 'categories'));
    }

    // Update a top-level product
    public function updateProductTop(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'phone' => 'nullable|string',
            'basic_price' => 'required|numeric|min:0',
            'nta' => 'nullable|numeric|min:0',
            'upsell' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_expires_at' => 'nullable|date',
            'status' => 'required|in:available,unavailable,draft,publish',
        ]);

        $nta = $validated['nta'] ?? $product->nta;
        $upsell = $validated['upsell'] ?? $product->upsell;
        $discountType = $validated['discount_type'] ?? $product->discount_type;
        $discountValue = $validated['discount_value'] ?? $product->discount_value;
        
        // Calculate discount amount
        $discountAmount = 0;
        $totalPrice = $nta + $upsell;
        if ($discountType === 'percentage' && $discountValue > 0) {
            $discountAmount = ($totalPrice * $discountValue) / 100;
        } elseif ($discountType === 'fixed' && $discountValue > 0) {
            $discountAmount = $discountValue;
        }

        $product->update([
            'name' => $validated['name'],
            'id_category' => $validated['category_id'],
            'description' => $validated['description'] ?? $product->description,
            'location' => $validated['location'] ?? $product->location,
            'phone' => $validated['phone'] ?? $product->phone,
            'basic_price' => $validated['basic_price'],
            'nta' => $nta,
            'upsell' => $upsell,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'discount_amount' => $discountAmount,
            'discount_expires_at' => $validated['discount_expires_at'] ?? $product->discount_expires_at,
            'total_price_before_discount' => $totalPrice,
            'final_price' => $totalPrice - $discountAmount,
            'status' => $validated['status'],
        ]);

        alert()->success('Success', 'Product updated successfully');
        return redirect()->route('super_admin.products');
    }

    // Delete a top-level product
    public function deleteProductTop($productId)
    {
        $product = Product::findOrFail($productId);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        if ($product->image2) {
            Storage::disk('public')->delete($product->image2);
        }
        if ($product->image3) {
            Storage::disk('public')->delete($product->image3);
        }

        $product->delete();

        alert()->success('Success', 'Product deleted successfully');
        return redirect()->route('super_admin.products');
    }

    // List all addons across vendors (top-level)
    public function allAddons()
    {
        $addons = Addon::with('vendor')->orderBy('created_at', 'desc')->paginate(15);
        return view('super_admin.addons_list', compact('addons'));
    }

    // Show edit form for a top-level addon
    public function editAddonTop($addonId)
    {
        $addon = Addon::findOrFail($addonId);
        return view('super_admin.addons_edit', compact('addon'));
    }

    // Update a top-level addon
    public function updateAddonTop(Request $request, $addonId)
    {
        $addon = Addon::findOrFail($addonId);

        $validated = $request->validate([
            'addons' => 'required|string|max:255',
            'location' => 'nullable|string',
            'phone' => 'nullable|string',
            'desc' => 'nullable|string',
            'nta' => 'required|numeric|min:0',
            'upsell' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_expires_at' => 'nullable|date',
            'status' => 'required|in:available,unavailable,draft,publish',
        ]);

        $nta = $validated['nta'];
        $upsell = $validated['upsell'] ?? $addon->upsell;
        $discountType = $validated['discount_type'] ?? $addon->discount_type;
        $discountValue = $validated['discount_value'] ?? $addon->discount_value;
        
        // Calculate discount amount
        $discountAmount = 0;
        $totalPrice = $nta + $upsell;
        if ($discountType === 'percentage' && $discountValue > 0) {
            $discountAmount = ($totalPrice * $discountValue) / 100;
        } elseif ($discountType === 'fixed' && $discountValue > 0) {
            $discountAmount = $discountValue;
        }

        $addon->update([
            'addons' => $validated['addons'],
            'location' => $validated['location'] ?? $addon->location,
            'phone' => $validated['phone'] ?? $addon->phone,
            'desc' => $validated['desc'] ?? $addon->desc,
            'nta' => $nta,
            'upsell' => $upsell,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'discount_amount' => $discountAmount,
            'discount_expires_at' => $validated['discount_expires_at'] ?? $addon->discount_expires_at,
            'total_price_before_discount' => $totalPrice,
            'final_price' => $totalPrice - $discountAmount,
            'status' => $validated['status'],
        ]);

        alert()->success('Success', 'Addon updated successfully');
        return redirect()->route('super_admin.addons');
    }

    // Delete a top-level addon
    public function deleteAddonTop($addonId)
    {
        $addon = Addon::findOrFail($addonId);

        if ($addon->image) {
            Storage::disk('public')->delete($addon->image);
        }

        $addon->delete();

        alert()->success('Success', 'Addon deleted successfully');
        return redirect()->route('super_admin.addons');
    }
}