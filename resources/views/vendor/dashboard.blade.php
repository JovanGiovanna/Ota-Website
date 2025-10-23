@extends('layouts.vendor')

@section('title', 'Vendor Dashboard')

@section('welcome')
Welcome back, {{ Auth::guard('vendor')->check() ? Auth::guard('vendor')->user()->name : (Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'Vendor') }}!
@endsection

@section('logout_route', route('vendor.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="border-4 border-dashed border-gray-200 rounded-lg">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-emerald-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                        <p class="text-3xl font-bold text-emerald-600">{{ $totalBookings ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calendar-check text-emerald-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center">
                    <span class="text-green-500 text-sm font-medium">+15%</span>
                    <span class="text-gray-500 text-sm ml-2">from last month</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg border border-blue-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Revenue</p>
                        <p class="text-3xl font-bold text-blue-600">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center">
                    <span class="text-green-500 text-sm font-medium">+22%</span>
                    <span class="text-gray-500 text-sm ml-2">from last month</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg border border-purple-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Services</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $activeServices ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-concierge-bell text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center">
                    <span class="text-green-500 text-sm font-medium">+5%</span>
                    <span class="text-gray-500 text-sm ml-2">from last month</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-lg border border-orange-100 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Rating</p>
                        <p class="text-3xl font-bold text-orange-600">{{ $averageRating ?? 0 }}/5</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-star text-orange-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center">
                    <span class="text-green-500 text-sm font-medium">+0.2</span>
                    <span class="text-gray-500 text-sm ml-2">from last month</span>
                </div>
            </div>
        </div>

        <!-- Performance Chart -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-800">Monthly Performance</h3>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600"></i>
                </div>
            </div>
            <canvas id="performanceChart" class="w-full h-64"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Performance Chart
    const performanceCtx = document.getElementById('performanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!},
            datasets: [{
                label: 'Bookings',
                data: {!! json_encode([12, 19, 15, 25, 22, 30]) !!},
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(16, 185, 129)',
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
