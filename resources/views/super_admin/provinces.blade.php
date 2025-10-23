@extends('layouts.superadmin')

@section('title', 'Provinces Management')

@section('welcome')
Provinces Management
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Provinces</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage all provinces in the system</p>
            </div>
            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Province
            </button>
        </div>
        <ul class="divide-y divide-gray-200">
            @forelse($provinces as $province)
            <li>
                <div class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                    <span class="text-white font-medium">{{ substr($province->name, 0, 2) }}</span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $province->name }}</div>
                                <div class="text-sm text-gray-500">Province</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="text-blue-600 hover:text-blue-900 text-sm font-medium">Edit</button>
                            <button class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                        </div>
                    </div>
                </div>
            </li>
            @empty
            <li>
                <div class="px-4 py-4 sm:px-6 text-center text-gray-500">
                    No provinces found.
                </div>
            </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
