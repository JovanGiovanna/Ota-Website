@extends('layouts.admin')

@section('title', 'Package Booking Approval')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Package Booking Approval List</h1>
    <p class="text-gray-600 mb-6">Showing only Pending bookings that contain Packages.</p>
    
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
        <div class="divide-y divide-gray-200">
            @forelse($bookings as $booking)
            <div class="p-6 hover:bg-gray-50 flex justify-between items-start">
                <div class="flex-1 min-w-0 pr-4">
                    {{-- Judul tetap Package Booking karena sudah difilter --}}
                    <h3 class="text-lg font-bold mb-1 truncate">
                        Package Booking ({{ $booking->packages->count() }} packages)
                    </h3>
                    <p class="text-gray-600 mb-1">Booking ID : #{{ $booking->id }}</p>
                    
                    {{-- Info User --}}
                    <p class="text-sm text-gray-700 mb-2">
                        User : {{ $booking->user->name ?? $booking->booker_name }} ({{ $booking->booker_email }})
                    </p>

                    {{-- Tanggal dan Durasi --}}
                    <p class="text-sm text-gray-500">Check-in : {{ $booking->checkin_appointment_start->format('d M Y') }} | Check-out: {{ $booking->checkout_appointment_end->format('d M Y') }}</p>
                    
                    {{-- Item yang dibooking (Hanya tampilkan Packages karena sudah difilter) --}}
                    <div class="mt-2 text-sm text-gray-600">
                        <p>Packages: {{ $booking->packages->pluck('name_package')->join(', ') }}</p>
                        {{-- Opsional: Tampilkan item lain jika ada, tapi fokus utama packages --}}
                        @if($booking->products->count() > 0) <p class="text-xs text-yellow-600">Includes {{ $booking->products->count() }} Products</p> @endif
                        @if($booking->addons->count() > 0) <p class="text-xs text-yellow-600">Includes {{ $booking->addons->count() }} Add-ons</p> @endif
                    </div>
                </div>

                <div class="text-right flex-shrink-0">
                    {{-- Status Badge (Harusnya selalu PENDING di halaman ini) --}}
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>

                    <p class="text-xl font-bold text-blue-600 mt-2">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>

                    {{-- Tombol Aksi Approval --}}
                    <div class="mt-3 flex flex-col space-y-2">
                        <a href="{{ route('super_admin.package.index', $booking) }}" class="inline-block text-blue-600 hover:underline text-sm">View Details</a> 

                        {{-- Tombol Approve (menggunakan route packages.approve) --}}
                        <form action="{{ route('super_admin.package.approve', $booking) }}" method="POST" onsubmit="return confirm('Yakin SETUJUI booking paket ini?');">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white text-xs font-semibold rounded hover:bg-green-600 transition duration-150">
                                ✅ Approve
                            </button>
                        </form>

                        {{-- Tombol Reject (menggunakan route packages.reject) --}}
                        <form action="{{ route('super_admin.package.reject', $booking) }}" method="POST" onsubmit="return confirm('Yakin TOLAK booking paket ini?');">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white text-xs font-semibold rounded hover:bg-red-600 transition duration-150">
                                ❌ Reject
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-500">
                <p>No **Pending** package bookings found.</p>
            </div>
            @endforelse
        </div>

        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-end">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</div>
@endsection