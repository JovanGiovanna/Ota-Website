    <!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'User Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @stack('styles')
</head>
<body class="h-full bg-gradient-to-br from-blue-50 to-indigo-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-lg fixed top-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-compass text-white text-lg"></i>
                        </div>
                        <span class="text-2xl font-bold text-gray-800">Pointer</span>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-1">
                    <a href="{{ route('user.home') }}" class="text-gray-700 hover:text-blue-600 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('user.home') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>
                    <a href="{{ route('user.search') }}" class="text-gray-700 hover:text-blue-600 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('user.search') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-search mr-2"></i>Search
                    </a>
                    <a href="{{ route('user.form_booker') }}" class="text-gray-700 hover:text-blue-600 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('user.form_booker') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-calendar-plus mr-2"></i>Book
                    </a>
                    <a href="{{ route('user.history') }}" class="text-gray-700 hover:text-blue-600 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('user.history') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-history mr-2"></i>History
                    </a>
                    <a href="{{ route('user.profil') }}" class="text-gray-700 hover:text-blue-600 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request()->routeIs('user.profil') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-user mr-2"></i>Profile
                    </a>
                </div>

                <!-- Right side -->
                <div class="flex items-center space-x-3">
                    <!-- Search -->
                    <div class="hidden md:block">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input id="search-field" class="block w-64 pl-10 pr-3 py-2 border border-blue-300 rounded-lg text-blue-900 placeholder-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-blue-50 text-sm" placeholder="Search destinations..." type="search">
                        </div>
                    </div>

                    <!-- Notifications -->
                    <button class="p-2 text-blue-400 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg transition-colors duration-200">
                        <span class="sr-only">View notifications</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM15 7v5h5l-5-5zM4 12h8m-8 4h6" />
                        </svg>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <span class="text-blue-700 font-medium hidden xl:block">{{ Auth::user()->name ?? 'User' }}</span>
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-blue-200 py-1 z-50">
                            <div class="px-4 py-2 border-b border-blue-100">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? 'User' }}</p>
                                <p class="text-xs text-gray-500">Customer</p>
                            </div>
                            <a href="{{ route('user.profil') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-user mr-2"></i>View Profile
                            </a>
                            <div class="border-t border-blue-100">
                                <form method="POST" action="{{ route('logout') }}" class="inline w-full">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="lg:hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="bg-blue-600 inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white transition-colors duration-200">
                            <span class="sr-only">Open main menu</span>
                            <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="lg:hidden" x-show="open" @click.away="open = false" x-transition>
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-blue-50 border-t border-blue-200">
                    <a href="{{ route('user.home') }}" class="text-blue-700 hover:text-blue-900 block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('user.home') ? 'bg-blue-100' : '' }}">
                        <i class="fas fa-home mr-2"></i>Home
                    </a>
                    <a href="{{ route('user.search') }}" class="text-blue-700 hover:text-blue-900 block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('user.search') ? 'bg-blue-100' : '' }}">
                        <i class="fas fa-search mr-2"></i>Search Hotels
                    </a>
                    <a href="{{ route('user.form_booker') }}" class="text-blue-700 hover:text-blue-900 block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('user.form_booker') ? 'bg-blue-100' : '' }}">
                        <i class="fas fa-calendar-plus mr-2"></i>Book Now
                    </a>
                    <a href="{{ route('user.history') }}" class="text-blue-700 hover:text-blue-900 block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('user.history') ? 'bg-blue-100' : '' }}">
                        <i class="fas fa-history mr-2"></i>History
                    </a>
                    <a href="{{ route('user.profil') }}" class="text-blue-700 hover:text-blue-900 block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('user.profil') ? 'bg-blue-100' : '' }}">
                        <i class="fas fa-user mr-2"></i>Profile
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <main class="flex-1 relative overflow-y-auto focus:outline-none pt-16">
        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </div>
    </main>

    @stack('scripts')
</body>
</html>
