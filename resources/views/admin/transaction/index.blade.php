@extends('layouts.admin') {{-- Asumsi layout untuk superadmin/admin --}}

@section('title', 'Booking Transactions & Approval')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Booking Transaction List</h1>
    {{-- Notifikasi Sukses/Error --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold">All Bookings (Latest)</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($bookings as $booking)
            <div class="p-6 hover:bg-gray-50 flex justify-between items-start">
                <div class="flex-1 min-w-0 pr-4">
                    {{-- Tipe Booking dan ID --}}
                    <h3 class="text-lg font-semibold mb-1 truncate">
                        @if($booking->packages->count() > 0) Package Booking @elseif($booking->products->count() > 0) Product Booking @elseif($booking->addons->count() > 0) Addon Booking @else Mixed Booking @endif
                    </h3>
                    <p class="text-gray-600 mb-1">Booking ID : #{{ $booking->booking_code }}</p>
                    
                    {{-- Info User --}}
                    <p class="text-sm text-gray-700 mb-2">
                        User : {{ $booking->user->name ?? $booking->booker_name }} ({{ $booking->booker_email }})
                    </p>

                    {{-- Tanggal dan Durasi --}}
                    <p class="text-sm text-gray-500">Check-in : {{ $booking->checkin_appointment_start->format('d M Y') }} | Check-out: {{ $booking->checkout_appointment_end->format('d M Y') }}</p>
                    <p class="text-sm text-gray-500">Duration : {{ $booking->duration_days }} days</p>

                    {{-- Item yang dibooking --}}
                    <div class="mt-2 text-sm text-gray-600">
                        @if($booking->packages->count() > 0) <p>Packages: {{ $booking->packages->pluck('name_package')->join(', ') }}</p> @endif
                        @if($booking->products->count() > 0) <p>Products: {{ $booking->products->pluck('name')->join(', ') }}</p> @endif
                        @if($booking->addons->count() > 0) <p>Add-ons: {{ $booking->addons->pluck('addons')->join(', ') }}</p> @endif
                    </div>
                </div>

                <div class="text-right flex-shrink-0">
                    {{-- Status Badge --}}
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                        @if($booking->status == 'confirmed') bg-green-100 text-green-800
                        @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($booking->status == 'completed') bg-blue-100 text-blue-800
                        @elseif($booking->status == 'checked_in') bg-purple-100 text-purple-800
                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>

                    <p class="text-xl font-bold text-blue-600 mt-2">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>

                    {{-- Tombol Aksi Approval (Hanya muncul jika status PENDING) --}}
                    <div class="mt-3 flex flex-col space-y-2">
                        {{-- Ganti route ini dengan route detail Admin/Superadmin jika ada --}}
                        <a href="{{ route('super_admin.transaction.detail', $booking) }}" class="inline-block text-blue-600 hover:underline text-sm">View Details</a> 

                        @if($booking->status == 'pending')
                            {{-- Tombol Approve --}}
                            <form action="{{ route('super_admin.transaction.approve', $booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI booking ini?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white text-xs font-semibold rounded hover:bg-green-600 transition duration-150">
                                    ✅ Approve
                                </button>
                            </form>

                            {{-- Tombol Reject --}}
                            <form action="{{ route('super_admin.transaction.reject', $booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK/MEMBATALKAN booking ini?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white text-xs font-semibold rounded hover:bg-red-600 transition duration-150">
                                    ❌ Reject
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-500">
                <p>No bookings found.</p>
            </div>
            @endforelse
        </div>

        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-600">Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} bookings</p>
                <div class="flex space-x-2">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection