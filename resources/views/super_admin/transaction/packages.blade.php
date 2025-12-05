@extends('layouts.superadmin')

@section('title', 'Package Booking Approval (Super Admin)')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Package Booking Approval List</h1>
    <p class="text-gray-600 mb-6">Showing only Pending bookings that contain Packages.</p>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse($bookings as $booking)
            <div class="p-6 hover:bg-gray-50 flex justify-between items-start">
                <div class="flex-1 min-w-0 pr-4">
                    <h3 class="text-lg font-bold mb-1 truncate">Package Booking ({{ $booking->packages->count() }} packages)</h3>
                    <p class="text-gray-600 mb-1">Booking ID : #{{ $booking->id }}</p>
                    <p class="text-sm text-gray-700 mb-2">User : {{ $booking->user->name ?? $booking->booker_name }} ({{ $booking->booker_email }})</p>
                    <div class="mt-2 text-sm text-gray-600"><p>Packages: {{ $booking->packages->pluck('name_package')->join(', ') }}</p></div>
                </div>

                <div class="text-right flex-shrink-0">
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                    <p class="text-xl font-bold text-blue-600 mt-2">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>

                    <div class="mt-3 flex flex-col space-y-2">
                        <a href="{{ route('super_admin.package.index', $booking) }}" class="inline-block text-blue-600 hover:underline text-sm">View Details</a>
                        <form action="{{ route('super_admin.package.approve', $booking) }}" method="POST" onsubmit="return confirm('Yakin SETUJUI booking paket ini?');">@csrf<button type="submit" class="w-full px-4 py-2 bg-green-500 text-white text-xs font-semibold rounded hover:bg-green-600 transition duration-150">✅ Approve</button></form>
                        <form action="{{ route('super_admin.package.reject', $booking) }}" method="POST" onsubmit="return confirm('Yakin TOLAK booking paket ini?');">@csrf<button type="submit" class="w-full px-4 py-2 bg-red-500 text-white text-xs font-semibold rounded hover:bg-red-600 transition duration-150">❌ Reject</button></form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-500"><p>No **Pending** package bookings found.</p></div>
            @endforelse
        </div>

        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50"><div class="flex justify-end">{{ $bookings->links() }}</div></div>
    </div>
</div>
@endsection
