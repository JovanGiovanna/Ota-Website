<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pointer - Travel & Booking Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        .hero-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg fixed top-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-compass text-white text-lg"></i>
                        </div>
                        <span class="text-2xl font-bold text-gray-800">Pointer</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 shadow-md">
                        Sign Up
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-bg pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-6">
                    Discover Amazing
                    <span class="block text-yellow-300">Travel Experiences</span>
                </h1>
                <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                    Find and book the best hotel packages, products, and add-ons for your perfect journey. Your adventure starts here.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-blue-50 transition-all duration-200 shadow-lg">
                        <i class="fas fa-rocket mr-2"></i>
                        Start Your Journey
                    </a>
                    <a href="#features" class="border-2 border-white text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-white hover:text-blue-600 transition-all duration-200">
                        <i class="fas fa-info-circle mr-2"></i>
                        Learn More
                    </a>
                </div>
            </div>
            <div class="mt-16 flex justify-center">
                <div class="floating">
                    <div class="w-32 h-32 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-plane text-white text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Items Section -->
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Popular Items</h2>
            <p class="text-gray-600 mb-8">Discover our most popular packages based on customer ratings and reviews</p>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($popularPackages as $package)
                    <a href="{{ route('user.package_detail', $package->id) }}" class="block bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                        <div class="relative">
                            @php
                                $validImages = array_filter((array) ($package->images ?? []), function($img) {
                                    return is_string($img) && !empty($img);
                                });
                            @endphp
                            @if($validImages && count($validImages) > 0)
                                <div class="relative h-32 overflow-hidden">
                                    <img src="{{ asset('storage/' . reset($validImages)) }}" alt="{{ $package->name_package }}" class="w-full h-full object-cover">
                                </div>
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
                                <div class="text-lg font-bold text-blue-600">Rp {{ number_format($package->nta, 0, ',', '.') }}</div>
                                <span class="text-xs text-gray-500">per package</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $popularPackages->withQueryString()->links() }}
            </div>
        </div>
    </section>

    <!-- Newest Items Section -->
    <section class="py-12 bg-gradient-to-r from-blue-50 to-indigo-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">Newest Items</h2>
            <p class="text-gray-600 mb-8">Explore our newest packages, ordered from newest to oldest</p>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($newestPackages as $package)
                    <a href="{{ route('user.package_detail', $package->id) }}" class="block bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                        <div class="relative">
                            @php
                                $validImages = array_filter((array) ($package->images ?? []), function($img) {
                                    return is_string($img) && !empty($img);
                                });
                            @endphp
                            @if($validImages && count($validImages) > 0)
                                <div class="relative h-32 overflow-hidden">
                                    <img src="{{ asset('storage/' . reset($validImages)) }}" alt="{{ $package->name_package }}" class="w-full h-full object-cover">
                                </div>
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
                                <div class="text-lg font-bold text-blue-600">Rp {{ number_format($package->nta, 0, ',', '.') }}</div>
                                <span class="text-xs text-gray-500">per package</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            
            <div class="mt-6">
                {{ $newestPackages->withQueryString()->links() }}
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Why Choose Pointer?</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    We provide comprehensive travel solutions with premium services and unforgettable experiences.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-hotel text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Premium Hotels</h3>
                    <p class="text-gray-600">
                        Access to luxury accommodations with world-class amenities and exceptional service.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-plus-circle text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Add-on Services</h3>
                    <p class="text-gray-600">
                        Enhance your experience with spa treatments, airport transfers, and guided tours.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-edit text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Custom Bookings</h3>
                    <p class="text-gray-600">
                        Tailor-made bookings designed for your specific needs and preferences.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Secure Booking</h3>
                    <p class="text-gray-600">
                        Safe and secure booking process with 24/7 customer support and money-back guarantee.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-gradient-to-br from-red-50 to-pink-50 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-r from-red-500 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-star text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Top Reviews</h3>
                    <p class="text-gray-600">
                        Read genuine reviews from our satisfied customers and make informed decisions.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-clock text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">24/7 Support</h3>
                    <p class="text-gray-600">
                        Round-the-clock customer support to assist you whenever you need help.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-800 py-20">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-white mb-4">Ready to Start Your Adventure?</h2>
            <p class="text-xl text-blue-100 mb-8">
                Join thousands of travelers who trust Pointer for their perfect getaway.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="bg-white text-blue-600 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-blue-50 transition-all duration-200 shadow-lg">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Account
                </a>
                <a href="{{ route('login') }}" class="border-2 border-white text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-white hover:text-blue-600 transition-all duration-200">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </a>
            </div>
        </div>
    </section>
</body>
</html>
