@extends('layouts.superadmin')

@section('title', 'Super Admin Dashboard')

@section('welcome')
Welcome, {{ Auth::guard('super_admin')->check() ? Auth::guard('super_admin')->user()->name : 'Super Admin' }}!
@endsection

@section('logout_route', route('super_admin.logout'))


@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="border-4 border-dashed border-gray-200 rounded-lg">
        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Booking Chart -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Monthly Bookings</h3>
                <canvas id="bookingChart"></canvas>
            </div>

            <!-- Revenue Chart -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Monthly Revenue</h3>
                <canvas id="revenueChart"></canvas>
            </div>

            <!-- Status Chart -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Booking Status</h3>
                <canvas id="statusChart"></canvas>
            </div>

            <!-- Facilities Chart -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Monthly Facility Bookings</h3>
                <canvas id="facilitiesChart"></canvas>
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
                data: {!! json_encode($revenueData) !!},
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
            labels: {!! json_encode(['Confirmed', 'Pending', 'Cancelled', 'Completed']) !!},
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
            aspectRatio: 1,
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

    // Facilities Chart
    const facilitiesCtx = document.getElementById('facilitiesChart').getContext('2d');
    new Chart(facilitiesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Facility Bookings',
                data: {!! json_encode($facilityData) !!},
                borderColor: 'rgb(147, 51, 234)',
                backgroundColor: 'rgba(147, 51, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgb(147, 51, 234)',
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
</script>
@endpush
@endsection
