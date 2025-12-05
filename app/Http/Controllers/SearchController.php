<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Product;
use App\Models\Addon;
use App\Models\Category;
use App\Models\Type;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Get search type from request, default to 'all'
        $searchType = $request->get('type', 'all');

        // Get filter options
        $categories = Category::all();
        $types = Type::all();

        // Query packages with relationships
        $packageQuery = Package::with(['reviews', 'vendorInfo.vendor', 'vendorInfo.city', 'vendorInfo.province', 'type']);

        // Apply destination filter
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

        // Apply date filters
        if ($request->filled('checkin') || $request->filled('checkout')) {
            $checkin = $request->checkin;
            $checkout = $request->checkout;

            if ($checkin && $checkout) {
                // Both dates provided: package must be active throughout the period
                $packageQuery->where('start_publish', '<=', $checkin)
                            ->where(function($q) use ($checkout) {
                                $q->where('end_publish', '>=', $checkout)
                                  ->orWhereNull('end_publish');
                            });
            } elseif ($checkin) {
                // Only checkin provided: package must be active on checkin date
                $packageQuery->where('start_publish', '<=', $checkin)
                            ->where(function($q) use ($checkin) {
                                $q->where('end_publish', '>=', $checkin)
                                  ->orWhereNull('end_publish');
                            });
            } elseif ($checkout) {
                // Only checkout provided: package must be active on checkout date
                $packageQuery->where('start_publish', '<=', $checkout)
                            ->where(function($q) use ($checkout) {
                                $q->where('end_publish', '>=', $checkout)
                                  ->orWhereNull('end_publish');
                            });
            }
        }

        // Apply type filter
        if ($request->filled('package_type')) {
            $packageQuery->where('id_type', $request->package_type);
        }

        // Apply price filters
        if ($request->filled('price_min')) {
            $packageQuery->where('nta', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $packageQuery->where('nta', '<=', $request->price_max);
        }

        // Apply rating filter
        if ($request->filled('rating_min')) {
            $packageQuery->whereHas('reviews', function($q) use ($request) {
                $q->selectRaw('package_id, AVG(rating) as avg_rating')
                  ->groupBy('package_id')
                  ->havingRaw('AVG(rating) >= ?', [$request->rating_min]);
            });
        }

        $packages = $packageQuery->where('is_active', true)->paginate(12);

        // Query products with relationships
        $productQuery = Product::with(['category', 'vendor.vendorInfo.city', 'vendor.vendorInfo.province']);

        // Apply destination filter
        if ($request->filled('destination')) {
            $productQuery->where(function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->destination . '%')
                      ->orWhere('description', 'like', '%' . $request->destination . '%')
                      ->orWhereHas('category', function($categoryQuery) use ($request) {
                          $categoryQuery->where('categories', 'like', '%' . $request->destination . '%');
                      })
                      ->orWhereHas('vendor', function($vendorQuery) use ($request) {
                          $vendorQuery->where('name', 'like', '%' . $request->destination . '%')
                                    ->orWhereHas('vendorInfo', function($vendorInfoQuery) use ($request) {
                                        $vendorInfoQuery->where('name_corporate', 'like', '%' . $request->destination . '%')
                                                      ->orWhere('address', 'like', '%' . $request->destination . '%')
                                                      ->orWhereHas('city', function($cityQuery) use ($request) {
                                                          $cityQuery->where('city.name', 'like', '%' . $request->destination . '%');
                                                      })
                                                      ->orWhereHas('province', function($provinceQuery) use ($request) {
                                                          $provinceQuery->where('province.name', 'like', '%' . $request->destination . '%');
                                                      });
                                    });
                      });
            });
        }

        // Apply category filter
        if ($request->filled('category')) {
            $productQuery->where('id_category', $request->category);
        }

        // Apply price filters
        if ($request->filled('price_min')) {
            $productQuery->where('basic_price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $productQuery->where('basic_price', '<=', $request->price_max);
        }

        $products = $productQuery->where('status', 'publish')->paginate(12);

        // Query addons with relationships
        $addonQuery = Addon::with(['vendor.vendorInfo.city', 'vendor.vendorInfo.province']);

        // Apply destination filter
        if ($request->filled('destination')) {
            $addonQuery->where(function($query) use ($request) {
                $query->where('addons', 'like', '%' . $request->destination . '%')
                      ->orWhere('desc', 'like', '%' . $request->destination . '%')
                      ->orWhereHas('vendor', function($vendorQuery) use ($request) {
                          $vendorQuery->where('name', 'like', '%' . $request->destination . '%')
                                    ->orWhereHas('vendorInfo', function($vendorInfoQuery) use ($request) {
                                        $vendorInfoQuery->where('name_corporate', 'like', '%' . $request->destination . '%')
                                                      ->orWhere('address', 'like', '%' . $request->destination . '%')
                                                      ->orWhereHas('city', function($cityQuery) use ($request) {
                                                          $cityQuery->where('city.name', 'like', '%' . $request->destination . '%');
                                                      })
                                                      ->orWhereHas('province', function($provinceQuery) use ($request) {
                                                          $provinceQuery->where('province.name', 'like', '%' . $request->destination . '%');
                                                      });
                                    });
                      });
            });
        }

        // Apply price filters
        if ($request->filled('price_min')) {
            $addonQuery->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $addonQuery->where('price', '<=', $request->price_max);
        }

        $addons = $addonQuery->where('status', 'publish')->paginate(12);

        // Popular packages: paginate 8 by highest average rating (most popular first)
        $popularPackages = Package::with(['reviews'])
            ->where('is_active', true)
            ->withCount('reviews')
            ->orderByRaw('(SELECT AVG(rating) FROM reviews WHERE reviews.package_id = packages.id) DESC')
            ->orderBy('reviews_count', 'DESC')
            ->paginate(8, ['*'], 'popularPackagesPage');

        // Newest packages: paginate 8 from newest to oldest (id descending)
        $newestPackages = Package::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(8, ['*'], 'newestPackagesPage');

        return view('user.search', compact('packages', 'products', 'addons', 'searchType', 'categories', 'types', 'popularPackages', 'newestPackages'));
    }
}
