@extends('layouts.vendor')

@section('title', 'Vendor Dashboard')

@section('welcome')
Welcome back, {{ Auth::guard('vendor')->check() ? Auth::guard('vendor')->user()->name : (Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'Vendor') }}!
@endsection

@section('logout_route', route('vendor.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @php
    $vendor = Auth::guard('vendor')->user() ?? Auth::guard('super_admin')->user();
    $hasVendorInfo = \App\Models\VendorInfo::where('id_vendor', $vendor->id)->exists();
    @endphp

    @if (!$hasVendorInfo)
        <div class="mb-6 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">
                        Lengkapi informasi vendor Anda untuk mulai menerima pesanan.
                        <a href="{{ route('vendor.info') }}" class="font-medium underline text-yellow-700 hover:text-yellow-600">
                            Lengkapi sekarang →
                        </a>
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Cards Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <!-- Total Bookings Card -->
        <div class="bg-white rounded-lg p-4 shadow-sm border border-emerald-100 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Total Bookings</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalBookings ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar-check text-emerald-600"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Active bookings</p>
        </div>

        <!-- Cancellations Card -->
        <div class="bg-white rounded-lg p-4 shadow-sm border border-red-100 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Cancellations</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $totalCancellations ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
            <p class="text-xs text-red-500 mt-2">{{ $totalBookings > 0 ? round(($totalCancellations / ($totalBookings + $totalCancellations)) * 100, 1) : 0 }}% rate</p>
        </div>

        <!-- Revenue Card -->
        <div class="bg-white rounded-lg p-4 shadow-sm border border-blue-100 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Revenue</p>
                    <p class="text-lg font-bold text-blue-600 mt-1">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-dollar-sign text-blue-600"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Completed</p>
        </div>

        <!-- Active Services Card -->
        <div class="bg-white rounded-lg p-4 shadow-sm border border-purple-100 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Active Services</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $activeServices ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-concierge-bell text-purple-600"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Published</p>
        </div>

        <!-- Rating Card -->
        <div class="bg-white rounded-lg p-4 shadow-sm border border-orange-100 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-600">Rating</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $averageRating ?? 0 }}/5</p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-star text-orange-600"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
                @if($averageRating >= 4)
                    Excellent
                @elseif($averageRating >= 3)
                    Good
                @else
                    Fair
                @endif
            </p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Monthly Performance Chart (Full Width on Mobile, 2/3 on Desktop) -->
        <div class="lg:col-span-2 bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Monthly Performance</h3>
                <i class="fas fa-chart-line text-green-600"></i>
            </div>
            <div style="position: relative; height: 300px;">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>

        <!-- Bookings vs Cancellations Chart -->
        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Comparison</h3>
                <i class="fas fa-chart-bar text-blue-600"></i>
            </div>
            <div style="position: relative; height: 300px;">
                <canvas id="bookingComparisonChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="mt-4 bg-white rounded-lg p-4 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Monthly Revenue Trend</h3>
            <i class="fas fa-money-bill text-green-600"></i>
        </div>
        <div style="position: relative; height: 300px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Performance Chart (Line chart showing bookings)
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: {!! $labels !!},
            datasets: [{
                label: 'Successful Bookings',
                data: {!! $monthlyBookings !!},
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(16, 185, 129)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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

    // Bookings vs Cancellations Chart (Bar chart)
    const comparisonCtx = document.getElementById('bookingComparisonChart').getContext('2d');
    new Chart(comparisonCtx, {
        type: 'bar',
        data: {
            labels: {!! $labels !!},
            datasets: [
                {
                    label: 'Bookings',
                    data: {!! $monthlyBookings !!},
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: 'Cancelled',
                    data: {!! $monthlyCancellations !!},
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
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

    // Revenue Chart (Area chart)
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! $labels !!},
            datasets: [{
                label: 'Revenue (Rp)',
                data: {!! $monthlyRevenue !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                            return 'Rp ' + value.toLocaleString('id-ID');
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
</script>
@endpush
@endsection
