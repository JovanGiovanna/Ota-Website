@extends('layouts.user')

@section('title', 'Search Hotels & Packages')

@section('welcome')
Find your perfect stay!
@endsection

@section('content')
<!-- Search Hero Section -->
<div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-800 rounded-2xl p-8 mb-8 text-white shadow-2xl">
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-4xl font-bold mb-4">Find Your Perfect Stay</h1>
        <p class="text-blue-100 text-lg mb-8">Discover amazing hotels and packages for your next adventure</p>

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
        <p class="text-gray-600">Found {{ count($hotels ?? []) }} hotels matching your criteria</p>
    </div>
@else
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Popular Destinations</h2>
        <p class="text-gray-600">Explore our most popular hotels and packages</p>
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <!-- Hotel Cards -->
    @forelse($hotels ?? [] as $hotel)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
            <div class="relative">
                <img src="{{ $hotel->image ?? 'https://via.placeholder.com/400x250' }}" alt="{{ $hotel->name }}" class="w-full h-48 object-cover">
                <div class="absolute top-4 right-4">
                    <div class="bg-white/90 backdrop-blur-sm rounded-full px-3 py-1 flex items-center space-x-1">
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <span class="text-sm font-semibold">{{ $hotel->rating ?? 4.5 }}</span>
                    </div>
                </div>
                @if($hotel->featured ?? false)
                    <div class="absolute top-4 left-4">
                        <span class="bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded-full">Featured</span>
                    </div>
                @endif
            </div>
            <div class="p-6">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="text-xl font-bold text-gray-800">{{ $hotel->name }}</h3>
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= ($hotel->rating ?? 4) ? 'text-yellow-500' : 'text-gray-300' }} text-sm"></i>
                        @endfor
                    </div>
                </div>
                <div class="flex items-center text-gray-600 mb-3">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    <span class="text-sm">{{ $hotel->location ?? 'Jakarta, Indonesia' }}</span>
                </div>
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $hotel->description ?? 'Beautiful hotel with amazing amenities and comfortable rooms.' }}</p>

                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        @if($hotel->wifi ?? true)
                            <div class="flex items-center">
                                <i class="fas fa-wifi mr-1"></i>
                                <span>WiFi</span>
                            </div>
                        @endif
                        @if($hotel->pool ?? true)
                            <div class="flex items-center">
                                <i class="fas fa-swimming-pool mr-1"></i>
                                <span>Pool</span>
                            </div>
                        @endif
                        @if($hotel->gym ?? true)
                            <div class="flex items-center">
                                <i class="fas fa-dumbbell mr-1"></i>
                                <span>Gym</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-3xl font-bold text-blue-600">Rp {{ number_format($hotel->price ?? 150000, 0, ',', '.') }}</span>
                        <span class="text-gray-500 text-sm">/night</span>
                    </div>
                    <a href="{{ route('user.form_booker', ['hotel' => $hotel->id ?? 1]) }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                        Book Now
                    </a>
                </div>
            </div>
        </div>
    @empty
        <!-- Sample Hotel Cards -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400" alt="Grand Hotel Jakarta" class="w-full h-48 object-cover">
                <div class="absolute top-4 right-4">
                    <div class="bg-white/90 backdrop-blur-sm rounded-full px-3 py-1 flex items-center space-x-1">
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <span class="text-sm font-semibold">4.8</span>
                    </div>
                </div>
                <div class="absolute top-4 left-4">
                    <span class="bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded-full">Featured</span>
                </div>
            </div>
            <div class="p-6">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="text-xl font-bold text-gray-800">Grand Hotel Jakarta</h3>
                    <div class="flex items-center space-x-1">
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <i class="fas fa-star-half-alt text-yellow-500 text-sm"></i>
                    </div>
                </div>
                <div class="flex items-center text-gray-600 mb-3">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    <span class="text-sm">Jakarta Pusat, Indonesia</span>
                </div>
                <p class="text-gray-600 text-sm mb-4">Luxury 5-star hotel in the heart of Jakarta with world-class amenities and exceptional service.</p>

                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-wifi mr-1"></i>
                            <span>WiFi</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-swimming-pool mr-1"></i>
                            <span>Pool</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-dumbbell mr-1"></i>
                            <span>Gym</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-3xl font-bold text-blue-600">Rp 1.250.000</span>
                        <span class="text-gray-500 text-sm">/night</span>
                    </div>
                    <a href="{{ route('user.form_booker') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                        Book Now
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=400" alt="Bali Paradise Resort" class="w-full h-48 object-cover">
                <div class="absolute top-4 right-4">
                    <div class="bg-white/90 backdrop-blur-sm rounded-full px-3 py-1 flex items-center space-x-1">
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <span class="text-sm font-semibold">4.6</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="text-xl font-bold text-gray-800">Bali Paradise Resort</h3>
                    <div class="flex items-center space-x-1">
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <i class="fas fa-star-half-alt text-yellow-500 text-sm"></i>
                    </div>
                </div>
                <div class="flex items-center text-gray-600 mb-3">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    <span class="text-sm">Nusa Dua, Bali</span>
                </div>
                <p class="text-gray-600 text-sm mb-4">Stunning beachfront resort with private villas, infinity pools, and breathtaking ocean views.</p>

                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-wifi mr-1"></i>
                            <span>WiFi</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-swimming-pool mr-1"></i>
                            <span>Pool</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-spa mr-1"></i>
                            <span>Spa</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-3xl font-bold text-blue-600">Rp 850.000</span>
                        <span class="text-gray-500 text-sm">/night</span>
                    </div>
                    <a href="{{ route('user.form_booker') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                        Book Now
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300">
            <div class="relative">
                <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=400" alt="Yogyakarta Heritage Hotel" class="w-full h-48 object-cover">
                <div class="absolute top-4 right-4">
                    <div class="bg-white/90 backdrop-blur-sm rounded-full px-3 py-1 flex items-center space-x-1">
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <span class="text-sm font-semibold">4.4</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="text-xl font-bold text-gray-800">Yogyakarta Heritage Hotel</h3>
                    <div class="flex items-center space-x-1">
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <i class="fas fa-star text-gray-300 text-sm"></i>
                    </div>
                </div>
                <div class="flex items-center text-gray-600 mb-3">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    <span class="text-sm">Yogyakarta, Indonesia</span>
                </div>
                <p class="text-gray-600 text-sm mb-4">Charming heritage hotel blending traditional Javanese architecture with modern comfort.</p>

                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-wifi mr-1"></i>
                            <span>WiFi</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-utensils mr-1"></i>
                            <span>Restaurant</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-parking mr-1"></i>
                            <span>Parking</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-3xl font-bold text-blue-600">Rp 450.000</span>
                        <span class="text-gray-500 text-sm">/night</span>
                    </div>
                    <a href="{{ route('user.form_booker') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                        Book Now
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

<!-- Load More Button -->
@if((count($hotels ?? []) > 0 && count($hotels) >= 9) || (!request()->has('destination') && !request()->has('checkin')))
    <div class="text-center mt-12">
        <button class="bg-white border-2 border-blue-600 text-blue-600 px-8 py-4 rounded-xl font-semibold hover:bg-blue-600 hover:text-white transition-all duration-200 shadow-lg">
            Load More Hotels
        </button>
    </div>
@endif
@endsection
