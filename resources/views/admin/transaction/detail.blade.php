@extends('layouts.admin') {{-- Asumsi layout Admin --}}

@section('title', 'Detail Booking ID: ' . $booking->id)

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Detail Booking #{{ $booking->id }}</h1>
        <a href="#" class="flex items-center text-blue-600 hover:text-blue-800">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali 
        </a>
    </div>

    {{-- Notifikasi --}}
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Detail Booking, User, dan Status --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Bagian Ringkasan dan Status --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold border-b pb-3 mb-4">📝 Ringkasan Booking</h2>
                <div class="flex justify-between items-center mb-4">
                    <p class="text-lg font-medium text-gray-700">Total Harga:</p>
                    <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                    <p><strong>Check-in:</strong> {{ $booking->checkin_appointment_start->format('d F Y') }}</p>
                    <p><strong>Check-out:</strong> {{ $booking->checkout_appointment_end->format('d F Y') }}</p>
                    <p><strong>Durasi:</strong> {{ $booking->duration_days }} hari</p>
                    <p><strong>Jumlah Orang:</strong> {{ $booking->amount }}</p>
                    <p><strong>Tanggal Dibuat:</strong> {{ $booking->created_at->format('d F Y H:i') }}</p>
                    <p>
                        <strong>Status Saat Ini:</strong> 
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium 
                            @if($booking->status == 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status == 'completed') bg-blue-100 text-blue-800
                            @elseif($booking->status == 'checked_in') bg-purple-100 text-purple-800
                            @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </p>
                </div>
                @if($booking->note)
                    <div class="mt-4 p-3 bg-gray-50 border-l-4 border-gray-400 text-sm text-gray-700">
                        <p><strong>Request Khusus:</strong></p>
                        <p>{{ $booking->note }}</p>
                    </div>
                @endif
            </div>

            {{-- Bagian Detail Booker --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold border-b pb-3 mb-4">👤 Informasi Booker</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <p><strong>Nama Booker:</strong> {{ $booking->booker_name }}</p>
                    <p><strong>Email Booker:</strong> {{ $booking->booker_email }}</p>
                    <p><strong>Telepon Booker:</strong> {{ $booking->booker_telp }}</p>
                    <p><strong>User Akun (ID):</strong> {{ $booking->user->name ?? 'N/A' }} ({{ $booking->id_user ?? 'Guest' }})</p>
                </div>
            </div>

            {{-- Bagian Item yang Dipesan --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold border-b pb-3 mb-4">🛒 Item yang Dipesan</h2>
                
                @if($booking->packages->count() > 0)
                    <h3 class="font-bold text-md mb-2 text-green-700">Packages (Per {{ $booking->duration_days }} hari):</h3>
                    <ul class="list-disc ml-6 mb-4 space-y-1 text-sm">
                        @foreach($booking->packages as $package)
                            <li>{{ $package->name_package }} (Rp {{ number_format($package->price_publish * $booking->duration_days, 0, ',', '.') }})</li>
                        @endforeach
                    </ul>
                @endif

                @if($booking->products->count() > 0)
                    <h3 class="font-bold text-md mb-2 text-blue-700">Products (Sekali Bayar):</h3>
                    <ul class="list-disc ml-6 mb-4 space-y-1 text-sm">
                        @foreach($booking->products as $product)
                            <li>{{ $product->name }} (Rp {{ number_format($product->price, 0, ',', '.') }})</li>
                        @endforeach
                    </ul>
                @endif

                @if($booking->addons->count() > 0)
                    <h3 class="font-bold text-md mb-2 text-purple-700">Add-ons (Sekali Bayar):</h3>
                    <ul class="list-disc ml-6 space-y-1 text-sm">
                        @foreach($booking->addons as $addon)
                            {{-- Asumsi Anda memiliki cara untuk mendapatkan kuantitas addon dari relasi BookPackageAddon --}}
                            {{-- Untuk kesederhanaan, asumsikan relasi membawa kuantitas --}}
                            @php
                                // Mencoba mencari kuantitas dari pivot table, ini mungkin butuh penyesuaian
                                $pivotData = $booking->addons->find($addon->id)->pivot ?? null;
                                $quantity = $pivotData ? ($pivotData->quantity ?? 1) : 1; 
                            @endphp
                            <li>{{ $addon->addons }} ({{ $quantity }}x | Rp {{ number_format($addon->price * $quantity, 0, ',', '.') }})</li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>

        {{-- Kolom Kanan: Aksi dan Manajemen Status --}}
        <div class="space-y-6">
            
            {{-- Form Approval/Rejection (Jika PENDING) --}}
            @if($booking->status == 'pending')
            <div class="bg-yellow-50 border border-yellow-300 p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold text-yellow-800 mb-4">⚠️ Aksi Approval</h2>
                
                {{-- Tombol Approve --}}
                <form action="{{ route('super_admin.transaction.approve', $booking) }}" method="POST" class="mb-3" onsubmit="return confirm('KONFIRMASI: Setujui booking #{{ $booking->id }}?');">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-150">
                        ✅ Setujui Booking
                    </button>
                </form>

                {{-- Tombol Reject --}}
                <form action="{{ route('super_admin.transaction.reject', $booking) }}" method="POST" onsubmit="return confirm('TOLAK: Batalkan booking #{{ $booking->id }}?');">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition duration-150">
                        ❌ Tolak/Batalkan Booking
                    </button>
                </form>
            </div>
            @endif

            {{-- Form Update Status Manual (Untuk Admin) --}}
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold border-b pb-3 mb-4">⚙️ Ubah Status Manual</h2>
                <form action="#" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Pilih Status Baru</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2">
                            <option value="pending" @if($booking->status == 'pending') selected @endif>Pending</option>
                            <option value="confirmed" @if($booking->status == 'confirmed') selected @endif>Confirmed</option>
                            <option value="checked_in" @if($booking->status == 'checked_in') selected @endif>Checked In</option>
                            <option value="completed" @if($booking->status == 'completed') selected @endif>Completed</option>
                            <option value="cancelled" @if($booking->status == 'cancelled') selected @endif>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition duration-150">
                        Update Status
                    </button>
                </form>
            </div>

            {{-- Opsi Hapus (Hanya untuk Superadmin) --}}
            <div class="bg-white p-6 rounded-lg shadow-md border-t border-gray-200">
                <h2 class="text-xl font-semibold text-red-700 border-b pb-3 mb-4">🗑️ Hapus Booking</h2>
                <form action="#" method="POST" onsubmit="return confirm('PERINGATAN! Anda akan menghapus permanen booking #{{ $booking->id }}. Lanjutkan?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-100 text-red-700 font-semibold rounded-lg hover:bg-red-200 transition duration-150 border border-red-300">
                        Hapus Permanen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection