@extends('layouts.superadmin')

@section('title', 'Vendor Profile - ' . $vendor->name)

@section('welcome')
Vendor Profile Management
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Profile for {{ $vendor->name }}</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">View vendor profile information</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('super_admin.vendors') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Vendors
                </a>
                <a href="{{ route('super_admin.vendors.edit', $vendor->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Vendor
                </a>
            </div>
        </div>

        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h4 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h4>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Vendor Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $vendor->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $vendor->email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Corporate Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $vendor->vendorInfo->name_corporate ?? 'Not provided' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $vendor->vendorInfo->phone ?? 'Not provided' }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $vendor->vendorInfo->address ?? 'Not provided' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">City</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $vendor->vendorInfo->city->name ?? 'Not specified' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Province</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $vendor->vendorInfo->city->province->name ?? 'Not specified' }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $vendor->vendorInfo->description ?? 'No description available' }}</p>
                </div>

                <!-- Location Information -->
                <div class="md:col-span-2">
                    <h4 class="text-lg font-medium text-gray-900 mb-4 mt-6">Location Information</h4>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Coordinates</label>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($vendor->vendorInfo->coordinate_latitude && $vendor->vendorInfo->coordinate_longitude)
                            {{ $vendor->vendorInfo->coordinate_latitude }}, {{ $vendor->vendorInfo->coordinate_longitude }}
                        @else
                            Not provided
                        @endif
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Landmark Description</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $vendor->vendorInfo->landmark_description ?? 'Not provided' }}</p>
                </div>

                <!-- Status Information -->
                <div class="md:col-span-2">
                    <h4 class="text-lg font-medium text-gray-900 mb-4 mt-6">Status Information</h4>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Account Status</label>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Verification Status</label>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $vendor->vendorInfo->is_verified ?? false ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $vendor->vendorInfo->is_verified ?? false ? 'Verified' : 'Pending Verification' }}
                    </span>
                </div>

                <!-- Account Information -->
                <div class="md:col-span-2">
                    <h4 class="text-lg font-medium text-gray-900 mb-4 mt-6">Account Information</h4>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Joined Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $vendor->created_at->format('M d, Y') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $vendor->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
