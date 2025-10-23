@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('welcome')
Welcome back, {{ Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : (Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'Admin') }}!
@endsection

@section('logout_route', route('logout'))

@section('sidebar')
<!-- Navigation Items with Modern Styling -->
<div class="space-y-2">
    <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg' : 'text-indigo-200 hover:bg-indigo-600/20 hover:text-white' }}">
        <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : 'bg-indigo-500/30' }} mr-3">
            <i class="fas fa-tachometer-alt text-sm"></i>
        </div>
        <span>Dashboard</span>
        @if(request()->routeIs('admin.dashboard'))
            <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
        @endif
    </a>

    <a href="{{ route('admin.users') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 text-indigo-200 hover:bg-indigo-600/20 hover:text-white">
        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-500/30 mr-3">
            <i class="fas fa-users text-sm"></i>
        </div>
        <span>User Management</span>
    </a>

    <a href="{{ route('admin.bookings') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 text-indigo-200 hover:bg-indigo-600/20 hover:text-white">
        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-500/30 mr-3">
            <i class="fas fa-calendar-check text-sm"></i>
        </div>
        <span>Bookings</span>
    </a>

    <a href="{{ route('admin.categories') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 text-indigo-200 hover:bg-indigo-600/20 hover:text-white">
        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-500/30 mr-3">
            <i class="fas fa-tags text-sm"></i>
        </div>
        <span>Categories</span>
    </a>

    <a href="{{ route('admin.analytics') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 text-indigo-200 hover:bg-indigo-600/20 hover:text-white">
        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-500/30 mr-3">
            <i class="fas fa-chart-line text-sm"></i>
        </div>
        <span>Analytics</span>
    </a>

    <a href="{{ route('admin.settings') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 text-indigo-200 hover:bg-indigo-600/20 hover:text-white">
        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-500/30 mr-3">
            <i class="fas fa-cog text-sm"></i>
        </div>
        <span>Settings</span>
    </a>
</div>
@endsection

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-800 rounded-2xl p-8 mb-8 text-white shadow-2xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2">Admin Dashboard</h1>
            <p class="text-indigo-100">Monitor and manage your hotel operations</p>
        </div>
        <div class="hidden md:block">
            <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center">
                <i class="fas fa-chart-pie text-3xl text-white"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-indigo-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Categories</p>
                <p class="text-3xl font-bold text-indigo-600">{{ $totalKategori }}</p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-tags text-indigo-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center">
            <span class="text-green-500 text-sm font-medium">+12%</span>
            <span class="text-gray-500 text-sm ml-2">from last month</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-lg border border-red-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                <p class="text-3xl font-bold text-red-600">{{ $totalBooking }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-calendar-check text-red-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center">
            <span class="text-green-500 text-sm font-medium">+8%</span>
            <span class="text-gray-500 text-sm ml-2">from last month</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-lg border border-green-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Active Users</p>
                <p class="text-3xl font-bold text-green-600">{{ $totalUsers ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-green-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center">
            <span class="text-green-500 text-sm font-medium">+15%</span>
            <span class="text-gray-500 text-sm ml-2">from last month</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-lg border border-purple-100 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Revenue</p>
                <p class="text-3xl font-bold text-purple-600">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center">
            <span class="text-green-500 text-sm font-medium">+23%</span>
            <span class="text-gray-500 text-sm ml-2">from last month</span>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Booking Chart -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">Monthly Bookings</h3>
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-line text-blue-600"></i>
            </div>
        </div>
        <canvas id="bookingChart" class="w-full h-64"></canvas>
    </div>

    <!-- Revenue Chart -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">Monthly Revenue</h3>
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-bar text-green-600"></i>
            </div>
        </div>
        <canvas id="revenueChart" class="w-full h-64"></canvas>
    </div>

    <!-- Status Chart -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">Booking Status</h3>
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-pie text-purple-600"></i>
            </div>
        </div>
        <canvas id="statusChart" class="w-full h-64"></canvas>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-800">Recent Activity</h3>
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-orange-600"></i>
            </div>
        </div>
        <div class="space-y-4">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-xs"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">New booking confirmed</p>
                    <p class="text-xs text-gray-500">2 minutes ago</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-plus text-blue-600 text-xs"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">New user registered</p>
                    <p class="text-xs text-gray-500">15 minutes ago</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-star text-purple-600 text-xs"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800">New review received</p>
                    <p class="text-xs text-gray-500">1 hour ago</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Booking Chart
    const bookingCtx = document.getElementById('bookingChart').getContext('2d');
    new Chart(bookingCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Bookings',
                data: {!! json_encode($bookingData) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return Number(value).toFixed(0);
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($pendapatanData) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return Number(value).toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusLabels) !!},
            datasets: [{
                data: {!! json_encode($statusData) !!},
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(156, 163, 175, 0.8)'
                ],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 2,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
