@extends('layouts.vendor')

@section('title', 'Stock Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-2xl font-semibold text-gray-900 mb-4">Stock Management</h1>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md">{{ session('success') }}</div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($products as $product)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500">{{ $product->description ? Str::limit($product->description, 60) : 'No description' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->jumlah ?? 0 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <form action="{{ route('vendor.products.stock', $product->id) }}" method="POST" class="inline-flex items-center space-x-2">
                                    @csrf
                                    <input type="number" name="amount" min="1" value="1" class="w-20 px-2 py-1 border border-gray-300 rounded-md text-sm">
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">Add</button>
                                </form>
                                <a href="{{ route('vendor.products.edit', $product->id) }}" class="ml-3 text-yellow-600 hover:text-yellow-800">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No products found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 bg-white border-t border-gray-200">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
