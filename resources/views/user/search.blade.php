@extends('layouts.user')

@section('title', 'Search')

@section('welcome')
Find your perfect packages, products, and add-ons!
@endsection

@section('content')
<!-- Search Hero Section -->
<div class="relative bg-cover bg-center text-white overflow-hidden" style="background-image: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80'); background-size: cover; background-position: center; background-attachment: fixed; height: 100vh; width: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; position: relative;">
    
    <div class="relative max-w-4xl mx-auto text-center z-50 px-4 w-full">
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4 leading-tight">
            Your Journey, Our Priority:<br>
            <span class="text-2xl md:text-3xl lg:text-4xl">
                Ensuring Every Trip is Hassle-Free
            </span>
        </h1>

        <!-- Search Box -->
        <div class="mt-12 max-w-2xl mx-auto">
            <form method="GET" action="{{ route('user.search') }}" class="space-y-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <input type="text" 
                               name="destination" 
                               placeholder="Search By City, Package, or Category" 
                               value="{{ request('destination') }}"
                               class="w-full px-6 py-4 rounded-full text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-lg">
                        <i class="fas fa-map-marker-alt absolute right-6 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-full font-semibold transition-all duration-200 shadow-lg hover:shadow-xl whitespace-nowrap flex items-center justify-center gap-2">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                </div>

                <!-- Advance Filter Link -->
                <div class="text-center relative z-50">
                    <button type="button" id="advanceFilterBtn" class="text-white hover:text-blue-200 font-medium transition-colors duration-200 cursor-pointer px-4 py-2 rounded">
                        <i class="fas fa-filter mr-2"></i>Advance Filter
                    </button>
                </div>

                <!-- Advanced Filters (Collapsible) -->
                <div id="advanced-filters" class="hidden border-t border-white/20 pt-6 transition-all duration-300">
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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

<!-- Best Deals Section -->
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-100 rounded-xl p-3">
                    <i class="fas fa-ticket-alt text-blue-600 text-2xl"></i>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Best deals for a price-less travel!</h2>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" aria-label="Scroll left" data-target="deals-slider" data-dir="left" class="slider-arrow bg-white text-gray-700 hover:text-blue-600 hover:bg-gray-50 border border-gray-200 rounded-full w-10 h-10 flex items-center justify-center shadow-md transition-all">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button" aria-label="Scroll right" data-target="deals-slider" data-dir="right" class="slider-arrow bg-white text-gray-700 hover:text-blue-600 hover:bg-gray-50 border border-gray-200 rounded-full w-10 h-10 flex items-center justify-center shadow-md transition-all">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex flex-wrap gap-2 mb-6">
            <button class="deals-tab active px-6 py-3 rounded-full font-semibold text-white bg-blue-500 hover:bg-blue-600 transition-all flex items-center space-x-2 shadow-md">
                <i class="fas fa-plane"></i>
                <span>Flight</span>
            </button>
            <button class="deals-tab px-6 py-3 rounded-full font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all flex items-center space-x-2">
                <i class="fas fa-hotel"></i>
                <span>Hotels</span>
            </button>
            <button class="deals-tab px-6 py-3 rounded-full font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all flex items-center space-x-2">
                <i class="fas fa-bus"></i>
                <span>Bus & Travel</span>
            </button>
            <button class="deals-tab px-6 py-3 rounded-full font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all flex items-center space-x-2">
                <i class="fas fa-car"></i>
                <span>Cars</span>
            </button>
            <button class="deals-tab px-6 py-3 rounded-full font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all flex items-center space-x-2">
                <i class="fas fa-map-marked-alt"></i>
                <span>Things to Do</span>
            </button>
        </div>

        <!-- Deals Slider -->
        <div id="deals-slider" class="flex overflow-x-auto gap-6 pb-4 snap-x snap-mandatory hide-scrollbar">
            <!-- Deal Card 1 -->
            <div class="group flex-none w-80 md:w-[380px] bg-gradient-to-br from-indigo-900 via-blue-900 to-indigo-800 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] snap-start">
                <div class="relative h-72 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=800" alt="Flight Deal" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                    <div class="absolute top-4 left-4 flex items-center space-x-3">
                        <div class="bg-white rounded-lg px-3 py-1.5">
                            <span class="text-sm font-bold text-indigo-900">Sriwijaya Air</span>
                        </div>
                        <div class="bg-white rounded-lg px-3 py-1.5">
                            <span class="text-sm font-bold text-blue-900">ANA AIR</span>
                        </div>
                    </div>
                    <div class="absolute bottom-4 left-4 right-4 text-white">
                        <h3 class="text-2xl font-bold mb-2">Rencana matang, kompet senang</h3>
                        <p class="text-sm mb-3">Pesan lebih awal, lebih hemat s.d. 25%</p>
                        <button class="bg-white text-indigo-900 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-100 transition-colors">S&K berlaku</button>
                    </div>
                </div>
            </div>

            <!-- Deal Card 2 -->
            <div class="group flex-none w-80 md:w-[380px] bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-600 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] snap-start">
                <div class="relative h-72 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1464037866556-6812c9d1c72e?w=800" alt="Melbourne Deal" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                    <div class="absolute top-4 right-4 bg-yellow-400 rounded-lg px-3 py-1.5">
                        <span class="text-xs font-bold text-gray-900">MELBOURNE AIRPORT</span>
                    </div>
                    <div class="absolute bottom-4 left-4 right-4 text-white">
                        <h3 class="text-2xl font-bold mb-2">Rasakan irama kota Melbourne</h3>
                        <p class="text-sm mb-1">Diskon <span class="font-bold">Rp600rb</span></p>
                        <p class="text-sm font-bold mb-3">+ Gratis Airport Transfer</p>
                        <button class="bg-white text-blue-900 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-100 transition-colors">S&K berlaku</button>
                    </div>
                </div>
            </div>

            <!-- Deal Card 3 -->
            <div class="group flex-none w-80 md:w-[380px] bg-gradient-to-br from-red-600 via-red-700 to-red-800 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] snap-start">
                <div class="relative h-72 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=800" alt="AirAsia Deal" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                    <div class="absolute top-4 left-4 bg-yellow-400 rounded-full px-3 py-1.5">
                        <span class="text-xs font-bold text-gray-900">PTO Promo Terbang Oke!</span>
                    </div>
                    <div class="absolute bottom-4 left-4 right-4 text-white">
                        <h3 class="text-2xl font-bold mb-2">Saatnya wujudin bucket-list liburanmu</h3>
                        <p class="text-sm mb-3">Cashback s.d. <span class="font-bold">10rb</span> naik AirAsia</p>
                        <button class="bg-white text-red-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-100 transition-colors whitespace-nowrap">S&K berlaku</button>
                    </div>
                </div>
            </div>

            <!-- Deal Card 4 -->
            <div class="group flex-none w-80 md:w-[380px] bg-gradient-to-br from-orange-500 via-red-500 to-pink-500 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] snap-start">
                <div class="relative h-72 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=800" alt="Promo Deal" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4 text-white">
                        <h3 class="text-2xl font-bold mb-2">Promo Spesial Akhir Tahun</h3>
                        <p class="text-sm mb-3">Diskon hingga <span class="font-bold">50%</span> untuk semua rute domestik</p>
                        <button class="bg-white text-red-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-100 transition-colors">S&K berlaku</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="#" class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-700 font-semibold text-lg group transition-colors">
                <span>See All Promos</span>
                <i class="fas fa-chevron-right group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>
</section>

<!-- Indonesia Destinations Section -->
<section class="bg-gradient-to-b from-gray-50 to-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="inline-flex items-center space-x-2 mb-4">
                <i class="fas fa-globe-asia text-3xl text-blue-600"></i>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Rediscover yourself in Indonesia</h2>
            <p class="text-gray-600 text-lg">Explore the beauty and diversity of the archipelago</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="{{ route('user.search', ['destination' => 'Bali']) }}" class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] h-72">
                <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800" alt="Bali" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <h3 class="text-2xl font-bold mb-2">Bali</h3>
                    <p class="text-sm text-white/90">Island of Gods</p>
                </div>
            </a>

            <a href="{{ route('user.search', ['destination' => 'Jakarta']) }}" class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] h-72">
                <img src="https://images.unsplash.com/photo-1555899434-94d1eb5c7e38?w=800" alt="Jakarta" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <h3 class="text-2xl font-bold mb-2">Jakarta</h3>
                    <p class="text-sm text-white/90">Capital City</p>
                </div>
            </a>

            <a href="{{ route('user.search', ['destination' => 'Yogyakarta']) }}" class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] h-72">
                <img src="https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=800" alt="Yogyakarta" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <h3 class="text-2xl font-bold mb-2">Yogyakarta</h3>
                    <p class="text-sm text-white/90">Cultural Heart</p>
                </div>
            </a>

            <a href="{{ route('user.search', ['destination' => 'Lombok']) }}" class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] h-72">
                <img src="https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800" alt="Lombok" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <h3 class="text-2xl font-bold mb-2">Lombok</h3>
                    <p class="text-sm text-white/90">Paradise Island</p>
                </div>
            </a>

            <a href="{{ route('user.search', ['destination' => 'Bandung']) }}" class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] h-72">
                <img src="https://images.unsplash.com/photo-1588668214407-6ea9a6d8c272?w=800" alt="Bandung" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <h3 class="text-2xl font-bold mb-2">Bandung</h3>
                    <p class="text-sm text-white/90">Paris of Java</p>
                </div>
            </a>

            <a href="{{ route('user.search', ['destination' => 'Raja Ampat']) }}" class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] h-72">
                <img src="https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800" alt="Raja Ampat" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <h3 class="text-2xl font-bold mb-2">Raja Ampat</h3>
                    <p class="text-sm text-white/90">Diving Paradise</p>
                </div>
            </a>
        </div>
    </div>
</section>

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
            .hide-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            .hide-scrollbar::-webkit-scrollbar {
                display: none;
            }
        `;
        document.head.appendChild(style);

        // Advanced filters toggle with smooth animation
        console.log('Script loaded, searching for elements...');
        
        const advanceBtn = document.getElementById('advanceFilterBtn');
        const filters = document.getElementById('advanced-filters');
        
        console.log('Found button:', advanceBtn);
        console.log('Found filters:', filters);
        
        if (advanceBtn && filters) {
            console.log('Both elements found, attaching event listener...');
            advanceBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Button clicked!');
                
                if (filters.classList.contains('hidden')) {
                    filters.classList.remove('hidden');
                    console.log('Showing filters');
                } else {
                    filters.classList.add('hidden');
                    console.log('Hiding filters');
                }
            });
            console.log('Event listener attached successfully!');
        } else {
            console.error('Elements not found! Button:', advanceBtn, 'Filters:', filters);
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

        // Setup horizontal sliders for deals and articles
        function setupHorizontalSlider(id) {
            const slider = document.getElementById(id);
            if (!slider) return;

            const leftBtn = document.querySelector(`[data-target="${id}"][data-dir="left"]`);
            const rightBtn = document.querySelector(`[data-target="${id}"][data-dir="right"]`);
            const scrollStep = () => Math.max(slider.clientWidth * 0.9, 300);
            const scrollByDir = (dir) => slider.scrollBy({ left: dir * scrollStep(), behavior: 'smooth' });

            leftBtn?.addEventListener('click', () => scrollByDir(-1));
            rightBtn?.addEventListener('click', () => scrollByDir(1));
        }

        ['deals-slider', 'articles-slider'].forEach(setupHorizontalSlider);

        // Deals tabs switching
        const dealsTabs = document.querySelectorAll('.deals-tab');
        dealsTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                dealsTabs.forEach(t => {
                    t.classList.remove('bg-blue-500', 'text-white');
                    t.classList.add('bg-gray-100', 'text-gray-600');
                });
                tab.classList.remove('bg-gray-100', 'text-gray-600');
                tab.classList.add('bg-blue-500', 'text-white');
            });
        });
    });
</script>

<!-- Articles Section -->
<section class="bg-gradient-to-b from-blue-50 to-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-100 rounded-xl p-3">
                    <i class="fas fa-book-open text-blue-600 text-2xl"></i>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Read on and kickstart your adventure</h2>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" aria-label="Scroll left" data-target="articles-slider" data-dir="left" class="slider-arrow bg-white text-gray-700 hover:text-blue-600 hover:bg-gray-50 border border-gray-200 rounded-full w-10 h-10 flex items-center justify-center shadow-md transition-all">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button" aria-label="Scroll right" data-target="articles-slider" data-dir="right" class="slider-arrow bg-white text-gray-700 hover:text-blue-600 hover:bg-gray-50 border border-gray-200 rounded-full w-10 h-10 flex items-center justify-center shadow-md transition-all">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <!-- Articles Slider -->
        <div id="articles-slider" class="flex overflow-x-auto gap-6 pb-4 snap-x snap-mandatory hide-scrollbar">
            <a href="#" class="group flex-none w-80 bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] snap-start">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1598808503491-dd78df51c1f1?w=800" alt="Chiang Mai" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">A Guide: The Best Time to Visit Chiang Mai</h3>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center space-x-1"><i class="fas fa-user-circle"></i><span>Traveloka Team</span></span>
                        <span class="flex items-center space-x-1"><i class="far fa-clock"></i><span>4 min read</span></span>
                    </div>
                </div>
            </a>

            <a href="#" class="group flex-none w-80 bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] snap-start">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=800" alt="Sulawesi" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">Rumah Adat Sulawesi Tengah: Mengenal Jenis, Sejarah, dan Keunikannya</h3>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center space-x-1"><i class="fas fa-user-circle"></i><span>Travel Bestie</span></span>
                        <span class="flex items-center space-x-1"><i class="far fa-clock"></i><span>4 min read</span></span>
                    </div>
                </div>
            </a>

            <a href="#" class="group flex-none w-80 bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] snap-start">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1591825729269-caeb344f6df2?w=800" alt="Bengkulu" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">Rumah Adat Bengkulu: Mengupas Sejarah, Ciri Khas, dan Makna</h3>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center space-x-1"><i class="fas fa-user-circle"></i><span>Travel Bestie</span></span>
                        <span class="flex items-center space-x-1"><i class="far fa-clock"></i><span>3 min read</span></span>
                    </div>
                </div>
            </a>

            <a href="#" class="group flex-none w-80 bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] snap-start">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1512389142860-9c449e58a543?w=800" alt="December" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">28 Desember Memperingati Hari Apa? Ketahui Daftar Lengkapnya</h3>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center space-x-1"><i class="fas fa-user-circle"></i><span>Travel Bestie</span></span>
                        <span class="flex items-center space-x-1"><i class="far fa-clock"></i><span>4 min read</span></span>
                    </div>
                </div>
            </a>

            <a href="#" class="group flex-none w-80 bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] snap-start">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=800" alt="Travel Tips" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">10 Tips Traveling Hemat untuk Liburan Impianmu</h3>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center space-x-1"><i class="fas fa-user-circle"></i><span>Travel Bestie</span></span>
                        <span class="flex items-center space-x-1"><i class="far fa-clock"></i><span>5 min read</span></span>
                    </div>
                </div>
            </a>

            <a href="#" class="group flex-none w-80 bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:scale-[1.02] snap-start">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800" alt="Hidden Gems" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">Hidden Gems Indonesia: Destinasi Wisata yang Belum Banyak Diketahui</h3>
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span class="flex items-center space-x-1"><i class="fas fa-user-circle"></i><span>Travel Bestie</span></span>
                        <span class="flex items-center space-x-1"><i class="far fa-clock"></i><span>6 min read</span></span>
                    </div>
                </div>
            </a>
        </div>

        <div class="text-center mt-8">
            <a href="#" class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-700 font-semibold text-lg group transition-colors">
                <span>Read Inspiring Articles</span>
                <i class="fas fa-chevron-right group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>
</section>
@endsection
