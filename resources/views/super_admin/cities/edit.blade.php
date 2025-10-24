@extends('layouts.superadmin')

@section('title', 'Edit City')

@section('welcome')
Edit City
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="max-w-md mx-auto bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Edit City</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Update city information</p>
        </div>

        <form method="POST" action="{{ route('super_admin.cities.update', $city) }}" class="px-4 py-5 sm:px-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="id_province" class="block text-sm font-medium text-gray-700">Province</label>
                <select name="id_province" id="id_province" required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('id_province') border-red-500 @enderror">
                    <option value="">Select Province</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}" {{ old('id_province', $city->id_province) == $province->id ? 'selected' : '' }}>
                            {{ $province->name }}
                        </option>
                    @endforeach
                </select>
                @error('id_province')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">City Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $city->name) }}" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('super_admin.cities') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Update City
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
