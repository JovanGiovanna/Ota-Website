<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transactions Report</title>
    <style>
        body { font-family: DejaVu Sans, DejaVu Sans Condensed, Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f4f4f4; }
        h2 { margin-bottom: 8px; }
    </style>
</head>
<body>
    <h2>Transactions Report</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Booking Code</th>
                <th>Type</th>
                <th>Item</th>
                <th>Customer</th>
                <th>Transaction Date</th>
                <th class="text-right">Price (Rp)</th>
                <th>Quantity</th>
                <th>Effect</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $i => $t)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $t->booking_code ?? $t->booking_id }}</td>
                <td>{{ $t->type }}</td>
                <td>{{ $t->item_name }}</td>
                <td>{{ $t->customer_name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($t->transaction_date)->format('Y-m-d H:i') }}</td>
                <td style="text-align:right;">Rp {{ number_format($t->price ?? 0, 0, ',', '.') }}</td>
                <td>{{ $t->quantity ?? 1 }}</td>
                <td>
                    @php
                        $st = strtolower($t->booking_status ?? '');
                        $amt = $t->amount ?? 1;
                        $pr = $t->price ?? 0;
                        if ($st === 'book') {
                            $effect = "Stock -{$amt}";
                        } elseif ($st === 'paid') {
                            $effect = 'Nominal +Rp ' . number_format($pr ?? 0, 0, ',', '.');
                        } elseif ($st === 'cancelled') {
                            $effect = "Stock +{$amt}";
                        } elseif ($st === 'payment return' || $st === 'payment_return') {
                            $effect = 'Nominal -Rp ' . number_format($pr ?? 0, 0, ',', '.');
                        } else {
                            $effect = '-';
                        }
                    @endphp
                    {{ $effect }}
                </td>
                <td>{{ ucfirst($t->booking_status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
