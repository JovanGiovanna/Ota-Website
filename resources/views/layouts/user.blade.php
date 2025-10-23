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

    <!-- Navbar -->
<nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center py-4 px-6">
        <a href="/" class="text-2xl font-bold text-gray-800">Pointer</a>
        <div class="flex items-center space-x-4">
            @auth
                <span class="text-gray-700">{{ auth()->user()->name }}</span>

                <!-- Tombol History Booking -->
                <a href="{{ route('booking.history') }}" 
                   class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 transition">
                   History Booking
                </a>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-red-600 hover:underline">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-red-600">Login</a>
                <a href="{{ route('register') }}" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">Register</a>
            @endauth
        </div>
    </div>
</nav>


    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-6 mt-12">
        <div class="container mx-auto text-center">
            <p>&copy; {{ date('Y') }} Pointer. All rights reserved.</p>
        </div>
    </footer>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    @stack('scripts')
</body>
</html>