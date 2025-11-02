@extends('layouts.user')

@section('title', 'Home')

@section('welcome')
Welcome back, {{ Auth::user()->name ?? 'Traveler' }}!
@endsection

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-800 rounded-2xl p-8 mb-8 text-white shadow-2xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2">Discover Amazing Packages</h1>
            <p class="text-blue-100">Find and book the best packages with products and add-ons for your perfect experience.</p>
            <a href="{{ route('user.search') }}" class="inline-flex items-center mt-4 bg-white text-blue-600 px-6 py-3 rounded-xl font-semibold hover:bg-blue-50 transition-all duration-200 shadow-lg">
                <i class="fas fa-search mr-2"></i>
                Start Exploring
            </a>
        </div>
        <div class="hidden md:block">
            <div class="w-24 h-24 bg-white/10 rounded-full flex items-center justify-center">
                <i class="fas fa-compass text-4xl text-white"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-blue-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                <p class="text-3xl font-bold text-blue-600">{{ Auth::check() && Auth::user()->bookings ? Auth::user()->bookings->count() : 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center">
            <span class="text-green-500 text-sm font-medium">+{{ rand(5, 25) }}%</span>
            <span class="text-gray-500 text-sm ml-2">from last month</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-lg border border-green-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Spent</p>
                <p class="text-3xl font-bold text-green-600">Rp {{ number_format(Auth::check() && Auth::user()->bookings ? Auth::user()->bookings->sum('total_price') : 0, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center">
            <span class="text-green-500 text-sm font-medium">+{{ rand(5, 25) }}%</span>
            <span class="text-gray-500 text-sm ml-2">from last month</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-lg border border-purple-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Favorite Destinations</p>
                <p class="text-3xl font-bold text-purple-600">{{ Auth::check() && Auth::user()->bookings ? Auth::user()->bookings->unique('destination')->count() : 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-map-marker-alt text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center">
            <span class="text-green-500 text-sm font-medium">+{{ rand(5, 25) }}%</span>
            <span class="text-gray-500 text-sm ml-2">from last month</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-lg border border-orange-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Reviews Given</p>
                <p class="text-3xl font-bold text-orange-600">{{ Auth::check() && Auth::user()->reviews ? Auth::user()->reviews->count() : 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-star text-orange-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center">
            <span class="text-green-500 text-sm font-medium">+{{ rand(5, 25) }}%</span>
            <span class="text-gray-500 text-sm ml-2">from last month</span>
        </div>
    </div>
</div>

<!-- Featured Services -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Hotel Packages -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">Packages</h3>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-hotel text-blue-600"></i>
            </div>
        </div>
        <p class="text-gray-600 mb-4">Complete packages with accommodation, meals, and premium services.</p>
        <ul class="space-y-2 mb-6">
            <li class="flex items-center text-sm text-gray-600">
                <i class="fas fa-check text-green-500 mr-2"></i>
                Luxury accommodations
            </li>
            <li class="flex items-center text-sm text-gray-600">
                <i class="fas fa-check text-green-500 mr-2"></i>
                All-inclusive meals
            </li>
            <li class="flex items-center text-sm text-gray-600">
                <i class="fas fa-check text-green-500 mr-2"></i>
                Premium services
            </li>
        </ul>
        <a href="{{ route('user.search') }}" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-700">
            Explore Packages
            <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>

    <!-- Add-on Services -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">Add-on Services</h3>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-plus-circle text-green-600"></i>
            </div>
        </div>
        <p class="text-gray-600 mb-4">Enhance your experience with additional services and add-ons.</p>
        <ul class="space-y-2 mb-6">
            <li class="flex items-center text-sm text-gray-600">
                <i class="fas fa-check text-green-500 mr-2"></i>
                Spa treatments
            </li>
            <li class="flex items-center text-sm text-gray-600">
                <i class="fas fa-check text-green-500 mr-2"></i>
                Airport transfers
            </li>
            <li class="flex items-center text-sm text-gray-600">
                <i class="fas fa-check text-green-500 mr-2"></i>
                Guided tours
            </li>
        </ul>
        <a href="{{ route('user.search') }}" class="inline-flex items-center text-green-600 font-semibold hover:text-green-700">
            View Add-ons
            <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>

    <!-- Custom Bookings -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">Custom Bookings</h3>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-edit text-purple-600"></i>
            </div>
        </div>
        <p class="text-gray-600 mb-4">Tailor-made bookings designed for your specific needs and preferences.</p>
        <ul class="space-y-2 mb-6">
            <li class="flex items-center text-sm text-gray-600">
                <i class="fas fa-check text-green-500 mr-2"></i>
                Personalized itinerary
            </li>
            <li class="flex items-center text-sm text-gray-600">
                <i class="fas fa-check text-green-500 mr-2"></i>
                Flexible dates
            </li>
            <li class="flex items-center text-sm text-gray-600">
                <i class="fas fa-check text-green-500 mr-2"></i>
                Special requests
            </li>
        </ul>
        <a href="{{ route('user.form_booker') }}" class="inline-flex items-center text-purple-600 font-semibold hover:text-purple-700">
            Create Custom Booking
            <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
</div>

<!-- Recent Activity -->
<div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-800">Recent Activity</h3>
        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-clock text-blue-600"></i>
        </div>
    </div>
    <div class="space-y-4">
        @if(Auth::check() && Auth::user()->bookings)
            @forelse(Auth::user()->bookings->take(3) as $booking)
                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-check text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">Booking #{{ $booking->id }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->status }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="fas fa-calendar-alt text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500">No recent bookings</p>
                    <a href="{{ route('user.search') }}" class="inline-flex items-center mt-2 text-blue-600 font-semibold hover:text-blue-700">
                        Make your first booking
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            @endforelse
        @else
            <div class="text-center py-8">
                <i class="fas fa-calendar-alt text-gray-300 text-4xl mb-4"></i>
                <p class="text-gray-500">No recent bookings</p>
                <a href="{{ route('user.search') }}" class="inline-flex items-center mt-2 text-blue-600 font-semibold hover:text-blue-700">
                    Make your first booking
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
