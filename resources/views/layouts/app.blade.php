<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Hotel App')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800">
    <!-- Floating Navigation -->
<nav class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 
            bg-black/40 backdrop-blur-md rounded-full px-6 py-3 
            shadow-lg w-[90%] md:w-[80%] flex items-center justify-between">

    <!-- Logo -->
    <div class="flex items-center space-x-2">
        <img src="{{ asset('pointer.png') }}" alt="pointer" class="w-8 h-8 rounded-full">
        <span class="text-white font-bold text-lg">Pointer Hotel</span>
    </div>

    <!-- Menu (Desktop) -->
    <div class="hidden md:flex items-center space-x-10">
        <a href="{{route('landing')}}" class="text-white font-medium hover:text-blue-300 transition">Home</a>
        <a href="#facilities" class="text-white font-medium hover:text-blue-300 transition">Facility</a>
        <a href="#rooms" class="text-white font-medium hover:text-blue-300 transition">Rooms</a>
        <a href="#about" class="text-white font-medium hover:text-blue-300 transition">About Us</a>
    </div>

    <!-- Auth Buttons (Desktop) -->
    <div class="hidden md:flex items-center space-x-3">
        @guest 
            <!-- Kalau belum login -->
            <a href="{{ route('login') }}" 
               class="flex items-center space-x-2 bg-white/20 hover:bg-white/30 text-white px-5 py-2 rounded-full transition-all duration-300">
                <span>Login</span> 
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"> 
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17 8l4 4m0 0l-4 4m4-4H3"/> 
                </svg> 
            </a> 
        @else 
            <!-- Kalau sudah login -->
            <a href="{{ route('booking.history') }}" 
               class="bg-white/20 hover:bg-white/30 text-white px-5 py-2 rounded-full transition-all duration-300">
               History
            </a> 
            <form method="POST" action="{{ route('logout') }}"> 
                @csrf 
                <button type="submit" 
                        class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-full transition-all duration-300">
                    Logout
                </button> 
            </form> 
        @endguest 
    </div>

    <!-- Mobile Menu Button -->
    <div class="md:hidden flex items-center">
        <button id="mobile-menu-button" class="text-white focus:outline-none">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
</nav>

    <!-- Navbar -->
<!--  -->


    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @stack('scripts')
</body>
</html>