@extends('layouts.vendor')

@section('title', 'Transactions Report')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Transactions Report</h1>
        <a href="{{ route('vendor.products') }}" class="px-3 py-2 bg-emerald-600 text-white rounded-md">Back to Products</a>
    </div>

    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 mb-6">
        <form method="GET" action="{{ route('vendor.transactions.report') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-600">Item Name</label>
                <input type="text" name="product_name" value="{{ old('product_name', $product_name ?? '') }}" class="w-full mt-1 px-3 py-2 border rounded-md">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600">Date From</label>
                <input type="date" name="date_from" value="{{ old('date_from', $date_from ?? '') }}" class="w-full mt-1 px-3 py-2 border rounded-md">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600">Date To</label>
                <input type="date" name="date_to" value="{{ old('date_to', $date_to ?? '') }}" class="w-full mt-1 px-3 py-2 border rounded-md">
            </div>
            <div>
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md">Filter</button>
            </div>
            <div class="md:col-span-4 text-right">
                <a href="{{ route('vendor.transactions.report.export', array_merge(request()->all(), ['format' => 'csv'])) }}" class="inline-block mr-2 bg-gray-100 text-gray-700 px-3 py-2 rounded-md">Export CSV</a>
                <a href="{{ route('vendor.transactions.report.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" class="inline-block bg-gray-100 text-gray-700 px-3 py-2 rounded-md">Export PDF</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price (Rp)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effect</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $index => $t)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transactions->firstItem() + $index }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $t->type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $t->item_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($t->transaction_date)->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $t->customer_name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">Rp {{ number_format($t->price ?? 0, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $t->quantity ?? 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($t->booking_status) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @php
                                $st = strtolower($t->booking_status ?? '');
                                $amt = $t->quantity ?? 1;
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">No transactions found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
