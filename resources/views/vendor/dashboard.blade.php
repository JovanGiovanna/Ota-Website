@extends('layouts.vendor')

@section('title', 'Vendor Dashboard')

@section('welcome')
Welcome back, {{ Auth::guard('vendor')->check() ? Auth::guard('vendor')->user()->name : (Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'Vendor') }}!
@endsection

@section('logout_route', route('vendor.logout'))

@section('content')
<div class="space-y-6">
    <!-- Alert Messages -->
    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @php
    $vendor = Auth::guard('vendor')->user() ?? Auth::guard('super_admin')->user();
    $hasVendorInfo = \App\Models\VendorInfo::where('id_vendor', $vendor->id)->exists();
    @endphp

    @if (!$hasVendorInfo)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-yellow-800">
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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Total Bookings Card -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Total Bookings</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-2">{{ $totalBookings ?? 0 }}</p>
                    <p class="text-sm text-gray-500 mt-1">Active bookings</p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar-check text-emerald-600 text-lg"></i>
                </div>
            </div>
        </div>

        <!-- Revenue Card (Nominal Transaksi) -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Nominal Transaksi</p>
                    <p class="text-2xl font-bold text-blue-600 mt-2">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-500 mt-1">Total nominal dari transaksi yang selesai</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-dollar-sign text-blue-600 text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="space-y-6">
        <!-- Performance Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Monthly Performance Chart -->
            <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">Monthly Performance</h3>
                    <i class="fas fa-chart-line text-emerald-600 text-lg"></i>
                </div>
                <div class="relative" style="height: 320px;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            <!-- Bookings vs Cancellations Chart -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">Comparison</h3>
                    <i class="fas fa-chart-bar text-blue-600 text-lg"></i>
                </div>
                <div class="relative" style="height: 320px;">
                    <canvas id="bookingComparisonChart"></canvas>
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
                label: 'Nominal Transaksi (Rp)',
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
