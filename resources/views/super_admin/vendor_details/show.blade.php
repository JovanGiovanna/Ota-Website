@extends('layouts.superadmin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Vendor Detail Information</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('super_admin.vendor_details.edit', $vendorInfo->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Edit
                    </a>
                    <a href="{{ route('super_admin.vendor_details') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Back to List
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Vendor Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Vendor Information</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Vendor Name</label>
                            <p class="text-sm text-gray-900">{{ $vendorInfo->vendor->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Email</label>
                            <p class="text-sm text-gray-900">{{ $vendorInfo->vendor->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $vendorInfo->vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $vendorInfo->vendor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Business Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Business Information</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Corporate Name</label>
                            <p class="text-sm text-gray-900">{{ $vendorInfo->name_corporate }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Description</label>
                            <p class="text-sm text-gray-900">{{ $vendorInfo->desc }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">City</label>
                            <p class="text-sm text-gray-900">{{ $vendorInfo->city->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Location Information</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Coordinates</label>
                            <p class="text-sm text-gray-900">
                                Latitude: {{ $vendorInfo->coordinate_latitude }}<br>
                                Longitude: {{ $vendorInfo->coordinate_longitude }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Landmark Description</label>
                            <p class="text-sm text-gray-900">{{ $vendorInfo->landmark_description ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Timestamps</h2>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Created At</label>
                            <p class="text-sm text-gray-900">{{ $vendorInfo->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Updated At</label>
                            <p class="text-sm text-gray-900">{{ $vendorInfo->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
