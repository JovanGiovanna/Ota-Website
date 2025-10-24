@extends('layouts.superadmin')

@section('title', 'Add New Category')

@section('welcome')
Add New Category
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="max-w-md mx-auto bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Create New Category</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Add a new service category to the system</p>
        </div>

        <form method="POST" action="{{ route('super_admin.categories.store') }}" class="px-4 py-5 sm:px-6 space-y-4">
            @csrf

            <div>
                <label for="id_type" class="block text-sm font-medium text-gray-700">Type</label>
                <select name="id_type" id="id_type" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('id_type') border-red-500 @enderror">
                    <option value="">Select Type</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" {{ old('id_type') == $type->id ? 'selected' : '' }}>
                            {{ $type->type }}
                        </option>
                    @endforeach
                </select>
                @error('id_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="categories" class="block text-sm font-medium text-gray-700">Category Name</label>
                <input type="text" name="categories" id="categories" value="{{ old('categories') }}" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('categories') border-red-500 @enderror">
                @error('categories')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('super_admin.types_categories') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
