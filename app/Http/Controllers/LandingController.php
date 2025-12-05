<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Package;
use App\Models\Product;
use App\Models\Addon;
use App\Models\Category;
use App\Models\Type;

class LandingController extends Controller
{
    /**
     * Show the application's landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('user.home');
        }

        // Get search type from request, default to 'all'
        $searchType = $request->get('type', 'all');

        // Get filter options
        $categories = Category::all();
        $types = Type::all();

        // Query packages with relationships
        $packageQuery = Package::with(['reviews', 'vendorInfo.vendor', 'vendorInfo.city', 'vendorInfo.province', 'type']);

        // Apply destination filter if provided
        if ($request->filled('destination')) {
            $packageQuery->where(function($query) use ($request) {
                $query->where('name_package', 'like', '%' . $request->destination . '%')
                      ->orWhere('description', 'like', '%' . $request->destination . '%')
                      ->orWhereHas('vendorInfo', function($vendorQuery) use ($request) {
                          $vendorQuery->where('name_corporate', 'like', '%' . $request->destination . '%')
                                    ->orWhere('address', 'like', '%' . $request->destination . '%')
                                    ->orWhereHas('city', function($cityQuery) use ($request) {
                                        $cityQuery->where('city.name', 'like', '%' . $request->destination . '%');
                                    })
                                    ->orWhereHas('province', function($provinceQuery) use ($request) {
                                        $provinceQuery->where('province.name', 'like', '%' . $request->destination . '%');
                                    });
                      });
            });
        }

        $packages = $packageQuery->where('is_active', true)->paginate(12);

        // Query products
        $productQuery = Product::with(['category', 'vendor.vendorInfo.city', 'vendor.vendorInfo.province']);

        if ($request->filled('destination')) {
            $productQuery->where(function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->destination . '%')
                      ->orWhere('description', 'like', '%' . $request->destination . '%')
                      ->orWhereHas('category', function($categoryQuery) use ($request) {
                          $categoryQuery->where('categories', 'like', '%' . $request->destination . '%');
                      });
            });
        }

        $products = $productQuery->where('status', 'available')->paginate(12);

        // Query addons
        $addonQuery = Addon::with(['vendor.vendorInfo.city', 'vendor.vendorInfo.province']);

        if ($request->filled('destination')) {
            $addonQuery->where(function($query) use ($request) {
                $query->where('addons', 'like', '%' . $request->destination . '%')
                      ->orWhere('desc', 'like', '%' . $request->destination . '%');
            });
        }

        $addons = $addonQuery->where('status', 'publish')->paginate(12);

        // Popular packages: paginate 8 by highest average rating
        $popularPackages = Package::with(['reviews'])
            ->where('is_active', true)
            ->withCount('reviews')
            ->orderByRaw('(SELECT AVG(rating) FROM reviews WHERE reviews.package_id = packages.id) DESC')
            ->orderBy('reviews_count', 'DESC')
            ->paginate(8, ['*'], 'popularPackagesPage');

        // Newest packages: paginate 8 from newest to oldest
        $newestPackages = Package::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(8, ['*'], 'newestPackagesPage');

        return view('user.search', compact('packages', 'products', 'addons', 'searchType', 'categories', 'types', 'popularPackages', 'newestPackages'));
    }
}
