@extends('layouts.superadmin')

@section('title', 'Vendors Management')

@section('welcome')
Vendors Management
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Vendors</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage all vendors in the system</p>
            </div>
            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Vendor
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            <!-- Sample vendor cards -->
            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold">JS</span>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-lg font-semibold text-gray-900">John Studio</h4>
                            <p class="text-sm text-gray-600">Photography</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        <button class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button class="text-red-600 hover:text-red-900 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Email:</span>
                        <span class="text-sm font-semibold text-gray-900">john@example.com</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Phone:</span>
                        <span class="text-sm font-semibold text-gray-900">+62 812-3456-7890</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Location:</span>
                        <span class="text-sm font-semibold text-gray-900">Jakarta</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold">MV</span>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-lg font-semibold text-gray-900">Maya Videos</h4>
                            <p class="text-sm text-gray-600">Videography</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        <button class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button class="text-red-600 hover:text-red-900 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Email:</span>
                        <span class="text-sm font-semibold text-gray-900">maya@example.com</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Phone:</span>
                        <span class="text-sm font-semibold text-gray-900">+62 811-2345-6789</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Location:</span>
                        <span class="text-sm font-semibold text-gray-900">Bandung</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold">WS</span>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-lg font-semibold text-gray-900">Wedding Studio</h4>
                            <p class="text-sm text-gray-600">Photography</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        <button class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button class="text-red-600 hover:text-red-900 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Email:</span>
                        <span class="text-sm font-semibold text-gray-900">wedding@example.com</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Phone:</span>
                        <span class="text-sm font-semibold text-gray-900">+62 813-4567-8901</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Location:</span>
                        <span class="text-sm font-semibold text-gray-900">Surabaya</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
