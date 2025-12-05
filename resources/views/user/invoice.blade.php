<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $booking->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #2563eb;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .invoice-details div {
            flex: 1;
        }
        .invoice-details h3 {
            margin: 0 0 10px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>OTA WEBSITE</h1>
        <p>Invoice #{{ $booking->id }}</p>
    </div>

    <div class="invoice-details">
        <div>
            <h3>Bill To:</h3>
            <p>{{ $booking->booker_name }}</p>
            <p>{{ $booking->booker_email }}</p>
            <p>{{ $booking->booker_telp }}</p>
        </div>
        <div>
            <h3>Invoice Details:</h3>
            <p><strong>Invoice Date:</strong> {{ now()->format('d M Y') }}</p>
            <p><strong>Booking Date:</strong> {{ $booking->created_at->format('d M Y') }}</p>
            <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $booking->status)) }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @if($booking->packages->count() > 0)
                @foreach($booking->packages as $package)
                <tr>
                    <td>{{ $package->name_package }}</td>
                    <td>1</td>
                    <td>Rp {{ number_format($package->price_publish, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($package->price_publish, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @endif

            @if($booking->products->count() > 0)
                @foreach($booking->products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>1</td>
                    <td>Rp {{ number_format($product->pivot->total_price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($product->pivot->total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @endif

            @if($booking->addons->count() > 0)
                @foreach($booking->addons as $addon)
                <tr>
                    <td>{{ $addon->addons }}</td>
                    <td>{{ $addon->pivot->quantity ?? 1 }}</td>
                    <td>Rp {{ number_format($addon->finalPrice, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($addon->finalPrice * ($addon->pivot->quantity ?? 1), 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="total">
        <p>Total Amount: Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>OTA Website - Your Travel Partner</p>
    </div>
</body>
</html>
