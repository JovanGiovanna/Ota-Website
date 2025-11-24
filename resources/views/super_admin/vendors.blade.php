@extends('layouts.superadmin')

@section('title', 'Vendors Management')

@section('welcome')
Vendors Management
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center border-b border-gray-200">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Vendors List</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage all vendors in the system</p>
            </div>
            <a href="{{ route('super_admin.vendors.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Vendor
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Vendor Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                            Phone
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                            Location
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($vendors as $vendor)
                    <tr>
                        {{-- Vendor Name & Description --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-semibold text-xs">{{ strtoupper(substr($vendor->name, 0, 2)) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $vendor->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $vendor->vendorInfo->description ?? 'Vendor' }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Email --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $vendor->email }}
                        </td>

                        {{-- Phone --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                            {{ $vendor->vendorInfo->phone ?? 'N/A' }}
                        </td>

                        {{-- Location --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                            {{ $vendor->vendorInfo->address ?? 'N/A' }}
                        </td>
                        
                        {{-- Status --}}
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        {{-- Action Buttons --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-1 justify-center items-center">
                                
                                {{-- Activate/Deactivate Toggle --}}
                                <form method="POST" action="{{ route('super_admin.vendors.update', $vendor) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="name" value="{{ $vendor->name }}">
                                    <input type="hidden" name="email" value="{{ $vendor->email }}">
                                    <input type="hidden" name="is_active" value="{{ $vendor->is_active ? 0 : 1 }}">
                                    <button type="submit" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md {{ $vendor->is_active ? 'text-red-700 bg-red-50 hover:bg-red-100' : 'text-green-700 bg-green-50 hover:bg-green-100' }} focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $vendor->is_active ? 'focus:ring-red-500' : 'focus:ring-green-500' }}">
                                        {{ $vendor->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>

                                {{-- Profile --}}
                                <a href="{{ route('super_admin.vendors.profile', $vendor) }}" class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md text-purple-700 bg-purple-50 hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    Profile
                                </a>

                                {{-- Products/Addons Links (Collapsed into dropdown or separate actions if space allows) --}}
                                <a href="{{ route('super_admin.vendors.products', $vendor) }}" title="View Products" class="text-blue-600 hover:text-blue-900 px-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                </a>
                                <a href="{{ route('super_admin.vendors.addons', $vendor) }}" title="View AddOns" class="text-green-600 hover:text-green-900 px-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                                </a>
                                
                                {{-- Transaction Links --}}
                                <a href="{{ route('super_admin.vendors.transaction_products', $vendor) }}" title="Product Transactions" class="text-orange-600 hover:text-orange-900 px-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                </a>
                                <a href="{{ route('super_admin.vendors.transaction_addons', $vendor) }}" title="AddOn Transactions" class="text-indigo-600 hover:text-indigo-900 px-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                </a>

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                            No vendors found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection