@extends('layouts.admin')

@section('title', 'Profil Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Profil Admin</h1>
            <p class="text-gray-600 mt-2">Kelola informasi profil Anda</p>
        </div>

        <!-- Profile Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Profile Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-8">
                <div class="flex items-center space-x-4">
                    <!-- Avatar -->
                    <div class="w-24 h-24 rounded-full bg-white flex items-center justify-center text-blue-600 text-3xl font-bold shadow-lg">
                        {{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 2)) }}
                    </div>
                    
                    <!-- Info -->
                    <div class="text-white">
                        <h2 class="text-2xl font-bold">{{ Auth::guard('admin')->user()->name }}</h2>
                        <p class="text-blue-100 mt-1">{{ Auth::guard('admin')->user()->email }}</p>
                        <span class="inline-block mt-2 px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm">
                            Administrator
                        </span>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Informasi Akun</h3>
                
                <div class="space-y-4">
                    <!-- Name -->
                    <div class="flex items-start border-b pb-4">
                        <div class="w-1/3">
                            <label class="text-sm font-medium text-gray-600">Nama Lengkap</label>
                        </div>
                        <div class="w-2/3">
                            <p class="text-gray-800 font-medium">{{ Auth::guard('admin')->user()->name }}</p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex items-start border-b pb-4">
                        <div class="w-1/3">
                            <label class="text-sm font-medium text-gray-600">Email</label>
                        </div>
                        <div class="w-2/3">
                            <p class="text-gray-800 font-medium">{{ Auth::guard('admin')->user()->email }}</p>
                        </div>
                    </div>

                    <!-- ID -->
                    <div class="flex items-start border-b pb-4">
                        <div class="w-1/3">
                            <label class="text-sm font-medium text-gray-600">ID Admin</label>
                        </div>
                        <div class="w-2/3">
                            <p class="text-gray-800 font-medium">#{{ Auth::guard('admin')->user()->id }}</p>
                        </div>
                    </div>

                    <!-- Created At -->
                    <div class="flex items-start pb-4">
                        <div class="w-1/3">
                            <label class="text-sm font-medium text-gray-600">Terdaftar Sejak</label>
                        </div>
                        <div class="w-2/3">
                            <p class="text-gray-800 font-medium">
                                {{ Auth::guard('admin')->user()->created_at->format('d F Y') }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ Auth::guard('admin')->user()->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 flex space-x-3">
                    <button onclick="window.location.href='{{ route('admin.profile.edit') }}'" 
                            class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                        <i class="fas fa-edit mr-2"></i>Edit Profil
                    </button>
                    
                    <button onclick="window.location.href='#'" 
                            class="px-6 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition duration-200 font-medium">
                        <i class="fas fa-key mr-2"></i>Ubah Password
                    </button>
                </div>
            </div>
        </div>

        <!-- Additional Info Card -->
        <div class="mt-6 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Aktivitas Terakhir</h3>
            
            <div class="space-y-3">
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-clock text-blue-500 mr-3"></i>
                    <span>Terakhir login: <strong class="text-gray-800">{{ now()->format('d F Y, H:i') }}</strong></span>
                </div>
                
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-shield-alt text-green-500 mr-3"></i>
                    <span>Status: <strong class="text-green-600">Aktif</strong></span>
                </div>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        <strong>Keamanan Akun:</strong> Pastikan untuk mengubah password secara berkala dan jangan membagikan informasi login Anda kepada siapapun.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .container {
        min-height: calc(100vh - 64px);
    }
</style>
@endpush