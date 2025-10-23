@extends('layouts.vendor')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold mb-6">Lengkapi Informasi Vendor</h1>

                <form action="{{ route('vendor.info.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- City Selection -->
                        <div>
                            <label for="id_city" class="block text-sm font-medium text-gray-700">Kota</label>
                            <select id="id_city" name="id_city" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Pilih Kota</option>
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

                        <!-- Corporate Name -->
                        <div>
                            <label for="name_corporate" class="block text-sm font-medium text-gray-700">Nama Perusahaan</label>
                            <input type="text" id="name_corporate" name="name_corporate" value="{{ old('name_corporate') }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('name_corporate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="desc" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea id="desc" name="desc" rows="4" required class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('desc') }}</textarea>
                            @error('desc')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Latitude -->
                        <div>
                            <label for="coordinate_latitude" class="block text-sm font-medium text-gray-700">Koordinat Latitude</label>
                            <input type="number" step="any" id="coordinate_latitude" name="coordinate_latitude" value="{{ old('coordinate_latitude') }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="-6.2088">
                            @error('coordinate_latitude')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Longitude -->
                        <div>
                            <label for="coordinate_longitude" class="block text-sm font-medium text-gray-700">Koordinat Longitude</label>
                            <input type="number" step="any" id="coordinate_longitude" name="coordinate_longitude" value="{{ old('coordinate_longitude') }}" required class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="106.8456">
                            @error('coordinate_longitude')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Landmark Description -->
                        <div class="md:col-span-2">
                            <label for="landmark_description" class="block text-sm font-medium text-gray-700">Deskripsi Landmark (Opsional)</label>
                            <textarea id="landmark_description" name="landmark_description" rows="3" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('landmark_description') }}</textarea>
                            @error('landmark_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <a href="{{ route('vendor.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Kembali ke Dashboard
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Simpan Informasi Vendor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
