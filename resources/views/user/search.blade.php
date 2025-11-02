@extends('layouts.user')

@section('title', 'Search')

@section('welcome')
Find your perfect packages, products, and add-ons!
@endsection

@section('content')
<!-- Search Hero Section -->
<div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-800 rounded-2xl p-8 mb-8 text-white shadow-2xl">
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-4xl font-bold mb-4">Find Your Perfect Package</h1>
        <p class="text-blue-100 text-lg mb-8">Discover amazing packages with products and add-ons for your next adventure</p>

        <!-- Advanced Search Form -->
        <div class="bg-white rounded-2xl p-6 shadow-xl">
            <form method="GET" action="{{ route('user.search') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Destination</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-map-marker-alt text-gray-400"></i>
                        </div>
                        <input type="text" name="destination" placeholder="Where are you going?" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-in</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                        </div>
                        <input type="date" name="checkin" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-out</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                        </div>
                        <input type="date" name="checkout" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                        <i class="fas fa-search mr-2"></i>
                        Search
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Type Tabs -->
<div class="flex flex-wrap gap-2 mb-8 bg-white rounded-xl p-2 shadow-md">
    <a href="{{ route('user.search', array_merge(request()->query(), ['type' => 'all'])) }}"
       class="px-6 py-3 rounded-lg font-medium transition-all duration-200 {{ $searchType == 'all' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-gray-100' }}">
        <i class="fas fa-th-large mr-2"></i>
        All
    </a>
    <a href="{{ route('user.search', array_merge(request()->query(), ['type' => 'packages'])) }}"
       class="px-6 py-3 rounded-lg font-medium transition-all duration-200 {{ $searchType == 'packages' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-gray-100' }}">
        <i class="fas fa-box mr-2"></i>
        Packages
    </a>
    <a href="{{ route('user.search', array_merge(request()->query(), ['type' => 'products'])) }}"
       class="px-6 py-3 rounded-lg font-medium transition-all duration-200 {{ $searchType == 'products' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-gray-100' }}">
        <i class="fas fa-shopping-bag mr-2"></i>
        Products
    </a>
    <a href="{{ route('user.search', array_merge(request()->query(), ['type' => 'addons'])) }}"
       class="px-6 py-3 rounded-lg font-medium transition-all duration-200 {{ $searchType == 'addons' ? 'bg-blue-600 text-white shadow-lg' : 'text-gray-700 hover:bg-gray-100' }}">
        <i class="fas fa-plus-circle mr-2"></i>
        Add-ons
    </a>
</div>

<!-- Quick Filters -->
<div class="flex flex-wrap gap-4 mb-8">
    <button class="px-6 py-3 bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition-all duration-200 flex items-center space-x-2">
        <i class="fas fa-star text-yellow-500"></i>
        <span class="font-medium">Top Rated</span>
    </button>
    <button class="px-6 py-3 bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition-all duration-200 flex items-center space-x-2">
        <i class="fas fa-dollar-sign text-green-500"></i>
        <span class="font-medium">Budget Friendly</span>
    </button>
    <button class="px-6 py-3 bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition-all duration-200 flex items-center space-x-2">
        <i class="fas fa-crown text-purple-500"></i>
        <span class="font-medium">Luxury</span>
    </button>
    <button class="px-6 py-3 bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition-all duration-200 flex items-center space-x-2">
        <i class="fas fa-utensils text-orange-500"></i>
        <span class="font-medium">All Inclusive</span>
    </button>
</div>

<!-- Search Results -->
@if(request()->has('destination') || request()->has('checkin'))
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Search Results</h2>
        @if($searchType == 'all' || $searchType == 'packages')
            <p class="text-gray-600">Found {{ $packages->count() }} packages, {{ $products->count() }} products, {{ $addons->count() }} add-ons matching your criteria</p>
        @elseif($searchType == 'products')
            <p class="text-gray-600">Found {{ $products->count() }} products matching your criteria</p>
        @elseif($searchType == 'addons')
            <p class="text-gray-600">Found {{ $addons->count() }} add-ons matching your criteria</p>
        @endif
    </div>
@else
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Popular Items</h2>
        <p class="text-gray-600">Explore our most popular packages, products, and add-ons</p>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    @if($searchType == 'all' || $searchType == 'packages')
        @forelse($packages as $package)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="relative">
                    @if($package->image)
                        <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name_package }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                            <i class="fas fa-box text-white text-4xl"></i>
                        </div>
                    @endif
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-star text-yellow-500"></i>
                            <span class="text-sm font-semibold">{{ number_format($package->averageRating(), 1) }}</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $package->name_package }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($package->description, 100) }}</p>
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-2xl font-bold text-blue-600">Rp {{ number_format($package->price_publish, 0, ',', '.') }}</div>
                        <span class="text-sm text-gray-500">per night</span>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('user.product_detail', $package->slug) }}" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-200 transition-all duration-200 text-center">
                            View Details
                        </a>
                        <a href="{{ route('user.form_booker', ['package' => $package->id]) }}" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-all duration-200 text-center">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
        @empty
            @if($searchType == 'packages')
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-box text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No packages found</h3>
                    <p class="text-gray-500">Try adjusting your search criteria</p>
                </div>
            @endif
        @endforelse
    @endif

    @if($searchType == 'all' || $searchType == 'products')
        @forelse($products as $product)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="relative">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-r from-green-400 to-blue-500 flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-white text-4xl"></i>
                        </div>
                    @endif
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1">
                        <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">{{ $product->category->categories ?? 'Product' }}</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $product->name }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($product->description, 100) }}</p>
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-2xl font-bold text-green-600">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        <span class="text-sm text-gray-500">per unit</span>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('user.product_detail', $product->id) }}" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-200 transition-all duration-200 text-center">
                            View Details
                        </a>
                        <a href="{{ route('user.form_booker', ['product' => $product->id]) }}" class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-700 transition-all duration-200 text-center">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
        @empty
            @if($searchType == 'products')
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No products found</h3>
                    <p class="text-gray-500">Try adjusting your search criteria</p>
                </div>
            @endif
        @endforelse
    @endif

    @if($searchType == 'all' || $searchType == 'addons')
        @forelse($addons as $addon)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
                <div class="relative">
                    @if($addon->image)
                        <img src="{{ asset('storage/' . $addon->image) }}" alt="{{ $addon->addons }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gradient-to-r from-orange-400 to-red-500 flex items-center justify-center">
                            <i class="fas fa-plus-circle text-white text-4xl"></i>
                        </div>
                    @endif
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1">
                        <span class="text-xs font-semibold text-orange-600 bg-orange-100 px-2 py-1 rounded-full">Addon</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $addon->addons }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($addon->desc, 100) }}</p>
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-2xl font-bold text-orange-600">Rp {{ number_format($addon->price, 0, ',', '.') }}</div>
                        <span class="text-sm text-gray-500">per unit</span>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('user.addon_detail', $addon->id) }}" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-200 transition-all duration-200 text-center">
                            View Details
                        </a>
                        <a href="{{ route('user.form_booker', ['addon' => $addon->id]) }}" class="flex-1 bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-orange-700 transition-all duration-200 text-center">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
        @empty
            @if($searchType == 'addons')
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-plus-circle text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No add-ons found</h3>
                    <p class="text-gray-500">Try adjusting your search criteria</p>
                </div>
            @endif
        @endforelse
    @endif
</div>

<!-- Load More Button -->
@if((count($packages ?? []) > 0 && $packages->count() >= 9) || (!request()->has('destination') && !request()->has('checkin')))
    <div class="text-center mt-12">
        <button class="bg-white border-2 border-blue-600 text-blue-600 px-8 py-4 rounded-xl font-semibold hover:bg-blue-600 hover:text-white transition-all duration-200 shadow-lg">
            Load More Packages
        </button>
    </div>
@endif
@endsection
