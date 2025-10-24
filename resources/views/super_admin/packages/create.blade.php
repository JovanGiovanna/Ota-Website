@extends('layouts.superadmin')

@section('title', 'Add New Package')

@section('welcome')
Add New Package
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="max-w-2xl mx-auto bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Create New Package</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Add a new service package to the system</p>
        </div>

        <form method="POST" action="{{ route('super_admin.packages.store') }}" enctype="multipart/form-data" class="px-4 py-5 sm:px-6 space-y-6">
            @csrf

            <div>
                <label for="name_package" class="block text-sm font-medium text-gray-700">Package Name</label>
                <input type="text" name="name_package" id="name_package" value="{{ old('name_package') }}" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name_package') border-red-500 @enderror">
                @error('name_package')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">Package Image</label>
                <input type="file" name="image" id="image" accept="image/*"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('image') border-red-500 @enderror">
                <p class="mt-1 text-sm text-gray-500">Upload a package image (JPEG, PNG, JPG, GIF, max 2MB)</p>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="price_publish" class="block text-sm font-medium text-gray-700">Price (Rp)</label>
                    <input type="number" name="price_publish" id="price_publish" value="{{ old('price_publish') }}" step="0.01" min="0" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('price_publish') border-red-500 @enderror">
                    @error('price_publish')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_publish" class="block text-sm font-medium text-gray-700">Start Publish Date</label>
                    <input type="datetime-local" name="start_publish" id="start_publish" value="{{ old('start_publish') }}" required
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('start_publish') border-red-500 @enderror">
                    @error('start_publish')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="end_publish" class="block text-sm font-medium text-gray-700">End Publish Date (Optional)</label>
                    <input type="datetime-local" name="end_publish" id="end_publish" value="{{ old('end_publish') }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('end_publish') border-red-500 @enderror">
                    @error('end_publish')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="flex items-center mt-6">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Active Package</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('super_admin.packages') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create Package
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
