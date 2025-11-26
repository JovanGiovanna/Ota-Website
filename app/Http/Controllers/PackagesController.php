<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Product;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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
     * Show the form for creating a new package.
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
     * Store a newly created package in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_package' => 'required|string|max:255',
            'description' => 'nullable|string',
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'pax_paid_input' => 'nullable|numeric|min:0',
            'nta' => 'required|numeric|min:0',
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
        $totalNTA = 0;
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

            // Calculate products data and NTA
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

            // Calculate addons data and add to NTA
            $addonsData = [];
            $selectedAddons = Addon::whereIn('id', $addonsIds)->get();
            foreach ($selectedAddons as $addon) {
                $pax = $data['addon_pax'][$addon->id] ?? 1;
                $addonPrice = $addon->nta ?? $addon->basic_price ?? 0;

                $subTotal = $addonPrice * $pax;
                $totalNTA += $subTotal;

                $addonsData[] = [
                    'id' => $addon->id,
                    'name' => $addon->addons,
                    'nta' => $addonPrice,
                    'pax' => (int) $pax,
                    'sub_total' => $subTotal,
                ];
            }

            $totalPricePublishCalculated = $totalNTA;
            $paxPaidCalculated = ($totalPax > 0) ? $totalPricePublishCalculated / $totalPax : $totalPricePublishCalculated;
            $finalNTA = $data['nta'];
            $finalPaxPaid = $data['pax_paid_input'] ?? $paxPaidCalculated;

            // Generate unique slug
            $slug = Str::slug($data['name_package']);
            $originalSlug = $slug;
            $count = 1;
            while (Package::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            // Create package
            Package::create([
                'name_package' => $data['name_package'],
                'slug' => $slug,
                'description' => $data['description'],
                'images' => $uploadedImagePaths,
                'nta' => $finalNTA,
                'pax_paid' => round($finalPaxPaid, 2),
                'tax_rate' => $data['tax_rate'] ?? 0,
                'start_publish' => $data['start_publish'],
                'end_publish' => $data['end_publish'] ?? null,
                'is_active' => $data['is_active'],
                'products_data' => $productsData,
                'addons_data' => $addonsData,
            ]);

            DB::commit();
            return redirect()->route('super_admin.packages')->with('success', 'Paket berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();

            if (!empty($uploadedImagePaths)) {
                Storage::disk('public')->delete($uploadedImagePaths);
            }

            \Log::error('Package store failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan paket: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified package.
     *
     * @param \App\Models\Package $package
     * @return \Illuminate\View\View
     */
    public function edit(Package $package)
    {
        $products = Product::all();
        $addons = Addon::all();

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

        $selectedProductsData = collect($selectedProductsData)->map(function ($product) {
            return [
                'id' => $product['id'] ?? '',
                'name' => $product['name'] ?? 'Unknown Product',
                'nta' => $product['nta'] ?? $product['price'] ?? 0,
                'pax' => $product['pax'] ?? 1,
            ];
        })->toArray();

        $selectedAddonsData = collect($selectedAddonsData)->map(function ($addon) {
            return [
                'id' => $addon['id'] ?? '',
                'name' => $addon['name'] ?? $addon['addons'] ?? 'Unknown Addon',
                'nta' => $addon['nta'] ?? $addon['price'] ?? 0,
                'pax' => $addon['pax'] ?? 1,
            ];
        })->toArray();

        $selectedProductIds = collect($selectedProductsData)->pluck('id')->toArray();
        $selectedAddonIds = collect($selectedAddonsData)->pluck('id')->toArray();

        $currentImages = $package->images ?? [];
        if (is_string($currentImages)) {
            $currentImages = json_decode($currentImages, true) ?: [];
        }
        $currentImages = $currentImages ?? [];

        return view('super_admin.packages.edit', compact(
            'package',
            'products',
            'addons',
            'selectedProductIds',
            'selectedAddonIds',
            'selectedProductsData',
            'selectedAddonsData'
        ));
    }

    /**
     * Update the specified package in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Package $package
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Package $package)
    {
        $validator = Validator::make($request->all(), [
            'name_package' => 'required|string|max:255',
            'description' => 'nullable|string',
            'images' => 'nullable|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'pax_paid_input' => 'nullable|numeric|min:0',
            'nta' => 'required|numeric|min:0',
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
        $totalNTA = 0;
        $totalPax = 0;

        DB::beginTransaction();
        try {
            // Process products
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

            // Process addons
            $addonsData = [];
            $selectedAddons = Addon::whereIn('id', $addonsIds)->get();
            foreach ($selectedAddons as $addon) {
                $pax = $data['addon_pax'][$addon->id] ?? 1;
                $addonPrice = $addon->nta ?? $addon->basic_price ?? 0;

                $subTotal = $addonPrice * $pax;
                $totalNTA += $subTotal;

                $addonsData[] = [
                    'id' => $addon->id,
                    'name' => $addon->addons,
                    'nta' => $addonPrice,
                    'pax' => (int) $pax,
                    'sub_total' => $subTotal,
                ];
            }

            $totalPricePublishCalculated = $totalNTA;
            $paxPaidCalculated = ($totalPax > 0) ? $totalPricePublishCalculated / $totalPax : $totalPricePublishCalculated;
            $finalNTA = $data['nta'];
            $finalPaxPaid = $data['pax_paid_input'] ?? $paxPaidCalculated;

            $packageData = [
                'name_package' => $data['name_package'],
                'description' => $data['description'],
                'nta' => $finalNTA,
                'pax_paid' => round($finalPaxPaid, 2),
                'tax_rate' => $data['tax_rate'] ?? 0,
                'start_publish' => $data['start_publish'],
                'end_publish' => $data['end_publish'] ?? null,
                'is_active' => $data['is_active'],
                'products_data' => $productsData,
                'addons_data' => $addonsData,
            ];

            // Handle images
            $currentImages = $package->images ?? [];
            if (is_string($currentImages)) {
                $currentImages = json_decode($currentImages, true) ?: [];
            }
            $currentImages = $currentImages ?? [];

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

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $currentImages[] = $image->store('packages', 'public');
                }
            }

            if ($request->hasFile('images') || $request->has('remove_images')) {
                $packageData['images'] = $currentImages;
            }

            // Update slug
            $slug = Str::slug($packageData['name_package']);
            $originalSlug = $slug;
            $count = 1;
            while (Package::where('slug', $slug)->where('id', '!=', $package->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $packageData['slug'] = $slug;

            $package->update($packageData);
            DB::commit();

            return redirect()->route('super_admin.packages')->with('success', 'Paket berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Package update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui paket: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified package from storage.
     *
     * @param \App\Models\Package $package
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Package $package)
    {
        $images = $package->images ?? [];
        if (is_string($images)) {
            $images = json_decode($images, true) ?: [];
        }
        $images = $images ?? [];

        foreach ($images as $imagePath) {
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $package->delete();

        return redirect()->route('super_admin.packages')->with('success', 'Paket berhasil dihapus!');
    }

    /**
     * Display user landing page with popular and newest packages.
     *
     * @return \Illuminate\View\View
     */
    public function userLanding()
    {
        $popularPackages = Package::where('is_active', true)
            ->orderBy('created_at', 'asc')
            ->paginate(8, ['*'], 'popularPackagesPage');

        $newestPackages = Package::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(8, ['*'], 'newestPackagesPage');

        return view('user.landing', compact('popularPackages', 'newestPackages'));
    }

    /**
     * Show the detail page of specified package.
     *
     * @param \App\Models\Package $package
     * @return \Illuminate\View\View
     */
    public function showDetail(Package $package)
    {
        $package->load('reviews');

        // Try to manually get vendorInfo if possible
        $vendorInfo = null;

        // Example logic: if package has products_data and first product has vendor_id, try to get vendor info
        if (!empty($package->products_data) && is_array($package->products_data)) {
            $firstProduct = $package->products_data[0] ?? null;
            if ($firstProduct && isset($firstProduct['vendor_id'])) {
                $vendorInfo = \App\Models\VendorInfo::find($firstProduct['vendor_id']);
            }
        }

        // Attach vendorInfo to package dynamically to avoid errors in view
        $package->vendorInfo = $vendorInfo;

        // Check if logged-in user has this package in wishlist
        $isInWishlist = false;
        $user = Auth::user();
        if ($user) {
            $isInWishlist = $user->wishlists()
                ->where('wishable_type', Package::class)
                ->where('wishable_id', $package->id)
                ->exists();
        }

        return view('user.package_detail', compact('package', 'isInWishlist'));
    }
}
