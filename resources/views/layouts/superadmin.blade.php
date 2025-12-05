<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script async src="https://cdn.jsdelivr.net/npm/sweetalert2@11.27.0/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.27.0/dist/sweetalert2.min.css">
    @stack('styles')
</head>
<body class="h-full bg-gradient-to-br from-slate-50 to-blue-50">
    <div id="flash-messages" data-success="{{ session('success') }}" data-error="{{ session('error') }}" data-warning="{{ session('warning') }}" style="display:none;"></div>
    <div class="flex h-full">
        @php
            $adminUser = null;
            $roleDisplay = 'Super Admin';
            if (Auth::guard('super_admin')->check()) {
                $adminUser = Auth::guard('super_admin')->user();
                $roleDisplay = 'Super Admin';
            } elseif (Auth::guard('admin')->check()) {
                $adminUser = Auth::guard('admin')->user();
                $primaryRole = $adminUser->roles()->first();
                if ($primaryRole) {
                    $roleDisplay = ucfirst($primaryRole->key);
                } else {
                    $roleDisplay = 'Admin';
                }
            }
        @endphp
        <!-- Sidebar -->
        <div class="hidden md:flex md:w-72 md:flex-col">
            <div class="flex flex-col flex-grow bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 pt-6 pb-4 overflow-y-auto shadow-2xl">
                <div class="flex items-center flex-shrink-0 px-6 mb-8">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-crown text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-white text-xl font-bold">{{ $roleDisplay }}</h1>
                            <p class="text-slate-300 text-xs">{{ $roleDisplay == 'Super Admin' ? 'System Control' : 'Management Panel' }}</p>
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
                               @if($adminUser && $adminUser->hasPermission('system.manage'))
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
                               @endif
                            </div>
                        </div>

                        <!-- Category & Type Management -->
                        <div class="px-2 py-2">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Category & Type</h3>
                            <div class="space-y-1">
                                @if($adminUser && $adminUser->hasPermission('system.manage'))
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
                                @endif
                                @if($adminUser && $adminUser->hasPermission('packages.manage'))
                                <a href="{{ route('super_admin.packages') }}" class="hidden"></a>
                                @endif
                                @if($adminUser && $adminUser->hasPermission('products.manage'))
                                <a href="{{ route('super_admin.products') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Product
                                </a>
                                @endif
                                @if($adminUser && $adminUser->hasPermission('addons.manage'))
                                <a href="{{ route('super_admin.addons') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Addon
                                </a>
                                @endif
                            </div>
                        </div>

                        <!-- User Management -->
                        <div class="px-2 py-2">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">User Management</h3>
                            <div class="space-y-1">
                                @if($adminUser && $adminUser->hasPermission('vendors.manage'))
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
                                @endif
                                @if($adminUser && $adminUser->hasPermission('customers.manage'))
                                <a href="{{ route('super_admin.customers') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                    Customer
                                </a>
                                @endif
                            </div>
                        </div>

                        <!-- Transaction Management -->
                        <div class="px-2 py-2">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Transactions</h3>
                            <div class="space-y-1">
                                @if($adminUser && ($adminUser->hasPermission('transactions.view') || $adminUser->hasPermission('transactions.manage')))
                                <a href="{{ route('super_admin.bookings') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                                    </svg>
                                    Bookings
                                </a>
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
                                @endif
                            </div>
                        </div>

                        <!-- System Management -->
                        <div class="px-2 py-2">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">System</h3>
                            <div class="space-y-1">
                                @if($adminUser && $adminUser->hasPermission('rekon.manage'))
                                <a href="{{ route('super_admin.rekon') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Rekon
                                </a>
                                @endif
                            </div>
                        </div>

                        <!-- Admin Management -->
                        <div class="px-2 py-2">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Administration</h3>
                            <div class="space-y-1">
                                @if($adminUser && $adminUser->hasPermission('admins.manage'))
                                <a href="{{ route('super_admin.admins') }}" class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                                    <svg class="text-gray-400 group-hover:text-gray-300 mr-3 flex-shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Admins
                                </a>
                                @endif
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
                            <p class="text-white text-sm font-medium truncate">{{ $adminUser ? $adminUser->name : 'Admin' }}</p>
                            <p class="text-slate-400 text-xs">{{ $roleDisplay }}</p>
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
                <div class="flex-1 px-6 flex justify-end items-center">
                    <!-- Profile dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ substr($adminUser ? $adminUser->name : 'A', 0, 1) }}</span>
                            </div>
                            <span class="text-slate-700 font-medium hidden xl:block">{{ $adminUser ? $adminUser->name : 'Admin' }}</span>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-200 py-1 z-50">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-sm font-medium text-gray-900">{{ $adminUser ? $adminUser->name : 'Admin' }}</p>
                                <p class="text-xs text-gray-500">{{ $roleDisplay }}</p>
                            </div>
                            <div class="border-t border-slate-100">
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
                        <!-- Role Display Header -->
                        <div class="mb-8">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $roleDisplay }} Dashboard</h1>
                            <p class="mt-2 text-sm text-gray-600">Welcome back, {{ $adminUser ? $adminUser->name : 'Admin' }}! You are logged in as {{ $roleDisplay }}.</p>
                        </div>
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')

    <script>
        // Helper function for delete confirmation
        window.confirmDeleteAction = function(formElement) {
            if (!formElement) return false;
            event.preventDefault();

            const itemName = formElement.getAttribute('data-delete-item') || 'this item';

            // Use Swal if available (provided by realrashid package); otherwise fallback
            if (typeof Swal === 'undefined') {
                return confirm('Are you sure you want to delete ' + itemName + '?');
            }

            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you really want to delete ' + itemName + '? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    formElement.submit();
                }
            });
            return false;
        };
    </script>

    {{-- realrashid/sweet-alert blade include (renders alert scripts) --}}
    @includeIf('sweetalert::alert')

</body>
</html>
