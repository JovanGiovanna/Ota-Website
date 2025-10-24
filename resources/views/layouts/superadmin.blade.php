<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @stack('styles')
</head>
<body class="h-full bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="flex h-full">
        <!-- Sidebar -->
        <div class="hidden md:flex md:w-72 md:flex-col">
            <div class="flex flex-col flex-grow bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 pt-6 pb-4 overflow-y-auto shadow-2xl">
                <div class="flex items-center flex-shrink-0 px-6 mb-8">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-crown text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-white text-xl font-bold">Super Admin</h1>
                            <p class="text-slate-300 text-xs">System Control</p>
                        </div>
                    </div>
                </div>
                <div class="mt-2 flex-grow flex flex-col">
                    <nav class="flex-1 px-4 space-y-2">
                        @yield('sidebar')
                        <div class="space-y-1">
                            <a href="{{ route('super_admin.dashboard') }}"
                                class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" 
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" 
                                                stroke="currentColor" 
                                            class="text-gray-400 group-hover:text-gray-300 mr-3 h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2h-4a2 2 0 01-2-2V12H9v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                    </svg>
                                    Dashboard
                                    </a>
                                </div>
                        <!-- Location Management -->
                        <div class="px-2 py-2">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Location Management</h3>
                            <div class="space-y-1">
                               <a href="{{ route('super_admin.cities') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                     <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    City
                                </a>
                                <a href="{{ route('super_admin.provinces') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    Province
                                </a>
                            </div>
                        </div>

                        <!-- Category & Type Management -->
                        <div class="px-2 py-2">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Category & Type</h3>
                            <div class="space-y-1">
                                <a href="{{ route('super_admin.types_categories') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    Type & Category
                                </a>
                                <a href="{{ route('super_admin.packages') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    Package
                                </a>
                            </div>
                        </div>

                        <!-- User Management -->
                        <div class="px-2 py-2">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">User Management</h3>
                            <div class="space-y-1">
                                <a href="{{ route('super_admin.vendors') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Vendor
                                </a>
                                <a href="{{ route('super_admin.vendor_details') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Vendor Detail
                                </a>
                                <a href="{{ route('super_admin.customers') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                    Customer
                                </a>
                            </div>
                        </div>

                        <!-- Transaction Management -->
                        <div class="px-2 py-2">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Transactions</h3>
                            <div class="space-y-1">
                                <a href="{{ route('super_admin.transaction_packages') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    Transaction Package
                                </a>
                                <a href="{{ route('super_admin.transaction_products') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Transaction Product
                                </a>
                                <a href="{{ route('super_admin.transaction_addons') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Transaction Addons
                                </a>
                            </div>
                        </div>

                        <!-- System Management -->
                        <div class="px-2 py-2">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">System</h3>
                            <div class="space-y-1">
                                <a href="{{ route('super_admin.rekon') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Rekon
                                </a>
                            </div>
                        </div>

                        <!-- Dashboard Access -->
                        <div class="px-2 py-2">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Dashboard Access</h3>
                            <div class="space-y-1">
                                <a href="{{ route('admin.dashboard') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                    Admin Panel
                                </a>
                                <a href="{{ route('vendor.dashboard') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Vendor Panel
                                </a>
                                <a href="{{ route('landing') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9m0 9c-1.657 0-3-4.03-3-9s1.343-9 3-9m0 18c1.657 0 3-4.03 3-9s-1.343-9-3-9" />
                                    </svg>
                                    User Landing
                                </a>
                            </div>
                        </div>
                    </nav>
                </div>
                <div class="px-4 py-4 border-t border-slate-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-shield text-white text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm font-medium truncate">{{ Auth::guard('super_admin')->user()->name }}</p>
                            <p class="text-slate-400 text-xs">Super Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Top navigation -->
            <div class="relative z-10 flex-shrink-0 flex h-20 bg-white shadow-lg border-b border-slate-200">
                <button class="px-4 border-r border-slate-200 text-slate-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 md:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </button>
                <div class="flex-1 px-6 flex justify-between items-center">
                    <div class="flex-1 flex max-w-lg">
                        <div class="w-full">
                            <label for="search-field" class="sr-only">Search</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input id="search-field" class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50" placeholder="Search system..." type="search">
                            </div>
                        </div>
                    </div>
                    <div class="ml-4 flex items-center md:ml-6 space-x-4">
                        <!-- Notifications -->
                        <button class="p-2 text-slate-400 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg">
                            <span class="sr-only">View notifications</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM15 7v5h5l-5-5zM4 12h8m-8 4h6" />
                            </svg>
                        </button>

                        <!-- Profile dropdown -->
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">{{ substr(Auth::guard('super_admin')->user()->name, 0, 1) }}</span>
                                    </div>
                                    <span class="text-slate-700 font-medium hidden sm:block">@yield('welcome')</span>
                                </div>
                                <form method="POST" action="@yield('logout_route')" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Logout
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
