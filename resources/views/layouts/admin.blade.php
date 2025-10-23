<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @stack('styles')
</head>
<body class="h-full bg-gradient-to-br from-indigo-50 to-blue-50">
    <div class="flex h-full">
        <!-- Sidebar -->
        <div class="hidden md:flex md:w-72 md:flex-col">
            <div class="flex flex-col flex-grow bg-gradient-to-b from-indigo-900 via-blue-900 to-indigo-900 pt-6 pb-4 overflow-y-auto shadow-2xl">
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
                <div class="mt-2 flex-grow flex flex-col">
                    <nav class="flex-1 px-4 space-y-2">
                        @yield('sidebar')
                    </nav>
                </div>
                <div class="px-4 py-4 border-t border-indigo-700">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-indigo-400 to-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-cog text-white text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm font-medium truncate">{{ Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : (Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'Unknown') }}</p>
                            <p class="text-indigo-200 text-xs">Administrator</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Top navigation -->
            <div class="relative z-10 flex-shrink-0 flex h-20 bg-white shadow-lg border-b border-indigo-200">
                <button class="px-4 border-r border-indigo-200 text-indigo-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 md:hidden">
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
                                    <svg class="h-5 w-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input id="search-field" class="block w-full pl-10 pr-3 py-3 border border-indigo-300 rounded-xl text-indigo-900 placeholder-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-indigo-50" placeholder="Search admin data..." type="search">
                            </div>
                        </div>
                    </div>
                    <div class="ml-4 flex items-center md:ml-6 space-x-4">
                        <!-- Notifications -->
                        <button class="p-2 text-indigo-400 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-lg">
                            <span class="sr-only">View notifications</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM15 7v5h5l-5-5zM4 12h8m-8 4h6" />
                            </svg>
                        </button>

                        <!-- Profile dropdown -->
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">{{ substr(Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : (Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'U'), 0, 1) }}</span>
                                    </div>
                                    <span class="text-indigo-700 font-medium hidden sm:block">@yield('welcome')</span>
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
