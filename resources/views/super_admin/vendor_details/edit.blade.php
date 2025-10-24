@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Edit Vendor Detail</h1>
                <a href="{{ route('super_admin.vendor_details') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Back to List
                </a>
            </div>

            <form action="{{ route('super_admin.vendor_details.update', $vendorInfo->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="id_vendor" class="block text-sm font-medium text-gray-700 mb-2">Vendor</label>
                    <select name="id_vendor" id="id_vendor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ $vendorInfo->id_vendor == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->name }} ({{ $vendor->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_vendor')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="id_city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <select name="id_city" id="id_city" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select City</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ $vendorInfo->id_city == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_city')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="name_corporate" class="block text-sm font-medium text-gray-700 mb-2">Corporate Name</label>
                    <input type="text" name="name_corporate" id="name_corporate" value="{{ old('name_corporate', $vendorInfo->name_corporate) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('name_corporate')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="desc" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="desc" id="desc" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>{{ old('desc', $vendorInfo->desc) }}</textarea>
                    @error('desc')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="coordinate_latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                        <input type="number" step="any" name="coordinate_latitude" id="coordinate_latitude" value="{{ old('coordinate_latitude', $vendorInfo->coordinate_latitude) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('coordinate_latitude')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="coordinate_longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                        <input type="number" step="any" name="coordinate_longitude" id="coordinate_longitude" value="{{ old('coordinate_longitude', $vendorInfo->coordinate_longitude) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('coordinate_longitude')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label for="landmark_description" class="block text-sm font-medium text-gray-700 mb-2">Landmark Description</label>
                    <textarea name="landmark_description" id="landmark_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('landmark_description', $vendorInfo->landmark_description) }}</textarea>
                    @error('landmark_description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                        Update Vendor Detail
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
