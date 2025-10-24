@extends('layouts.vendor')

@section('title', 'Complete Vendor Information')

@section('welcome')
Complete Vendor Information
@endsection

@section('logout_route', route('vendor.logout'))

@section('content')
<div class="px-6 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Complete Your Vendor Information</h1>

                <form action="{{ route('vendor.info.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name_corporate" class="block text-sm font-medium text-gray-700">Corporate Name</label>
                                <input type="text" name="name_corporate" id="name_corporate" value="{{ old('name_corporate') }}" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                                @error('name_corporate')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="id_city" class="block text-sm font-medium text-gray-700">City</label>
                                <select name="id_city" id="id_city" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">Select City</option>
                                    @foreach(\App\Models\City::with('province')->get() as $city)
                                        <option value="{{ $city->id }}" {{ old('id_city') == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }} - {{ $city->province->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="desc" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="desc" id="desc" rows="4" required
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">{{ old('desc') }}</textarea>
                        @error('desc')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Location Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="coordinate_latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                                <input type="number" step="any" name="coordinate_latitude" id="coordinate_latitude" value="{{ old('coordinate_latitude') }}" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500" placeholder="-6.2088">
                                @error('coordinate_latitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="coordinate_longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                                <input type="number" step="any" name="coordinate_longitude" id="coordinate_longitude" value="{{ old('coordinate_longitude') }}" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500" placeholder="106.8456">
                                @error('coordinate_longitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Landmark -->
                    <div>
                        <label for="landmark_description" class="block text-sm font-medium text-gray-700">Landmark Description (Optional)</label>
                        <textarea name="landmark_description" id="landmark_description" rows="3"
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-emerald-500 focus:border-emerald-500">{{ old('landmark_description') }}</textarea>
                        @error('landmark_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('vendor.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium">
                            Back to Dashboard
                        </a>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-medium">
                            Save Information
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
