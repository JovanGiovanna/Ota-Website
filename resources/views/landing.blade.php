@extends('layouts.user')

@section('title', 'Landing Page - Hotel App')

@section('content')

<!-- Hero Section -->
<section class="hero-bg text-white py-20">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold mb-6">Welcome to Our Luxury Hotel</h1>
        <p class="text-xl mb-8">Experience unparalleled comfort and elegance</p>
        <a href="#facilities" class="bg-white text-gray-800 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">Explore Facilities</a>
    </div>
</section>

<!-- Facilities Section -->
<section id="facilities" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-12">Our Facilities</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="bg-blue-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Luxury Rooms</h3>
                <p class="text-gray-600">Spacious and comfortable rooms designed for your ultimate relaxation.</p>
            </div>
            <div class="text-center">
                <div class="bg-green-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Fine Dining</h3>
                <p class="text-gray-600">Enjoy exquisite cuisine prepared by our world-class chefs.</p>
            </div>
            <div class="text-center">
                <div class="bg-purple-100 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">Spa & Wellness</h3>
                <p class="text-gray-600">Rejuvenate your body and mind with our premium spa services.</p>
            </div>
        </div>
    </div>
</section>

@endsection
