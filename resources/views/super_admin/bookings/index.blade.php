@extends('layouts.superadmin') {{-- Booking management for Super Admin --}}

@section('title', 'Manage Bookings (Super Admin)')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Bookings</h1>
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">All Bookings (Latest)</h2>
                <form method="GET" action="" class="flex items-center space-x-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code/name/email" class="border rounded px-2 py-1 text-sm" />
                    <select name="type" class="border rounded px-2 py-1 text-sm">
                        <option value="">All Types</option>
                        <option value="package" @if(request('type')=='package') selected @endif>Package</option>
                        <option value="product" @if(request('type')=='product') selected @endif>Product</option>
                        <option value="addon" @if(request('type')=='addon') selected @endif>Addon</option>
                    </select>
                    <select name="status" class="border rounded px-2 py-1 text-sm">
                        <option value="">All Status</option>
                        <option value="pending" @if(request('status')=='pending') selected @endif>Pending</option>
                        <option value="book" @if(request('status')=='book') selected @endif>Book</option>
                        <option value="paid" @if(request('status')=='paid') selected @endif>Paid</option>
                        <option value="payment_return" @if(request('status')=='payment_return') selected @endif>Payment Return</option>
                        <option value="confirmed" @if(request('status')=='confirmed') selected @endif>Confirmed</option>
                        <option value="completed" @if(request('status')=='completed') selected @endif>Completed</option>
                        <option value="cancelled" @if(request('status')=='cancelled') selected @endif>Cancelled</option>
                    </select>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded px-2 py-1 text-sm" />
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="border rounded px-2 py-1 text-sm" />
                    <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Filter</button>
                </form>
            </div>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($bookings as $booking)
            <div class="p-6 hover:bg-gray-50 flex justify-between items-start">
                <div class="flex-1 min-w-0 pr-4">
                    <h3 class="text-lg font-semibold mb-1 truncate">
                        @if($booking->packages->count() > 0) Package Booking @elseif($booking->products->count() > 0) Product Booking @elseif($booking->addons->count() > 0) Addon Booking @else Mixed Booking @endif
                    </h3>
                    <p class="text-gray-600 mb-1">Booking ID : #{{ $booking->booking_code }}</p>
                    <p class="text-sm text-gray-700 mb-2">User : {{ $booking->user->name ?? $booking->booker_name }} ({{ $booking->booker_email }})</p>
                    <p class="text-sm text-gray-500">Check-in : {{ $booking->checkin_appointment_start->format('d M Y') }} | Check-out: {{ $booking->checkout_appointment_end->format('d M Y') }}</p>
                    <div class="mt-2 text-sm text-gray-600">
                        @if($booking->packages->count() > 0) <p>Packages: {{ $booking->packages->pluck('name_package')->join(', ') }}</p> @endif
                        @if($booking->products->count() > 0) <p>Products: {{ $booking->products->pluck('name')->join(', ') }}</p> @endif
                        @if($booking->addons->count() > 0) <p>Add-ons: {{ $booking->addons->pluck('addons')->join(', ') }}</p> @endif
                    </div>
                </div>

                <div class="text-right flex-shrink-0">
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

                    <div class="mt-3 flex flex-col space-y-2">
                        <a href="{{ route('super_admin.bookings.detail', $booking) }}" class="inline-block text-blue-600 hover:underline text-sm">View Details</a>

                        @if($booking->status == 'pending')
                            <form action="{{ route('super_admin.bookings.approve', $booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI booking ini?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white text-xs font-semibold rounded hover:bg-green-600 transition duration-150">✅ Approve</button>
                            </form>
                            <form action="{{ route('super_admin.bookings.reject', $booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK/MEMBATALKAN booking ini?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white text-xs font-semibold rounded hover:bg-red-600 transition duration-150">❌ Reject</button>
                            </form>
                        @endif

                        @if($booking->status == 'paid')
                            <form action="{{ route('super_admin.bookings.verify_payment', $booking) }}" method="POST" onsubmit="return confirm('Verifikasi pembayaran dan tandai sebagai completed?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded hover:bg-blue-700 transition duration-150">✔️ Verify Payment</button>
                            </form>
                        @endif

                        @if($booking->status == 'payment_return')
                            <form action="{{ route('super_admin.bookings.process_refund', $booking) }}" method="POST" onsubmit="return confirm('Proses pengembalian dana dan tandai selesai?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white text-xs font-semibold rounded hover:bg-yellow-700 transition duration-150">💸 Process Refund</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-500"><p>No bookings found.</p></div>
            @endforelse
        </div>

        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-600">Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} bookings</p>
                <div class="flex space-x-2">{{ $bookings->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
