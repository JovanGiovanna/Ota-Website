@extends('layouts.superadmin')

@section('title', 'Add-on Bookings List (Super Admin)')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Add-on Booking List</h1>
    <p class="text-gray-600 mb-6">Menampilkan semua booking yang Hanya berisi Add-ons (tanpa Package atau Produk).</p>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse($bookings as $booking)
            <div class="p-6 hover:bg-gray-50 flex justify-between items-start">
                <div class="flex-1 min-w-0 pr-4">
                    <h3 class="text-lg font-bold mb-1 truncate text-black">Add-on Booking ({{ $booking->addons->count() }} item)</h3>
                    <p class="text-gray-600 mb-1">Booking ID : #{{ $booking->id }}</p>
                    <p class="text-sm text-gray-700 mb-2">Booker : {{ $booking->booker_name }} ({{ $booking->booker_email }})</p>
                    <div class="mt-2 text-sm text-gray-600"><p>Add-ons : {{ $booking->addons->pluck('addons')->join(', ') }}</p></div>
                </div>

                <div class="text-right flex-shrink-0">
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium 
                        @if($booking->status == 'confirmed') bg-green-100 text-green-800
                        @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>

                    <p class="text-xl font-bold text-blue-600 mt-2">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>

                    <div class="mt-3 flex flex-col space-y-2">
                        <a href="{{ route('super_admin.addon.detail', $booking) }}" class="inline-block px-4 py-2 bg-indigo-500 text-white text-xs font-semibold rounded hover:bg-indigo-600 transition duration-150">Lihat Detail</a>
                        @if($booking->status == 'pending')
                            <form action="{{ route('super_admin.addon.approve', $booking) }}" method="POST" onsubmit="return confirm('Yakin SETUJUI booking add-on ini?');">@csrf<button type="submit" class="w-full px-4 py-2 bg-green-500 text-white text-xs font-semibold rounded hover:bg-green-600 transition duration-150">✅ Approve</button></form>
                            <form action="{{ route('super_admin.addon.reject', $booking) }}" method="POST" onsubmit="return confirm('Yakin TOLAK booking add-on ini?');">@csrf<button type="submit" class="w-full px-4 py-2 bg-red-500 text-white text-xs font-semibold rounded hover:bg-red-600 transition duration-150">❌ Reject</button></form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-500"><p>Tidak ada booking Add-ons yang ditemukan.</p></div>
            @endforelse
        </div>

        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50"><div class="flex justify-end">{{ $bookings->links() }}</div></div>
    </div>
</div>
@endsection
