@extends('layouts.superadmin')

@section('title', 'Vendor Products - ' . $vendor->name)

@section('welcome')
Vendor Products Management
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Product Image</h3>
            <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="text-center">
            <img id="modalImage" src="" alt="Product Image" class="max-w-full max-h-96 mx-auto rounded-lg shadow-lg">
        </div>
    </div>
</div>

<!-- Product Details Modal -->
<div id="productDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white max-h-96 overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Product Details</h3>
            <button onclick="closeProductDetailsModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="productDetailsContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>
<div class="px-4 py-6 sm:px-0">
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Products for {{ $vendor->name }}</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage products for this vendor</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('super_admin.vendors') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Vendors
                </a>
            </div>
        </div>

        <!-- Products Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $product->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Str::limit($product->description ?? 'N/A', 50) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->category->categories ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}</td>
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
                            <div class="flex justify-end space-x-2">
                                @if($product->image)
                                    <button onclick="openImageModal('{{ asset('storage/' . $product->image) }}', '{{ $product->name }}')" class="text-blue-600 hover:text-blue-900 text-sm">View Image</button>
                                @endif
                                <a href="{{ route('super_admin.vendors.products.detail', [$vendor->id, $product->id]) }}" class="text-green-600 hover:text-green-900 text-sm">View Details</a>
                                <a href="{{ route('super_admin.vendors.products.edit', [$vendor->id, $product->id]) }}" class="text-yellow-600 hover:text-yellow-900 text-sm">Edit</a>
                                <form action="{{ route('super_admin.vendors.products.destroy', [$vendor->id, $product->id]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No products found for this vendor.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection

<script>
function openImageModal(imageSrc, title) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

function viewProductDetails(productId) {
    // Fetch product details via AJAX
    fetch(`/super-admin/vendors/products/${productId}/details`)
        .then(response => response.json())
        .then(data => {
            let content = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <strong>Name:</strong> ${data.name || 'N/A'}
                        </div>
                        <div>
                            <strong>Price:</strong> Rp ${data.price ? data.price.toLocaleString('id-ID') : 'N/A'}
                        </div>
                        <div>
                            <strong>Category:</strong> ${data.category || 'N/A'}
                        </div>
                        <div>
                            <strong>Status:</strong> ${data.status || 'N/A'}
                        </div>
                        <div>
                            <strong>Max Adults:</strong> ${data.max_adults || 'N/A'}
                        </div>
                        <div>
                            <strong>Max Children:</strong> ${data.max_children || 'N/A'}
                        </div>
                        <div>
                            <strong>Quantity:</strong> ${data.jumlah || 'N/A'}
                        </div>
                    </div>
                    <div>
                        <strong>Description:</strong>
                        <p class="mt-2 text-gray-600">${data.description || 'No description available'}</p>
                    </div>
                </div>
            `;
            document.getElementById('productDetailsContent').innerHTML = content;
            document.getElementById('productDetailsModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching product details:', error);
            alert('Error loading product details');
        });
}

function closeProductDetailsModal() {
    document.getElementById('productDetailsModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const imageModal = document.getElementById('imageModal');
    const productDetailsModal = document.getElementById('productDetailsModal');

    if (event.target == imageModal) {
        closeImageModal();
    }
    if (event.target == productDetailsModal) {
        closeProductDetailsModal();
    }
}
</script>
