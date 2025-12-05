@extends('layouts.superadmin')

@section('title', 'Detail Booking (Super Admin)')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Detail Booking</h1>
        <a href="{{ route('super_admin.bookings') }}" class="flex items-center text-blue-600 hover:text-blue-800">Kembali</a>
    </div>

    @if(session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert"><span class="block sm:inline">{{ session('success') }}</span></div>@endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold border-b pb-3 mb-4">Ringkasan Booking</h2>
                <div class="flex justify-between items-center mb-4"><p class="text-lg font-medium text-gray-700">Total Harga:</p><p class="text-2xl font-bold text-blue-600">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p></div>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                    <p><strong>Check-in:</strong> {{ $booking->checkin_appointment_start->format('d F Y') }}</p>
                    <p><strong>Check-out:</strong> {{ $booking->checkout_appointment_end->format('d F Y') }}</p>
                    <p><strong>Durasi:</strong> {{ $booking->duration_days }} hari</p>
                    <p><strong>Jumlah Orang:</strong> {{ $booking->amount }}</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold border-b pb-3 mb-4">Informasi Booker</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                    <p><strong>Nama Booker:</strong> {{ $booking->booker_name }}</p>
                    <p><strong>Email Booker:</strong> {{ $booking->booker_email }}</p>
                    <p><strong>Telepon Booker:</strong> {{ $booking->booker_telp }}</p>
                    <p><strong>User Akun (ID):</strong> {{ $booking->user->name ?? 'N/A' }} ({{ $booking->id_user ?? 'Guest' }})</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold border-b pb-3 mb-4">Item yang Dipesan</h2>
                @if($booking->packages->count() > 0)
                    <h3 class="font-bold text-md mb-2 text-green-700">Packages:</h3>
                    <ul class="list-disc ml-6 mb-4">@foreach($booking->packages as $package)<li>{{ $package->name_package }}</li>@endforeach</ul>
                @endif
                @if($booking->products->count() > 0)
                    <h3 class="font-bold text-md mb-2 text-blue-700">Products:</h3>
                    <ul class="list-disc ml-6 mb-4">@foreach($booking->products as $product)<li>{{ $product->name }}</li>@endforeach</ul>
                @endif
                @if($booking->addons->count() > 0)
                    <h3 class="font-bold text-md mb-2 text-purple-700">Add-ons:</h3>
                    <ul class="list-disc ml-6">@foreach($booking->addons as $addon)<li>{{ $addon->addons }}</li>@endforeach</ul>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            @if($booking->status == 'pending')
            <div class="bg-yellow-50 border border-yellow-300 p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold text-yellow-800 mb-4">Aksi Approval</h2>
                <form action="{{ route('super_admin.bookings.approve', $booking) }}" method="POST" class="mb-3">@csrf<button type="submit" class="w-full px-4 py-3 bg-green-600 text-white font-semibold rounded-lg">✅ Setujui Booking</button></form>
                <form action="{{ route('super_admin.bookings.reject', $booking) }}" method="POST">@csrf<button type="submit" class="w-full px-4 py-3 bg-red-600 text-white font-semibold rounded-lg">❌ Tolak/Batalkan Booking</button></form>
            </div>
            @endif

            @if($booking->status == 'paid')
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold text-green-700 mb-4">Pembayaran</h2>
                <form action="{{ route('super_admin.bookings.verify_payment', $booking) }}" method="POST">@csrf<button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white font-semibold rounded-lg">✔️ Verifikasi Pembayaran (Tandai Completed)</button></form>
            </div>
            @endif

            @if($booking->status == 'payment_return')
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold text-red-700 mb-4">Pengembalian Dana</h2>
                <form action="{{ route('super_admin.bookings.process_refund', $booking) }}" method="POST">@csrf<button type="submit" class="w-full px-4 py-3 bg-red-600 text-white font-semibold rounded-lg">💸 Proses Pengembalian Dana</button></form>
            </div>
            @endif

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold border-b pb-3 mb-4">Ubah Status Manual</h2>
                <form action="{{ route('admin.transaction.updateStatus', $booking) }}" method="POST">@csrf @method('PUT')<div class="mb-4"><label for="status" class="block text-sm font-medium text-gray-700 mb-1">Pilih Status Baru</label><select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md p-2"><option value="pending" @if($booking->status == 'pending') selected @endif>Pending</option><option value="confirmed" @if($booking->status == 'confirmed') selected @endif>Confirmed</option><option value="maintenance" @if($booking->status == 'maintenance') selected @endif>Maintenance</option><option value="completed" @if($booking->status == 'completed') selected @endif>Completed</option><option value="cancelled" @if($booking->status == 'cancelled') selected @endif>Cancelled</option></select></div><button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg">Update Status</button></form>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md border-t border-gray-200">
                <h2 class="text-xl font-semibold text-red-700 border-b pb-3 mb-4">Hapus Booking</h2>
                <form action="#" method="POST" onsubmit="return confirm('PERINGATAN! Anda akan menghapus permanen booking. Lanjutkan?');">@csrf @method('DELETE')<button type="submit" class="w-full px-4 py-2 bg-red-100 text-red-700 font-semibold rounded-lg">Hapus Permanen</button></form>
            </div>
        </div>
    </div>
</div>
@endsection
