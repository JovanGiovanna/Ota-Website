@extends('layouts.vendor')

@section('title', 'My Products')

@section('welcome')
Welcome, {{ Auth::guard('vendor')->check() ? Auth::guard('vendor')->user()->name : 'Vendor' }}!
@endsection

@section('logout_route', route('vendor.logout'))

@section('content')
<div class="px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Products</h1>
        <a href="{{ route('vendor.products.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium">
            Add New Product
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md">{{ session('success') }}</div>
            @endif
            @if($products && count($products) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                {{-- KOLOM HARGA BARU --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NTA / Basic Price / Tax</th>
                                {{-- KOLOM DISKON BARU --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                                {{-- END KOLOM HARGA BARU --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($products as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->images && is_array($product->images) && count($product->images) > 0)
                                        <div class="flex space-x-1">
                                            @foreach(array_slice($product->images, 0, 2) as $image)
                                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-lg">
                                            @endforeach
                                            @if(count($product->images) > 2)
                                                <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center text-xs font-medium text-gray-600">
                                                    +{{ count($product->images) - 2 }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($product->description ?? 'N/A', 50) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($product->address ?? $product->location ?? 'N/A', 50) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->jumlah ?? 0 }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <form action="{{ route('vendor.products.stock', $product->id) }}" method="POST" class="inline-flex items-center space-x-2">
                                            @csrf
                                            <input type="number" name="amount" min="1" value="1" class="w-20 px-2 py-1 border border-gray-300 rounded-md text-sm">
                                            <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">Add</button>
                                        </form>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category ? $product->category->categories : 'N/A' }}</td>
                                
                                {{-- ISI KOLOM HARGA BARU --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="font-bold text-gray-900">
                                        NTA: Rp {{ number_format($product->nta ?? $product->basic_price ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Basic: Rp {{ number_format($product->basic_price ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Tax: {{ number_format($product->tax_rate ?? 0, 2, ',', '.') }}%
                                    </div>
                                </td>
                                {{-- KOLOM DISKON BARU (Menampilkan Rupiah Diskon Terhitung) --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if(($product->discount_amount ?? 0) > 0)
                                        <span class="text-red-600 font-semibold">
                                            - Rp {{ number_format($product->discount_amount, 0, ',', '.') }}
                                        </span>
                                        <div class="text-xs text-gray-500">
                                            ({{ $product->discount_type === 'percentage' ? $product->discount_value . '%' : 'Fixed' }})
                                        </div>
                                    @else
                                        <span class="text-gray-500">None</span>
                                    @endif
                                </td>
                                {{-- END ISI KOLOM HARGA BARU --}}
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($product->status == 'available')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                    @elseif($product->status == 'unavailable')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Unavailable</span>
                                    @elseif($product->status == 'draft')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($product->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('vendor.products.edit', $product->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <form method="POST" action="{{ route('vendor.products.destroy', $product->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 bg-white border-t border-gray-200">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No products</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new product.</p>
                    <div class="mt-6">
                        <a href="{{ route('vendor.products.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Product
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection