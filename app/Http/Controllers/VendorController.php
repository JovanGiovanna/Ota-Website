<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorInfo;
use App\Models\Booking;
use App\Models\Vendor;

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
            'coordinate' => 0,
            'landmark' => 0,
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
        ]);

        $vendor->update([
            'name' => $request->name,
            'email' => $request->email,
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
        // Assuming products are related to vendor
        $products = []; // Replace with actual product model query, e.g., Product::where('vendor_id', $vendorId)->paginate(10);

        return view('super_admin.vendors.products', compact('vendor', 'products'));
    }

    public function vendorAddons($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        // Assuming addons are related to vendor
        $addons = []; // Replace with actual addon model query, e.g., Addon::where('vendor_id', $vendorId)->paginate(10);

        return view('super_admin.vendors.addons', compact('vendor', 'addons'));
    }

    public function vendorProfile($vendorId)
    {
        $vendor = Vendor::with('vendorInfo')->findOrFail($vendorId);

        return view('super_admin.vendors.profile', compact('vendor'));
    }

    public function vendorTransactionProducts($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        // Assuming transactions are related to vendor products
        $transactions = []; // Replace with actual transaction model query

        return view('super_admin.vendors.transaction_products', compact('vendor', 'transactions'));
    }

    public function vendorTransactionAddons($vendorId)
    {
        $vendor = Vendor::findOrFail($vendorId);
        // Assuming transactions are related to vendor addons
        $transactions = []; // Replace with actual transaction model query

        return view('super_admin.vendors.transaction_addons', compact('vendor', 'transactions'));
    }

    // Vendor-specific methods for vendor dashboard
    public function vendorProductsDashboard()
    {
        $vendor = Auth::guard('vendor')->user();
        $products = \App\Models\Product::where('id_vendor', $vendor->id)->with('category')->paginate(10);

        return view('vendor.products', compact('products'));
    }

    public function vendorAddonsDashboard()
    {
        $vendor = Auth::guard('vendor')->user();
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

    // Product CRUD methods
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
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'jumlah' => 'required|integer|min:1',
            'max_adults' => 'nullable|integer|min:0',
            'max_children' => 'nullable|integer|min:0',
            'status' => 'nullable|in:available,unavailable,draft',
        ]);

        $vendor = Auth::guard('vendor')->user();

        $data = $request->all();
        $data['id_vendor'] = $vendor->id;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        \App\Models\Product::create($data);

        return redirect()->route('vendor.products')->with('success', 'Product created successfully');
    }

    public function editProduct($id)
    {
        $vendor = Auth::guard('vendor')->user();
        $product = \App\Models\Product::where('id', $id)->where('id_vendor', $vendor->id)->firstOrFail();
        $categories = \App\Models\Category::all();

        return view('vendor.products.edit', compact('product', 'categories'));
    }

    public function updateProduct(Request $request, $id)
    {
        $vendor = Auth::guard('vendor')->user();
        $product = \App\Models\Product::where('id', $id)->where('id_vendor', $vendor->id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'id_category' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'jumlah' => 'required|integer|min:1',
            'max_adults' => 'nullable|integer|min:0',
            'max_children' => 'nullable|integer|min:0',
            'status' => 'nullable|in:available,unavailable,draft',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && \Storage::disk('public')->exists($product->image)) {
                \Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);

        return redirect()->route('vendor.products')->with('success', 'Product updated successfully');
    }

    public function destroyProduct($id)
    {
        $vendor = Auth::guard('vendor')->user();
        $product = \App\Models\Product::where('id', $id)->where('id_vendor', $vendor->id)->firstOrFail();

        // Delete image if exists
        if ($product->image && \Storage::disk('public')->exists($product->image)) {
            \Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('vendor.products')->with('success', 'Product deleted successfully');
    }

    // Addon CRUD methods
    public function createAddon()
    {
        return view('vendor.addons.create');
    }

    public function storeAddon(Request $request)
    {
        $request->validate([
            'addons' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'desc' => 'nullable|string|max:500',
            'status' => 'sometimes|string|in:available,unavailable,draft',
            'publish' => 'sometimes|boolean',
            'image_url' => 'nullable|url|max:2048',
        ]);

        $vendor = Auth::guard('vendor')->user();

        $data = $request->all();
        $data['id_vendor'] = $vendor->id;

        \App\Models\Addon::create($data);

        return redirect()->route('vendor.addons')->with('success', 'Addon created successfully');
    }

    public function editAddon($id)
    {
        $vendor = Auth::guard('vendor')->user();
        $addon = \App\Models\Addon::where('id', $id)->where('id_vendor', $vendor->id)->firstOrFail();

        return view('vendor.addons.edit', compact('addon'));
    }

    public function updateAddon(Request $request, $id)
    {
        $vendor = Auth::guard('vendor')->user();
        $addon = \App\Models\Addon::where('id', $id)->where('id_vendor', $vendor->id)->firstOrFail();

        $request->validate([
            'addons' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'desc' => 'nullable|string|max:500',
            'status' => 'sometimes|string|in:available,unavailable,draft',
            'publish' => 'sometimes|boolean',
            'image_url' => 'nullable|url|max:2048',
        ]);

        $addon->update($request->all());

        return redirect()->route('vendor.addons')->with('success', 'Addon updated successfully');
    }

    public function destroyAddon($id)
    {
        $vendor = Auth::guard('vendor')->user();
        $addon = \App\Models\Addon::where('id', $id)->where('id_vendor', $vendor->id)->firstOrFail();

        $addon->delete();

        return redirect()->route('vendor.addons')->with('success', 'Addon deleted successfully');
    }
}
