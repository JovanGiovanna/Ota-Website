@extends('layouts.vendor')

@section('title', 'My Profile')

@section('welcome')
Welcome, {{ Auth::guard('vendor')->check() ? Auth::guard('vendor')->user()->name : 'Vendor' }}!
@endsection

@section('logout_route', route('vendor.logout'))

@section('content')
<div class="px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
        <a href="{{ route('vendor.info.edit') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium">
            Edit Profile
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Vendor Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ Auth::guard('vendor')->user()->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="mt-1 text-sm text-gray-900">{{ Auth::guard('vendor')->user()->email }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Corporate Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ Auth::guard('vendor')->user()->vendorInfo->name_corporate ?? 'Not provided' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <p class="mt-1 text-sm text-gray-900">{{ Auth::guard('vendor')->user()->vendorInfo->phone ?? 'Not provided' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">City</label>
                    <p class="mt-1 text-sm text-gray-900">{{ Auth::guard('vendor')->user()->vendorInfo->city->name ?? 'Not specified' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Province</label>
                    <p class="mt-1 text-sm text-gray-900">{{ Auth::guard('vendor')->user()->vendorInfo->city->province->name ?? 'Not specified' }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <p class="mt-1 text-sm text-gray-900">{{ Auth::guard('vendor')->user()->vendorInfo->desc ?? 'No description available' }}</p>
                </div>

                <!-- Location Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 mt-6">Location Information</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Coordinates</label>
                    <p class="mt-1 text-sm text-gray-900">
                        @if(Auth::guard('vendor')->user()->vendorInfo->coordinate_latitude && Auth::guard('vendor')->user()->vendorInfo->coordinate_longitude)
                            {{ Auth::guard('vendor')->user()->vendorInfo->coordinate_latitude }}, {{ Auth::guard('vendor')->user()->vendorInfo->coordinate_longitude }}
                        @else
                            Not provided
                        @endif
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Landmark Description</label>
                    <p class="mt-1 text-sm text-gray-900">{{ Auth::guard('vendor')->user()->vendorInfo->landmark_description ?? 'Not provided' }}</p>
                </div>

                <!-- Status Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 mt-6">Status Information</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Account Status</label>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ Auth::guard('vendor')->user()->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ Auth::guard('vendor')->user()->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Verification Status</label>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ Auth::guard('vendor')->user()->vendorInfo->is_verified ?? false ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ Auth::guard('vendor')->user()->vendorInfo->is_verified ?? false ? 'Verified' : 'Pending Verification' }}
                    </span>
                </div>

                <!-- Account Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 mt-6">Account Information</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Joined Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ Auth::guard('vendor')->user()->created_at->format('M d, Y') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                    <p class="mt-1 text-sm text-gray-900">{{ Auth::guard('vendor')->user()->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
