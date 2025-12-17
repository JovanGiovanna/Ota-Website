    <!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vendor Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
</head>
<body class="h-full bg-gradient-to-br from-emerald-50 to-teal-50">
    <div id="flash-messages" data-success="{{ session('success') }}" data-error="{{ session('error') }}" data-warning="{{ session('warning') }}" style="display:none;"></div>
    <div class="flex h-full" x-data="{ mobileOpen: false }">
        <!-- Mobile sidebar -->
        <div class="md:hidden" x-show="mobileOpen" style="display: none;" x-transition.opacity>
            <div class="fixed inset-0 flex z-40">
                <div class="fixed inset-0 bg-emerald-900/70" aria-hidden="true" @click="mobileOpen = false"></div>
                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-gradient-to-b from-emerald-800 via-emerald-700 to-teal-800 shadow-2xl" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">
                    <div class="absolute top-0 right-0 -mr-12 pt-4">
                        <button class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="mobileOpen = false" aria-label="Close sidebar">
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                        <div class="flex items-center px-4 mb-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center shadow-lg">
                                    <i class="fas fa-store text-white text-lg"></i>
                                </div>
                                <div>
                                    <h1 class="text-white text-xl font-bold">Vendor Portal</h1>
                                    <p class="text-emerald-200 text-xs">Business Management</p>
                                </div>
                            </div>
                        </div>
                        <nav class="px-2 space-y-2">
                            <a href="{{ route('vendor.dashboard') }}" class="text-emerald-200 hover:bg-emerald-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="text-emerald-400 group-hover:text-emerald-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z" />
                                </svg>
                                Dashboard
                            </a>
                            <a href="{{ route('vendor.products') }}" class="text-emerald-200 hover:bg-emerald-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="text-emerald-400 group-hover:text-emerald-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Product
                            </a>
                            <a href="{{ route('vendor.addons') }}" class="text-emerald-200 hover:bg-emerald-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="text-emerald-400 group-hover:text-emerald-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Addons
                            </a>
                            <a href="{{ route('vendor.transaction_products') }}" class="text-emerald-200 hover:bg-emerald-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="text-emerald-400 group-hover:text-emerald-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                View Book Product
                            </a>
                            <a href="{{ route('vendor.transaction_addons') }}" class="text-emerald-200 hover:bg-emerald-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="text-emerald-400 group-hover:text-emerald-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                View Book Addons
                            </a>
                            <a href="{{ route('vendor.transactions.report') }}" class="text-emerald-200 hover:bg-emerald-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                <svg class="text-emerald-400 group-hover:text-emerald-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h6M9 7H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2" />
                                </svg>
                                Transactions Report
                            </a>
                        </nav>
                    </div>
                    <div class="px-4 py-4 border-t border-emerald-600">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-tie text-white text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-white text-sm font-medium truncate">{{ Auth::guard('vendor')->check() ? Auth::guard('vendor')->user()->name : (Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'Unknown') }}</p>
                                <p class="text-emerald-200 text-xs">Vendor Account</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="hidden md:flex md:w-72 md:flex-col">
            <div class="flex flex-col flex-grow bg-gradient-to-b from-emerald-800 via-emerald-700 to-teal-800 pt-6 pb-4 overflow-y-auto shadow-2xl">
                <div class="flex items-center flex-shrink-0 px-6 mb-8">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-store text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-white text-xl font-bold">Vendor Portal</h1>
                            <p class="text-emerald-200 text-xs">Business Management</p>
                        </div>
                    </div>
                </div>
                <div class="mt-2 flex-grow flex flex-col">
                    <nav class="flex-1 px-4 space-y-2">
                        <a href="{{ route('vendor.dashboard') }}" class="text-emerald-200 hover:bg-emerald-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <svg class="text-emerald-400 group-hover:text-emerald-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z" />
                            </svg>
                            Dashboard
                        </a>
                        <a href="{{ route('vendor.products') }}" class="text-emerald-200 hover:bg-emerald-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <svg class="text-emerald-400 group-hover:text-emerald-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Product
                        </a>
                        <a href="{{ route('vendor.addons') }}" class="text-emerald-200 hover:bg-emerald-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <svg class="text-emerald-400 group-hover:text-emerald-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Addons
                        </a>
                        
                        <a href="{{ route('vendor.transaction_products') }}" class="text-emerald-200 hover:bg-emerald-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <svg class="text-emerald-400 group-hover:text-emerald-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            View Book Product
                        </a>
                        <a href="{{ route('vendor.transaction_addons') }}" class="text-emerald-200 hover:bg-emerald-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <svg class="text-emerald-400 group-hover:text-emerald-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            View Book Addons
                        </a>
                        <a href="{{ route('vendor.transactions.report') }}" class="text-emerald-200 hover:bg-emerald-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <svg class="text-emerald-400 group-hover:text-emerald-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6a2 2 0 012-2h6M9 7H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-2" />
                            </svg>
                            Transactions Report
                        </a>
                    </nav>
                </div>
                <div class="px-4 py-4 border-t border-emerald-600">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie text-white text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm font-medium truncate">{{ Auth::guard('vendor')->check() ? Auth::guard('vendor')->user()->name : (Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'Unknown') }}</p>
                            <p class="text-emerald-200 text-xs">Vendor Account</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Top navigation -->
            <div class="relative z-10 flex-shrink-0 flex h-20 bg-white shadow-lg border-b border-emerald-200">
                <button @click="mobileOpen = true" class="px-4 border-r border-emerald-200 text-emerald-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500 md:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </button>
                <div class="flex-1 px-6 flex justify-end items-center">
                    <!-- Profile dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors duration-200">
                            <div class="w-8 h-8 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ substr(Auth::guard('vendor')->check() ? Auth::guard('vendor')->user()->name : (Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'V'), 0, 1) }}</span>
                            </div>
                            <span class="text-emerald-700 font-medium hidden xl:block">{{ Auth::guard('vendor')->check() ? Auth::guard('vendor')->user()->name : (Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'Vendor') }}</span>
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-emerald-200 py-1 z-50">
                            <div class="px-4 py-2 border-b border-emerald-100">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::guard('vendor')->check() ? Auth::guard('vendor')->user()->name : (Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'Vendor') }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::guard('vendor')->check() ? 'Vendor' : 'Super Admin' }}</p>
                            </div>
                            @if(Auth::guard('vendor')->check())
                                <a href="{{ route('vendor.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600">
                                    <i class="fas fa-user mr-2"></i>View Profile
                                </a>
                            @endif
                            <div class="border-t border-emerald-100">
                                <form id="logout-form" method="POST" action="@if(Auth::guard('vendor')->check()){{ route('vendor.logout') }}@elseif(Auth::guard('super_admin')->check()){{ route('super_admin.logout') }}@endif" class="inline w-full">
                                    @csrf
                                    <button type="button" onclick="confirmLogout()" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700">
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

    @include('sweetalert::alert')
    <script>
        function confirmLogout() {
            const form = document.getElementById('logout-form');
            if (!form || typeof Swal === 'undefined') {
                // Fallback submit if Swal not available
                return form ? form.submit() : null;
            }
            Swal.fire({
                title: 'Keluar dari akun?',
                text: 'Anda akan logout dari portal vendor.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, logout',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
