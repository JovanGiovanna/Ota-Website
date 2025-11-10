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
            <form method="GET" action="{{ route('user.search') }}" class="space-y-6">
                <!-- Basic Search Row -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Destination</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                            </div>
                            <input type="text" name="destination" value="{{ request('destination') }}" placeholder="Where are you going?" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-in</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-alt text-gray-400"></i>
                            </div>
                            <input type="date" name="checkin" value="{{ request('checkin') }}" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-out</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-alt text-gray-400"></i>
                            </div>
                            <input type="date" name="checkout" value="{{ request('checkout') }}" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                            <i class="fas fa-search mr-2"></i>
                            Search
                        </button>
                    </div>
                </div>

                <!-- Advanced Filters (Collapsible) -->
                <div class="border-t pt-4">
                    <button type="button" class="flex items-center justify-between w-full text-left" onclick="toggleAdvancedFilters()">
                        <span class="text-sm font-medium text-gray-700">Advanced Filters</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="advanced-filters-icon"></i>
                    </button>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 hidden" id="advanced-filters">
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

<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @if($searchType == 'all' || $searchType == 'packages')
        @forelse($packages as $package)
            <a href="{{ route('user.package_detail', $package->id) }}" class="block bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                <div class="relative">
                    @if($package->image)
                        <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name_package }}" class="w-full h-32 object-cover">
                    @else
                        <div class="w-full h-32 bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                            <i class="fas fa-box text-white text-2xl"></i>
                        </div>
                    @endif
                    <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm rounded-full px-2 py-1">
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
                            <span class="text-xs font-semibold ml-1">{{ number_format($package->averageRating(), 1) }} ({{ $package->reviews->count() }})</span>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $package->name_package }}</h3>
                    <p class="text-gray-600 text-xs mb-3 line-clamp-2">{{ Str::limit($package->description, 60) }}</p>
                    <div class="flex items-center justify-between">
                        <div class="text-lg font-bold text-blue-600">Rp {{ number_format($package->price_publish, 0, ',', '.') }}</div>
                        <span class="text-xs text-gray-500">per package</span>
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
            <a href="{{ route('user.product_detail', $product->id) }}" class="block bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                <div class="relative">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-32 object-cover">
                    @else
                        <div class="w-full h-32 bg-gradient-to-r from-green-400 to-blue-500 flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-white text-2xl"></i>
                        </div>
                    @endif
                    <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm rounded-full px-2 py-1">
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
                            <span class="text-xs font-semibold ml-1">{{ number_format($product->averageRating(), 1) }} ({{ $product->reviews->count() }})</span>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $product->name }}</h3>
                    <p class="text-gray-600 text-xs mb-3 line-clamp-2">{{ Str::limit($product->description, 60) }}</p>
                    <div class="flex items-center justify-between">
                        <div class="text-lg font-bold text-green-600">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        <span class="text-xs text-gray-500">per unit</span>
                    </div>
                </div>
            </a>
        @empty
            @if($searchType == 'products')
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-shopping-cart text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No products found</h3>
                    <p class="text-gray-500 text-sm">Try adjusting your search criteria</p>
                </div>
            @endif
        @endforelse
    @endif

    @if($searchType == 'all' || $searchType == 'addons')
        @forelse($addons as $addon)
            <a href="{{ route('user.addon_detail', $addon->id) }}" class="block bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                <div class="relative">
                    @if($addon->image)
                        <img src="{{ asset('storage/' . $addon->image) }}" alt="{{ $addon->addons }}" class="w-full h-32 object-cover">
                    @else
                        <div class="w-full h-32 bg-gradient-to-r from-orange-400 to-red-500 flex items-center justify-center">
                            <i class="fas fa-plus-circle text-white text-2xl"></i>
                        </div>
                    @endif
                    <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm rounded-full px-2 py-1">
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
                            <span class="text-xs font-semibold ml-1">{{ number_format($addon->averageRating(), 1) }} ({{ $addon->reviews->count() }})</span>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $addon->addons }}</h3>
                    <p class="text-gray-600 text-xs mb-3 line-clamp-2">{{ Str::limit($addon->desc, 60) }}</p>
                    <div class="flex items-center justify-between">
                        <div class="text-lg font-bold text-orange-600">Rp {{ number_format($addon->price, 0, ',', '.') }}</div>
                        <span class="text-xs text-gray-500">per unit</span>
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

<script>
    function toggleAdvancedFilters() {
        const filters = document.getElementById('advanced-filters');
        const icon = document.getElementById('advanced-filters-icon');

        if (filters.classList.contains('hidden')) {
            filters.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            filters.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Get all star rating containers
        const starRatingContainers = document.querySelectorAll('.star-rating');

        starRatingContainers.forEach(container => {
            const stars = container.querySelectorAll('.star');
            const radioButtons = container.querySelectorAll('input[type="radio"]');

            function updateStars(rating) {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.remove('text-gray-300');
                        star.classList.add('text-yellow-500');
                    } else {
                        star.classList.remove('text-yellow-500');
                        star.classList.add('text-gray-300');
                    }
                });
            }

            // Initialize with the checked radio button
            const checkedRadio = container.querySelector('input[type="radio"]:checked');
            if (checkedRadio) {
                updateStars(parseInt(checkedRadio.value));
            }
        });
    });
</script>
@endsection
