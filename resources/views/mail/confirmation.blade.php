<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Pemesanan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            color: #333;
            padding: 30px;
        }
        .container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: auto;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 150px;
        }
        .btn {
            background: #007bff;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            display: inline-block;
        }
        .panel {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">
        <img src="{{ asset('logo/PointerLogo.png') }}" alt="{{ config('app.name') }} Logo">
    </div>

    <h2>Halo, {{ $user->name ?? 'Pelanggan' }}!</h2>
    <p>Terima kasih! Pemesanan Anda telah <strong>berhasil dikonfirmasi.</strong></p>

    <div class="panel">
        <p><strong>No. Pemesanan:</strong> #{{ $booking->booking_id }}</p>
        <p><strong>Tanggal Pemesanan:</strong> {{ $booking->created_at->format('d F Y H:i') }}</p>
        <p><strong>Total Pembayaran:</strong> Rp {{ number_format($booking->total_price ?? 0, 0, ',', '.') }}</p>
    </div>

    <a href="{{ url('/user/bookings/' . $booking->booking_id) }}" class="btn">Lihat Detail Pemesanan</a>

    <p style="margin-top:20px;">Kami akan segera memproses pesanan Anda. Jika ada pertanyaan, jangan ragu untuk menghubungi kami.</p>

    <p>Salam hangat,<br><strong>Tim {{ config('app.name') }}</strong></p>
</div>
</body>
</html>
