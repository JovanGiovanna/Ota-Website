@extends('layouts.user')

@section('title', 'Search')

@section('welcome')
Find your perfect packages, products, and add-ons!
@endsection

@section('content')
<!-- Search Hero Section -->
<div class="relative bg-cover bg-center rounded-2xl p-3 mb-4 text-white shadow-xl overflow-hidden" style="background-image: url('{{ asset('resort.jpeg') }}'); min-height: 150px;">
    <!-- Simple dark overlay -->
    <div class="absolute inset-0 bg-gradient-to-br from-black/70 via-purple-900/60 to-indigo-900/70"></div>

    <div class="relative max-w-4xl mx-auto text-center z-10">
        <div class="mb-2">
            <span class="inline-block px-2 py-1 bg-white/10 backdrop-blur-sm rounded-full text-xs font-medium text-white/90 border border-white/20">
                <i class="fas fa-search mr-1"></i>Advanced Search
            </span>
        </div>
        <h1 class="text-xl md:text-2xl font-bold mb-2 bg-gradient-to-r from-white via-blue-100 to-purple-100 bg-clip-text text-transparent">
            Find Your Perfect Package
        </h1>
        <p class="text-blue-100 text-sm md:text-base mb-3 leading-relaxed">
            Discover amazing packages with products and add-ons for your next adventure
        </p>

        <!-- Advanced Filters Button -->
        <div class="mb-4 text-center">
            <button type="button" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl" onclick="toggleAdvancedFilters()">
                <i class="fas fa-filter mr-2"></i>
                Advanced Filters
                <i class="fas fa-chevron-down ml-2 transition-transform duration-300" id="advanced-filters-icon"></i>
            </button>
        </div>

        <!-- Advanced Search Form -->
        <div class="bg-white rounded-2xl p-4 shadow-xl">
            <form method="GET" action="{{ route('user.search') }}" class="space-y-6">
                <!-- Basic Search Row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search by City, Package, or Category</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                            </div>
                            <input type="text" name="destination" value="{{ request('destination') }}" placeholder="Search cities, packages, categories..." class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div class="md:col-span-2 flex items-end">
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                            <i class="fas fa-search mr-2"></i>
                            Search
                        </button>
                    </div>
                </div>

                <!-- Advanced Filters (Collapsible) -->
                <div class="border-t border-white/20 pt-6">
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 hidden overflow-hidden transition-all duration-300" id="advanced-filters">
                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Min Price</label>
                            <input type="number" name="price_min" value="{{ request('price_min') }}" placeholder="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Price</label>
                            <input type="number" name="price_max" value="{{ request('price_max') }}" placeholder="No limit" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <!-- Package Type (only for packages) -->
                        @if($searchType == 'all' || $searchType == 'packages')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Package Type</label>
                                <select name="package_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">All Types</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" {{ request('package_type') == $type->id ? 'selected' : '' }}>{{ $type->type }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Category (only for products) -->
                        @if($searchType == 'all' || $searchType == 'products')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->categories }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Rating Filter -->
                        @if($searchType == 'all' || $searchType == 'packages')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Min Rating</label>
                                <select name="rating_min" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Any Rating</option>
                                    <option value="1" {{ request('rating_min') == '1' ? 'selected' : '' }}>1+ Stars</option>
                                    <option value="2" {{ request('rating_min') == '2' ? 'selected' : '' }}>2+ Stars</option>
                                    <option value="3" {{ request('rating_min') == '3' ? 'selected' : '' }}>3+ Stars</option>
                                    <option value="4" {{ request('rating_min') == '4' ? 'selected' : '' }}>4+ Stars</option>
                                    <option value="5" {{ request('rating_min') == '5' ? 'selected' : '' }}>5 Stars</option>
                                </select>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Enhanced Type Tabs -->
<div class="flex flex-wrap gap-2 mb-8 bg-white/80 backdrop-blur-sm rounded-2xl p-2 shadow-xl border border-white/50">
    <a href="{{ route('user.search', array_merge(request()->query(), ['type' => 'all'])) }}"
       class="group relative px-6 py-3 rounded-2xl font-bold text-base transition-all duration-300 transform hover:scale-105 {{ $searchType == 'all' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-2xl shadow-blue-500/25' : 'text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-blue-50 hover:text-blue-700 hover:shadow-lg' }}">
        <div class="flex items-center space-x-2">
            <i class="fas fa-th-large text-lg {{ $searchType == 'all' ? 'text-blue-100' : 'text-blue-500 group-hover:text-blue-600' }}"></i>
            <span>All</span>
        </div>
        @if($searchType == 'all')
            <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-white rounded-full animate-pulse"></div>
        @endif
    </a>
    <a href="{{ route('user.search', array_merge(request()->query(), ['type' => 'packages'])) }}"
       class="group relative px-6 py-3 rounded-2xl font-bold text-base transition-all duration-300 transform hover:scale-105 {{ $searchType == 'packages' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-2xl shadow-emerald-500/25' : 'text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-emerald-50 hover:text-emerald-700 hover:shadow-lg' }}">
        <div class="flex items-center space-x-2">
            <i class="fas fa-box text-lg {{ $searchType == 'packages' ? 'text-emerald-100' : 'text-emerald-500 group-hover:text-emerald-600' }}"></i>
            <span>Packages</span>
        </div>
        @if($searchType == 'packages')
            <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-white rounded-full animate-pulse"></div>
        @endif
    </a>
    <a href="{{ route('user.search', array_merge(request()->query(), ['type' => 'products'])) }}"
       class="group relative px-6 py-3 rounded-2xl font-bold text-base transition-all duration-300 transform hover:scale-105 {{ $searchType == 'products' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-2xl shadow-purple-500/25' : 'text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-purple-50 hover:text-purple-700 hover:shadow-lg' }}">
        <div class="flex items-center space-x-2">
            <i class="fas fa-shopping-bag text-lg {{ $searchType == 'products' ? 'text-purple-100' : 'text-purple-500 group-hover:text-purple-600' }}"></i>
            <span>Products</span>
        </div>
        @if($searchType == 'products')
            <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-white rounded-full animate-pulse"></div>
        @endif
    </a>
    <a href="{{ route('user.search', array_merge(request()->query(), ['type' => 'addons'])) }}"
       class="group relative px-6 py-3 rounded-2xl font-bold text-base transition-all duration-300 transform hover:scale-105 {{ $searchType == 'addons' ? 'bg-gradient-to-r from-orange-600 to-red-600 text-white shadow-2xl shadow-orange-500/25' : 'text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-orange-50 hover:text-orange-700 hover:shadow-lg' }}">
        <div class="flex items-center space-x-2">
            <i class="fas fa-plus-circle text-lg {{ $searchType == 'addons' ? 'text-orange-100' : 'text-orange-500 group-hover:text-orange-600' }}"></i>
            <span>Add-ons</span>
        </div>
        @if($searchType == 'addons')
            <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-white rounded-full animate-pulse"></div>
        @endif
    </a>
</div>

<!-- Search Results -->
@if(request()->has('destination'))
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-1">Search Results</h2>
                @if($searchType == 'all' || $searchType == 'packages')
                    <p class="text-gray-600 text-base">Found <span class="font-semibold text-blue-600">{{ $packages->count() }}</span> packages, <span class="font-semibold text-green-600">{{ $products->count() }}</span> products, <span class="font-semibold text-orange-600">{{ $addons->count() }}</span> add-ons matching your criteria</p>
                @elseif($searchType == 'products')
                    <p class="text-gray-600 text-base">Found <span class="font-semibold text-green-600">{{ $products->count() }}</span> products matching your criteria</p>
                @elseif($searchType == 'addons')
                    <p class="text-gray-600 text-base">Found <span class="font-semibold text-orange-600">{{ $addons->count() }}</span> add-ons matching your criteria</p>
                @endif
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <i class="fas fa-clock"></i>
                <span>Results loaded in 0.2s</span>
            </div>
        </div>
    </div>
@else
    <div class="mb-6">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-3">Explore Our Collection</h2>
            <p class="text-gray-600 text-base">Discover amazing packages, products, and add-ons for your next adventure</p>
        </div>
    </div>
@endif

<!-- Skeleton Loader (shown while loading) -->
<div id="skeleton-loader" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-8 hidden">
    @for($i = 0; $i < 8; $i++)
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden animate-pulse">
            <div class="h-40 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200"></div>
            <div class="p-6 space-y-3">
                <div class="h-6 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 rounded-lg"></div>
                <div class="space-y-2">
                    <div class="h-4 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 rounded"></div>
                    <div class="h-4 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 rounded w-3/4"></div>
                </div>
                <div class="flex justify-between items-center">
                    <div class="h-6 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 rounded-lg w-20"></div>
                    <div class="h-5 bg-gradient-to-r from-gray-200 via-gray-300 to-gray-200 rounded-full w-16"></div>
                </div>
            </div>
        </div>
    @endfor
</div>

<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @if($searchType == 'all' || $searchType == 'packages')
        @forelse($packages as $package)
            <a href="{{ route('user.package_detail', $package->id) }}" class="group block bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 transform hover:scale-[1.02] hover:-translate-y-2">
                <div class="relative overflow-hidden">
                    @php
                        $validImages = array_filter((array) ($package->images ?? []), function($img) {
                            return is_string($img) && !empty($img);
                        });
                    @endphp
                    @if($validImages && count($validImages) > 0)
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('storage/' . reset($validImages)) }}" alt="{{ $package->name_package }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            @if(count($validImages) > 1)
                                <!-- Image indicators -->
                                <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-1">
                                    @for($i = 0; $i < min(count($validImages), 3); $i++)
                                        <div class="w-1.5 h-1.5 rounded-full {{ $i === 0 ? 'bg-white' : 'bg-white/50' }}"></div>
                                    @endfor
                                    @if(count($validImages) > 3)
                                        <span class="text-white text-xs ml-1">+</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="w-full h-32 bg-gradient-to-br from-blue-400 via-indigo-500 to-purple-600 flex items-center justify-center relative overflow-hidden">
                            <i class="fas fa-box text-white text-3xl animate-bounce"></i>
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-300/20 to-purple-500/20 animate-pulse"></div>
                        </div>
                    @endif
                    <!-- Enhanced rating badge -->
                    <div class="absolute top-2 right-2 bg-white/95 backdrop-blur-lg rounded-xl px-2 py-1 shadow-lg border border-white/50">
                        <div class="flex items-center space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($package->averageRating()))
                                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                                @elseif($i - 0.5 <= $package->averageRating())
                                    <i class="fas fa-star-half-alt text-yellow-400 text-xs"></i>
                                @else
                                    <i class="far fa-star text-gray-300 text-xs"></i>
                                @endif
                            @endfor
                            <span class="text-xs font-bold ml-1 text-gray-800">{{ number_format($package->averageRating(), 1) }}</span>
                            <span class="text-xs text-gray-600">({{ $package->reviews->count() }})</span>
                        </div>
                    </div>
                    <!-- Best Seller badge -->
                    @if($package->averageRating() >= 4.5)
                        <div class="absolute top-2 left-2 bg-gradient-to-r from-blue-400 to-indigo-500 text-white rounded-full px-2 py-1 text-xs font-bold shadow-lg">
                            <i class="fas fa-crown mr-1"></i>Best Seller
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-300">{{ $package->name_package }}</h3>
                    <p class="text-gray-600 text-xs mb-3 line-clamp-2 leading-relaxed">{{ Str::limit($package->description, 60) }}</p>
                    <div class="flex items-center justify-between">
                        @if($package->finalPrice < $package->totalPriceBeforeDiscount)
                            <div class="text-xl font-bold text-blue-600">
                                <span class="text-xs text-gray-500 line-through mr-1">Rp {{ number_format($package->totalPriceBeforeDiscount, 0, ',', '.') }}</span>
                                Rp {{ number_format($package->finalPrice, 0, ',', '.') }}
                                <span class="text-xs bg-red-100 text-red-600 px-1 py-0.5 rounded-full ml-1 font-semibold">
                                    -{{ round((1 - $package->finalPrice / $package->totalPriceBeforeDiscount) * 100) }}%
                                </span>
                            </div>
                        @else
                            <div class="text-xl font-bold text-blue-600">Rp {{ number_format($package->finalPrice, 0, ',', '.') }}</div>
                        @endif
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">per package</span>
                    </div>
                </div>
            </a>
        @empty
            @if($searchType == 'packages')
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-box text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No packages found</h3>
                    <p class="text-gray-500 text-sm">Try adjusting your search criteria</p>
                </div>
            @endif
        @endforelse
    @endif

    @if($searchType == 'all' || $searchType == 'products')
        @forelse($products as $product)
            <a href="{{ route('user.product_detail', $product->id) }}" class="group block bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl hover:shadow-green-500/10 transition-all duration-500 transform hover:scale-[1.02] hover:-translate-y-2">
                <div class="relative overflow-hidden">
                    @php
                        $validImages = array_filter((array) ($product->images ?? []), function($img) {
                            return is_string($img) && !empty($img);
                        });
                    @endphp
                    @if($validImages && count($validImages) > 0)
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('storage/' . reset($validImages)) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                    @else
                        <div class="w-full h-32 bg-gradient-to-br from-green-400 via-emerald-500 to-teal-600 flex items-center justify-center relative overflow-hidden">
                            <i class="fas fa-shopping-cart text-white text-3xl animate-bounce"></i>
                            <div class="absolute inset-0 bg-gradient-to-br from-green-300/20 to-teal-500/20 animate-pulse"></div>
                        </div>
                    @endif
                    <!-- Enhanced rating badge -->
                    <div class="absolute top-2 right-2 bg-white/95 backdrop-blur-lg rounded-xl px-2 py-1 shadow-lg border border-white/50">
                        <div class="flex items-center space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($product->averageRating()))
                                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                                @elseif($i - 0.5 <= $product->averageRating())
                                    <i class="fas fa-star-half-alt text-yellow-400 text-xs"></i>
                                @else
                                    <i class="far fa-star text-gray-300 text-xs"></i>
                                @endif
                            @endfor
                            <span class="text-xs font-bold ml-1 text-gray-800">{{ number_format($product->averageRating(), 1) }}</span>
                            <span class="text-xs text-gray-600">({{ $product->reviews->count() }})</span>
                        </div>
                    </div>
                    <!-- Best Seller badge -->
                    @if($product->averageRating() >= 4.5)
                        <div class="absolute top-2 left-2 bg-gradient-to-r from-green-400 to-emerald-500 text-white rounded-full px-2 py-1 text-xs font-bold shadow-lg">
                            <i class="fas fa-crown mr-1"></i>Best Seller
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-green-600 transition-colors duration-300">{{ $product->name }}</h3>
                    <p class="text-gray-600 text-xs mb-3 line-clamp-2 leading-relaxed">{{ Str::limit($product->description, 60) }}</p>
                    <div class="flex items-center justify-between">
                        @if($product->finalPrice < $product->totalPriceBeforeDiscount)
                            <div class="text-xl font-bold text-green-600">
                                <span class="text-xs text-gray-500 line-through mr-1">Rp {{ number_format($product->totalPriceBeforeDiscount, 0, ',', '.') }}</span>
                                Rp {{ number_format($product->finalPrice, 0, ',', '.') }}
                                <span class="text-xs bg-red-100 text-red-600 px-1 py-0.5 rounded-full ml-1 font-semibold">
                                    -{{ round((1 - $product->finalPrice / $product->totalPriceBeforeDiscount) * 100) }}%
                                </span>
                            </div>
                        @else
                            <div class="text-xl font-bold text-green-600">Rp {{ number_format($product->finalPrice, 0, ',', '.') }}</div>
                        @endif
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">per unit</span>
                    </div>
                </div>
            </a>
        @empty
            @if($searchType == 'products')
                <div class="col-span-full text-center py-16">
                    <div class="relative mb-6">
                        <div class="w-24 h-24 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shopping-cart text-green-400 text-3xl"></i>
                        </div>
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center animate-bounce">
                            <i class="fas fa-search text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-3">No products found</h3>
                    <p class="text-gray-500 text-lg mb-6">Try adjusting your search criteria or explore other categories</p>
                    <a href="{{ route('user.search', ['type' => 'all']) }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-2xl font-semibold hover:from-green-600 hover:to-emerald-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <i class="fas fa-th-large mr-2"></i>
                        View All Items
                    </a>
                </div>
            @endif
        @endforelse
    @endif

    @if($searchType == 'all' || $searchType == 'addons')
        @forelse($addons as $addon)
            <a href="{{ route('user.addon_detail', $addon->id) }}" class="group block bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl hover:shadow-orange-500/10 transition-all duration-500 transform hover:scale-[1.02] hover:-translate-y-2">
                <div class="relative overflow-hidden">
                    @php
                        $validImages = array_filter((array) ($addon->images ?? []), function($img) {
                            return is_string($img) && !empty($img);
                        });
                    @endphp
                    @if($validImages && count($validImages) > 0)
                        <div class="relative h-32 overflow-hidden">
                            <img src="{{ asset('storage/' . reset($validImages)) }}" alt="{{ $addon->addons }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                    @else
                        <div class="w-full h-32 bg-gradient-to-br from-orange-400 via-red-500 to-pink-600 flex items-center justify-center relative overflow-hidden">
                            <i class="fas fa-plus-circle text-white text-3xl animate-bounce"></i>
                            <div class="absolute inset-0 bg-gradient-to-br from-orange-300/20 to-red-500/20 animate-pulse"></div>
                        </div>
                    @endif
                    <!-- Enhanced rating badge -->
                    <div class="absolute top-2 right-2 bg-white/95 backdrop-blur-lg rounded-xl px-2 py-1 shadow-lg border border-white/50">
                        <div class="flex items-center space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($addon->averageRating()))
                                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                                @elseif($i - 0.5 <= $addon->averageRating())
                                    <i class="fas fa-star-half-alt text-yellow-400 text-xs"></i>
                                @else
                                    <i class="far fa-star text-gray-300 text-xs"></i>
                                @endif
                            @endfor
                            <span class="text-xs font-bold ml-1 text-gray-800">{{ number_format($addon->averageRating(), 1) }}</span>
                            <span class="text-xs text-gray-600">({{ $addon->reviews->count() }})</span>
                        </div>
                    </div>
                    <!-- Best Seller badge -->
                    @if($addon->averageRating() >= 4.5)
                        <div class="absolute top-2 left-2 bg-gradient-to-r from-orange-400 to-red-500 text-white rounded-full px-2 py-1 text-xs font-bold shadow-lg">
                            <i class="fas fa-crown mr-1"></i>Best Seller
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-orange-600 transition-colors duration-300">{{ $addon->addons }}</h3>
                    <p class="text-gray-600 text-xs mb-3 line-clamp-2 leading-relaxed">{{ Str::limit($addon->desc, 60) }}</p>
                    <div class="flex items-center justify-between">
                        @if($addon->finalPrice < $addon->basic_price)
                            <div class="text-xl font-bold text-orange-600">
                                <span class="text-xs text-gray-500 line-through mr-1">Rp {{ number_format($addon->basic_price, 0, ',', '.') }}</span>
                                Rp {{ number_format($addon->finalPrice, 0, ',', '.') }}
                                <span class="text-xs bg-red-100 text-red-600 px-1 py-0.5 rounded-full ml-1 font-semibold">
                                    -{{ round((1 - $addon->finalPrice / $addon->basic_price) * 100) }}%
                                </span>
                            </div>
                        @else
                            <div class="text-xl font-bold text-orange-600">Rp {{ number_format($addon->finalPrice, 0, ',', '.') }}</div>
                        @endif
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">per unit</span>
                    </div>
                </div>
            </a>
        @empty
            @if($searchType == 'addons')
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-plus-circle text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No add-ons found</h3>
                    <p class="text-gray-500 text-sm">Try adjusting your search criteria</p>
                </div>
            @endif
        @endforelse
    @endif
</div>

<!-- New User Section Added -->
<section class="mt-12">
    <div>
        <!-- Enhanced Tabs navigation -->
        <div class="mb-8 bg-white/80 backdrop-blur-sm rounded-2xl p-2 shadow-xl border border-white/50">
            <nav class="flex space-x-2" aria-label="Tabs" id="packageTabs">
                <button class="group relative px-6 py-3 rounded-2xl font-bold text-base transition-all duration-300 transform hover:scale-105 flex items-center space-x-2 bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-xl shadow-orange-500/25" data-tab="popular" type="button">
                    <i class="fas fa-fire text-lg animate-pulse"></i>
                    <span>Popular Items</span>
                    <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-white rounded-full animate-pulse"></div>
                </button>
                <button class="group relative px-6 py-3 rounded-2xl font-bold text-base transition-all duration-300 transform hover:scale-105 flex items-center space-x-2 text-gray-700 hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 hover:text-green-700 hover:shadow-lg" data-tab="newest" type="button">
                    <i class="fas fa-clock text-lg text-green-500 group-hover:text-green-600"></i>
                    <span>Newest Items</span>
                </button>
            </nav>
        </div>

        <!-- Tabs content -->
        <div id="popular" class="tab-content">
            <p class="text-gray-600 mb-6">Discover our most popular packages based on customer ratings and reviews</p>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($popularPackages as $package)
                    <a href="{{ route('user.package_detail', $package->id) }}" class="group block bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 transform hover:scale-[1.02] hover:-translate-y-2">
                        <div class="relative overflow-hidden">
                            @php
                                $validImages = array_filter((array) ($package->images ?? []), function($img) {
                                    return is_string($img) && !empty($img);
                                });
                            @endphp
                            @if($validImages && count($validImages) > 0)
                                <div class="relative h-32 overflow-hidden">
                                    <img src="{{ asset('storage/' . reset($validImages)) }}" alt="{{ $package->name_package }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                </div>
                            @else
                                <div class="w-full h-32 bg-gradient-to-br from-blue-400 via-indigo-500 to-purple-600 flex items-center justify-center relative overflow-hidden">
                                    <i class="fas fa-box text-white text-3xl animate-bounce"></i>
                                    <div class="absolute inset-0 bg-gradient-to-br from-blue-300/20 to-purple-500/20 animate-pulse"></div>
                                </div>
                            @endif
                            <!-- Enhanced rating badge -->
                            <div class="absolute top-2 right-2 bg-white/95 backdrop-blur-lg rounded-xl px-2 py-1 shadow-lg border border-white/50">
                                <div class="flex items-center space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($package->averageRating()))
                                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                                        @elseif($i - 0.5 <= $package->averageRating())
                                            <i class="fas fa-star-half-alt text-yellow-400 text-xs"></i>
                                        @else
                                            <i class="far fa-star text-gray-300 text-xs"></i>
                                        @endif
                                    @endfor
                                    <span class="text-xs font-bold ml-1 text-gray-800">{{ number_format($package->averageRating(), 1) }}</span>
                                    <span class="text-xs text-gray-600">({{ $package->reviews->count() }})</span>
                                </div>
                            </div>
                            <!-- Best Seller badge -->
                            @if($package->averageRating() >= 4.5)
                                <div class="absolute top-2 left-2 bg-gradient-to-r from-blue-400 to-indigo-500 text-white rounded-full px-2 py-1 text-xs font-bold shadow-lg">
                                    <i class="fas fa-crown mr-1"></i>Best Seller
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-300">{{ $package->name_package }}</h3>
                            <p class="text-gray-600 text-xs mb-3 line-clamp-2 leading-relaxed">{{ Str::limit($package->description, 60) }}</p>
                            <div class="flex items-center justify-between">
                                @if($package->finalPrice < $package->totalPriceBeforeDiscount)
                                    <div class="text-xl font-bold text-blue-600">
                                        <span class="text-xs text-gray-500 line-through mr-1">Rp {{ number_format($package->totalPriceBeforeDiscount, 0, ',', '.') }}</span>
                                        Rp {{ number_format($package->finalPrice, 0, ',', '.') }}
                                        <span class="text-xs bg-red-100 text-red-600 px-1 py-0.5 rounded-full ml-1 font-semibold">
                                            -{{ round((1 - $package->finalPrice / $package->totalPriceBeforeDiscount) * 100) }}%
                                        </span>
                                    </div>
                                @else
                                    <div class="text-xl font-bold text-blue-600">Rp {{ number_format($package->finalPrice, 0, ',', '.') }}</div>
                                @endif
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">per package</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $popularPackages->withQueryString()->links() }}
            </div>
        </div>

        <div id="newest" class="tab-content hidden">
            <p class="text-gray-600 mb-6">Explore our newest packages, ordered from newest to oldest</p>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($newestPackages as $package)
                    <a href="{{ route('user.package_detail', $package->id) }}" class="group block bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 transform hover:scale-[1.02] hover:-translate-y-2">
                        <div class="relative overflow-hidden">
                            @php
                                $validImages = array_filter((array) ($package->images ?? []), function($img) {
                                    return is_string($img) && !empty($img);
                                });
                            @endphp
                            @if($validImages && count($validImages) > 0)
                                <div class="relative h-32 overflow-hidden">
                                    <img src="{{ asset('storage/' . reset($validImages)) }}" alt="{{ $package->name_package }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                </div>
                            @else
                                <div class="w-full h-32 bg-gradient-to-br from-blue-400 via-indigo-500 to-purple-600 flex items-center justify-center relative overflow-hidden">
                                    <i class="fas fa-box text-white text-3xl animate-bounce"></i>
                                    <div class="absolute inset-0 bg-gradient-to-br from-blue-300/20 to-purple-500/20 animate-pulse"></div>
                                </div>
                            @endif
                            <!-- Enhanced rating badge -->
                            <div class="absolute top-2 right-2 bg-white/95 backdrop-blur-lg rounded-xl px-2 py-1 shadow-lg border border-white/50">
                                <div class="flex items-center space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($package->averageRating()))
                                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                                        @elseif($i - 0.5 <= $package->averageRating())
                                            <i class="fas fa-star-half-alt text-yellow-400 text-xs"></i>
                                        @else
                                            <i class="far fa-star text-gray-300 text-xs"></i>
                                        @endif
                                    @endfor
                                    <span class="text-xs font-bold ml-1 text-gray-800">{{ number_format($package->averageRating(), 1) }}</span>
                                    <span class="text-xs text-gray-600">({{ $package->reviews->count() }})</span>
                                </div>
                            </div>
                            <!-- Best Seller badge -->
                            @if($package->averageRating() >= 4.5)
                                <div class="absolute top-2 left-2 bg-gradient-to-r from-blue-400 to-indigo-500 text-white rounded-full px-2 py-1 text-xs font-bold shadow-lg">
                                    <i class="fas fa-crown mr-1"></i>Best Seller
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors duration-300">{{ $package->name_package }}</h3>
                            <p class="text-gray-600 text-xs mb-3 line-clamp-2 leading-relaxed">{{ Str::limit($package->description, 60) }}</p>
                            <div class="flex items-center justify-between">
                                <div class="text-xl font-bold text-blue-600">Rp {{ number_format($package->nta, 0, ',', '.') }}</div>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">per package</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $newestPackages->withQueryString()->links() }}
            </div>
        </div>
    </div>
</section>

<script>
    // Custom animations and effects
    document.addEventListener('DOMContentLoaded', function() {
        // Add custom CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
            @keyframes float-delayed {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-15px); }
            }
            @keyframes fade-in-up {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .animate-float {
                animation: float 3s ease-in-out infinite;
            }
            .animate-float-delayed {
                animation: float-delayed 4s ease-in-out infinite;
                animation-delay: 1s;
            }
            .animate-fade-in-up {
                animation: fade-in-up 0.8s ease-out forwards;
            }
        `;
        document.head.appendChild(style);

        // Advanced filters toggle with smooth animation
        function toggleAdvancedFilters() {
            const filters = document.getElementById('advanced-filters');
            const icon = document.getElementById('advanced-filters-icon');

            if (filters.classList.contains('hidden')) {
                filters.classList.remove('hidden');
                filters.style.maxHeight = '0px';
                setTimeout(() => {
                    filters.style.maxHeight = filters.scrollHeight + 'px';
                }, 10);
                icon.classList.add('rotate-180');
            } else {
                filters.style.maxHeight = '0px';
                setTimeout(() => {
                    filters.classList.add('hidden');
                }, 300);
                icon.classList.remove('rotate-180');
            }
        }

        // Attach toggle function to button
        const advancedFiltersBtn = document.querySelector('button[onclick="toggleAdvancedFilters()"]');
        if (advancedFiltersBtn) {
            advancedFiltersBtn.onclick = toggleAdvancedFilters;
        }

        // Enhanced tab switching with animations
        const tabs = document.querySelectorAll('#packageTabs button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Update tab styles
                tabs.forEach(t => {
                    t.classList.remove('bg-gradient-to-r', 'from-orange-500', 'to-red-500', 'text-white', 'shadow-xl', 'shadow-orange-500/25');
                    t.classList.add('bg-white', 'text-gray-600', 'hover:bg-gradient-to-r', 'hover:from-green-50', 'hover:to-emerald-50', 'hover:text-green-700', 'shadow-lg');
                });

                // Activate clicked tab
                tab.classList.remove('bg-white', 'text-gray-600', 'hover:bg-gradient-to-r', 'hover:from-green-50', 'hover:to-emerald-50', 'hover:text-green-700', 'shadow-lg');
                tab.classList.add('bg-gradient-to-r', 'from-orange-500', 'to-red-500', 'text-white', 'shadow-xl', 'shadow-orange-500/25');

                // Hide all tab contents with fade effect
                tabContents.forEach(content => {
                    content.style.opacity = '0';
                    setTimeout(() => content.classList.add('hidden'), 150);
                });

                // Show corresponding tab content with fade effect
                setTimeout(() => {
                    const tabId = tab.getAttribute('data-tab');
                    const targetContent = document.getElementById(tabId);
                    targetContent.classList.remove('hidden');
                    targetContent.style.opacity = '0';
                    setTimeout(() => {
                        targetContent.style.opacity = '1';
                        targetContent.style.transition = 'opacity 0.3s ease-in-out';
                    }, 10);
                }, 150);
            });
        });

        // Add loading animation for search
        const searchForm = document.querySelector('form[action*="search"]');
        if (searchForm) {
            searchForm.addEventListener('submit', function() {
                const skeletonLoader = document.getElementById('skeleton-loader');
                const resultsGrid = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-3.lg\\:grid-cols-4');

                if (skeletonLoader && resultsGrid) {
                    skeletonLoader.classList.remove('hidden');
                    resultsGrid.style.opacity = '0.3';
                    resultsGrid.style.pointerEvents = 'none';
                }
            });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add intersection observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in-up');
                }
            });
        }, observerOptions);

        // Observe cards for animation
        document.querySelectorAll('.grid.grid-cols-1.md\\:grid-cols-3.lg\\:grid-cols-4 > a').forEach(card => {
            observer.observe(card);
        });
    });
</script>
@endsection
