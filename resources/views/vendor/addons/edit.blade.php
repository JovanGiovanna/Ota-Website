@extends('layouts.vendor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Addon</h1>

        <form action="{{ route('vendor.addons.update', $addon->id) }}" method="POST" class="bg-white shadow-md rounded-lg p-6">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="addons" class="block text-sm font-medium text-gray-700 mb-2">Addon Name</label>
                <input type="text" name="addons" id="addons" value="{{ old('addons', $addon->addons) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('addons')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                <input type="number" name="price" id="price" value="{{ old('price', $addon->price) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="desc" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="desc" id="desc" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('desc', $addon->desc) }}</textarea>
                @error('desc')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="available" {{ old('status', $addon->status) == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ old('status', $addon->status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    <option value="draft" {{ old('status', $addon->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="publish" class="flex items-center">
                    <input type="checkbox" name="publish" id="publish" value="1" {{ old('publish', $addon->publish) ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Publish</span>
                </label>
                @error('publish')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="image_url" class="block text-sm font-medium text-gray-700 mb-2">Image URL</label>
                <input type="url" name="image_url" id="image_url" value="{{ old('image_url', $addon->image_url) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('image_url')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('vendor.addons') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Update Addon</button>
            </div>
        </form>
    </div>
</div>
@endsection
