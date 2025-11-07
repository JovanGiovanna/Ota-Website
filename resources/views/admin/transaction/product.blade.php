@extends('layouts.admin')

@section('title', 'Product Bookings List')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6"> Product Booking List</h1>
    <p class="text-gray-600 mb-6">Menampilkan semua booking yang Hanya berisi Produk.</p>
    
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
                    
                    {{-- Judul --}}
                    <h3 class="text-lg font-bold mb-1 truncate text-black">
                        Product Booking ({{ $booking->products->count() }} item)
                    </h3>
                    <p class="text-gray-600 mb-1">Booking ID : #{{ $booking->id }}</p>
                    
                    {{-- Info User --}}
                    <p class="text-sm text-gray-700 mb-2">
                        Booker : {{ $booking->booker_name }} ({{ $booking->booker_email }})
                    </p>

                    {{-- Tanggal --}}
                    <p class="text-sm text-gray-500">Tanggal Booking : {{ $booking->created_at->format('d F Y') }}</p>
                    
                    {{-- Item yang dibooking --}}
                    <div class="mt-2 text-sm text-gray-600">
                        <p>Products : {{ $booking->products->pluck('name')->join(', ') }}</p>
                    </div>
                </div>

                <div class="text-right flex-shrink-0">
                    {{-- Status Badge --}}
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium 
                        @if($booking->status == 'confirmed') bg-green-100 text-green-800
                        @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>

                    <p class="text-xl font-bold text-blue-600 mt-2">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>

                    {{-- Tombol Aksi --}}
                    <div class="mt-3 flex flex-col space-y-2">
                        {{-- MENGGUNAKAN NAMA RUTE KHUSUS PRODUCTS --}}
                        <a href="{{ route('super_admin.product.detail', $booking) }}" class="inline-block px-4 py-2 bg-indigo-500 text-white text-xs font-semibold rounded hover:bg-indigo-600 transition duration-150">
                            Lihat Detail
                        </a> 
                        
                        {{-- Tambahkan tombol approve/reject jika diperlukan untuk produk --}}
                        @if($booking->status == 'pending')
                            <form action="{{ route('super_admin.product.approve', $booking) }}" method="POST" onsubmit="return confirm('Yakin SETUJUI booking ini?');">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white text-xs font-semibold rounded hover:bg-green-600 transition duration-150">
                                    ✅ Approve
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-500">
                <p>Tidak ada booking produk yang ditemukan.</p>
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