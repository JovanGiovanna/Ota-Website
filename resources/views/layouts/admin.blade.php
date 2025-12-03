{{-- resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @stack('styles')
</head>
<body class="h-full bg-gradient-to-br from-indigo-50 to-blue-50">
    <div id="flash-messages" data-success="{{ session('success') }}" data-error="{{ session('error') }}" data-warning="{{ session('warning') }}" style="display:none;"></div>
    <div class="flex h-full">
        <!-- Sidebar -->
        <div class="hidden md:flex md:w-72 md:flex-col">
            <div class="flex flex-col flex-grow bg-gradient-to-b from-indigo-900 via-blue-900 to-indigo-900 pt-6 pb-4 overflow-y-auto shadow-2xl">
                
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0 px-6 mb-8">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-shield-alt text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-white text-xl font-bold">Admin Panel</h1>
                            <p class="text-indigo-200 text-xs">Management System</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="mt-2 flex-grow flex flex-col">
                    <nav class="flex-1 px-4 space-y-2">

                        <a href="{{ route('admin.dashboard') }}" class="text-indigo-200 hover:bg-indigo-700 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-tachometer-alt text-indigo-400 group-hover:text-white mr-3"></i>
                            Dashboard
                        </a>

                        <a href="{{ route('admin.packages') }}" class="text-indigo-200 hover:bg-indigo-700 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-cube text-indigo-400 group-hover:text-white mr-3"></i>
                            Packages
                        </a>
                        
                        <a href="{{ route('admin.bookings') }}" class="text-indigo-200 hover:bg-indigo-700 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-calendar-check text-indigo-400 group-hover:text-white mr-3"></i>
                            Booking Approval
                        </a>

                    
                        
                        <div class="mt-4 pt-4 border-t border-indigo-700">
                            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-indigo-300">Transaksi</p>
                        </div>

                        <a href="{{ route('super_admin.package.index') }}" class="text-indigo-200 hover:bg-indigo-700 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-receipt text-indigo-400 group-hover:text-white mr-3"></i>
                            Transaction Packages
                        </a>
                        
                        <a href="{{ route('super_admin.product.index') }}" class="text-indigo-200 hover:bg-indigo-700 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-shopping-bag text-indigo-400 group-hover:text-white mr-3"></i>
                            Transaction Product
                        </a>

                        <a href="{{ route('super_admin.addon.index') }}" class="text-indigo-200 hover:bg-indigo-700 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <i class="fas fa-money-check-alt text-indigo-400 group-hover:text-white mr-3"></i>
                            Transaction Addons
                        </a>

                        @yield('sidebar')
                    </nav>
                </div>

                <!-- Footer -->
                <div class="px-4 py-4 border-t border-indigo-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-indigo-400 to-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-cog text-white text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm font-medium truncate">
                                {{ Auth::guard('admin')->check() 
                                    ? Auth::guard('admin')->user()->name 
                                    : (Auth::guard('super_admin')->check() 
                                        ? Auth::guard('super_admin')->user()->name 
                                        : 'Unknown') }}
                            </p>
                            <p class="text-indigo-200 text-xs">
                                {{ Auth::guard('admin')->check() ? 'Admin' : 'Super Admin' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <div class="relative z-10 flex-shrink-0 flex h-20 bg-white shadow-lg border-b border-indigo-200">
                <div class="flex-1 px-6 flex justify-end items-center">
                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200">
                            <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ substr((Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : (Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'A')), 0, 1) }}</span>
                            </div>
                            <span class="text-indigo-700 font-medium hidden xl:block">{{ Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : (Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'Admin') }}</span>
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-indigo-200 py-1 z-50">
                            <div class="px-4 py-2 border-b border-indigo-100">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : (Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'Admin') }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::guard('admin')->check() ? 'Admin' : 'Super Admin' }}</p>
                            </div>
                            @if (Auth::guard('admin')->check())
                                <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">
                                    <i class="fas fa-user mr-2"></i>View Profile
                                </a>
                            @endif
                            <div class="border-t border-indigo-100">
                                <form method="POST" action="@if(Auth::guard('admin')->check()){{ route('admin.logout') }}@elseif(Auth::guard('super_admin')->check()){{ route('super_admin.logout') }}@endif" class="inline w-full">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="flex-1 relative overflow-y-auto focus:outline-none">
                <div class="py-8">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
