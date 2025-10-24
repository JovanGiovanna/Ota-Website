@extends('layouts.vendor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Product</h1>

        <form action="{{ route('vendor.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="id_category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="id_category" id="id_category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('id_category', $product->id_category) == $category->id ? 'selected' : '' }}>{{ $category->categories }}</option>
                    @endforeach
                </select>
                @error('id_category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                @if($product->image)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="Current Image" class="w-32 h-32 object-cover rounded-md">
                    </div>
                @endif
                <input type="file" name="image" id="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-sm text-gray-500 mt-1">Leave empty to keep current image</p>
                @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">Jumlah (Stock Quantity)</label>
                <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', $product->jumlah) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('jumlah')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="max_adults" class="block text-sm font-medium text-gray-700 mb-2">Max Adults</label>
                <input type="number" name="max_adults" id="max_adults" value="{{ old('max_adults', $product->max_adults) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('max_adults')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="max_children" class="block text-sm font-medium text-gray-700 mb-2">Max Children</label>
                <input type="number" name="max_children" id="max_children" value="{{ old('max_children', $product->max_children) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('max_children')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="available" {{ old('status', $product->status) == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ old('status', $product->status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('vendor.products') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Update Product</button>
            </div>
        </form>
    </div>
</div>
@endsection
