@extends('layouts.user')

@section('title', 'Booking History')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Booking History</h1>

    <!-- Booking History -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Recent Bookings</h2>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($bookings as $booking)
            <div class="p-6 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold mb-2">
                            @php
                                $hasPackages = $booking->packages->count() > 0;
                                $hasProducts = $booking->products->count() > 0;
                                $hasAddons = $booking->addons->count() > 0;
                                $types = [];
                                if ($hasPackages) $types[] = $booking->packages->count() . ' package' . ($booking->packages->count() > 1 ? 's' : '');
                                if ($hasProducts) $types[] = $booking->products->count() . ' product' . ($booking->products->count() > 1 ? 's' : '');
                                if ($hasAddons) $types[] = $booking->addons->count() . ' addon' . ($booking->addons->count() > 1 ? 's' : '');
                            @endphp
                            @if(count($types) > 1)
                                Mixed Booking ({{ implode(', ', $types) }})
                            @elseif($hasPackages)
                                Package Booking ({{ $types[0] }})
                            @elseif($hasProducts)
                                Product Booking ({{ $types[0] }})
                            @elseif($hasAddons)
                                Addon Booking ({{ $types[0] }})
                            @else
                                Unknown Booking Type
                            @endif
                        </h3>
                        <p class="text-gray-600 mb-2">Booking ID: {{ $booking->booking_code ?? '#' . strtoupper(substr($booking->id, 0, 8)) }}</p>
                        <p class="text-sm text-gray-500">Check-in: {{ $booking->checkin_appointment_start->format('d M Y') }} | Check-out: {{ $booking->checkout_appointment_end->format('d M Y') }}</p>
                        <p class="text-sm text-gray-500">Duration: {{ $booking->duration_days }} days</p>

                        <!-- Show items -->
                        <div class="mt-2 text-sm text-gray-600">
                            @if($booking->packages->count() > 0)
                                <p>Packages: {{ $booking->packages->pluck('name_package')->join(', ') }}</p>
                            @endif
                            @if($booking->products->count() > 0)
                                <p>Products: {{ $booking->products->pluck('name')->join(', ') }}</p>
                            @endif
                            @if($booking->addons->count() > 0)
                                <p>Add-ons: {{ $booking->addons->pluck('addons')->join(', ') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                            @if($booking->status == 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status == 'completed') bg-blue-100 text-blue-800
                            @elseif($booking->status == 'checked_in') bg-purple-100 text-purple-800
                            @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                        <p class="text-lg font-bold text-blue-600 mt-2">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                        <a href="{{ route('user.detail_history', $booking) }}" class="inline-block mt-2 text-blue-600 hover:underline text-sm">View Details</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-500">
                <p>No bookings found.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-600">Showing {{ $bookings->count() }} bookings</p>
                <div class="flex space-x-2">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
