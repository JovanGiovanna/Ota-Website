<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pointer Hotel - Luxury Experience')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-rose-50 via-pink-50 to-purple-50 min-h-screen">
    <!-- Enhanced Floating Navigation -->
    <nav class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50
                bg-white/90 backdrop-blur-xl rounded-2xl px-8 py-4
                shadow-2xl border border-white/20 w-[95%] md:w-[85%]
                flex items-center justify-between transition-all duration-300 hover:shadow-3xl">

        <!-- Logo Section -->
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-r from-rose-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                <img src="{{ asset('pointer.png') }}" alt="pointer" class="w-7 h-7">
            </div>
            <div>
                <span class="text-gray-800 font-bold text-xl">Pointer Hotel</span>
                <p class="text-gray-500 text-xs">Luxury Experience</p>
            </div>
        </div>

        <!-- Menu (Desktop) -->
        <div class="hidden md:flex items-center space-x-8">
            <a href="{{route('landing')}}" class="text-gray-700 font-medium hover:text-rose-600 transition-all duration-300 relative group">
                <span>Home</span>
                <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-rose-500 to-pink-600 group-hover:w-full transition-all duration-300"></div>
            </a>
            <a href="#facilities" class="text-gray-700 font-medium hover:text-rose-600 transition-all duration-300 relative group">
                <span>Facilities</span>
                <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-rose-500 to-pink-600 group-hover:w-full transition-all duration-300"></div>
            </a>
            <a href="#rooms" class="text-gray-700 font-medium hover:text-rose-600 transition-all duration-300 relative group">
                <span>Rooms</span>
                <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-rose-500 to-pink-600 group-hover:w-full transition-all duration-300"></div>
            </a>
            <a href="#about" class="text-gray-700 font-medium hover:text-rose-600 transition-all duration-300 relative group">
                <span>About Us</span>
                <div class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-rose-500 to-pink-600 group-hover:w-full transition-all duration-300"></div>
            </a>
        </div>

        <!-- Auth Buttons (Desktop) -->
        <div class="hidden md:flex items-center space-x-3">
            @guest
                <!-- Guest User -->
                <a href="{{ route('login') }}"
                   class="flex items-center space-x-2 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 text-white px-6 py-3 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <span class="font-medium">Login</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            @else
                <!-- Authenticated User -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('booking.history') }}"
                       class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-5 py-3 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-medium">
                       <i class="fas fa-history mr-2"></i>History
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white px-5 py-3 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 font-medium">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            @endguest
        </div>

        <!-- Mobile Menu Button -->
        <div class="md:hidden flex items-center">
            <button id="mobile-menu-button" class="text-gray-700 focus:outline-none p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu Overlay (Hidden by default) -->
    <div id="mobile-menu" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm hidden md:hidden">
        <div class="fixed top-24 left-1/2 transform -translate-x-1/2 w-[90%] bg-white rounded-2xl shadow-2xl p-6">
            <div class="space-y-4">
                <a href="{{route('landing')}}" class="block text-gray-700 font-medium hover:text-rose-600 transition py-2">Home</a>
                <a href="#facilities" class="block text-gray-700 font-medium hover:text-rose-600 transition py-2">Facilities</a>
                <a href="#rooms" class="block text-gray-700 font-medium hover:text-rose-600 transition py-2">Rooms</a>
                <a href="#about" class="block text-gray-700 font-medium hover:text-rose-600 transition py-2">About Us</a>
                @guest
                    <a href="{{ route('login') }}" class="block bg-gradient-to-r from-rose-500 to-pink-600 text-white px-4 py-3 rounded-xl text-center font-medium mt-4">Login</a>
                @else
                    <a href="{{ route('booking.history') }}" class="block bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-3 rounded-xl text-center font-medium">History</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full bg-gradient-to-r from-red-500 to-rose-600 text-white px-4 py-3 rounded-xl font-medium mt-2">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="min-h-screen pt-24">
        @yield('content')
    </main>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Mobile Menu Script -->
    <script>
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Close menu when clicking outside
        mobileMenu.addEventListener('click', (e) => {
            if (e.target === mobileMenu) {
                mobileMenu.classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
