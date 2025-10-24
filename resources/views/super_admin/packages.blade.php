@extends('layouts.superadmin')

@section('title', 'Packages Management')

@section('welcome')
Packages Management
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Packages</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage service packages</p>
            </div>
            <a href="{{ route('super_admin.packages.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Package
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            @forelse($packages as $package)
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-6 border border-blue-200 h-64 flex flex-col overflow-hidden">
                    <div class="flex items-start justify-between mb-4 flex-shrink-0">
                        <div class="flex items-start flex-1 min-w-0 pr-2">
                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1 min-w-0 overflow-hidden">
                                <h4 class="text-lg font-semibold text-gray-900 truncate">{{ $package->name_package }}</h4>
                                <p class="text-sm text-gray-600 overflow-hidden" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;" title="{{ $package->description ?? 'No description' }}">{{ Str::limit($package->description ?? 'No description', 17) }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-2 flex-shrink-0">
                            <a href="{{ route('super_admin.packages.edit', $package) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('super_admin.packages.destroy', $package) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium" onclick="return confirm('Are you sure you want to delete this package?')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="space-y-2 flex-1 overflow-hidden">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Price:</span>
                            <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($package->price_publish, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Start Publish:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $package->start_publish ? \Carbon\Carbon::parse($package->start_publish)->format('d M Y') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">End Publish:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $package->end_publish ? \Carbon\Carbon::parse($package->end_publish)->format('d M Y') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Active:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $package->is_active ? 'Yes' : 'No' }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500">No packages found.</p>
                </div>
            @endforelse
        </div>
        <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
            {{ $packages->links() }}
        </div>
    </div>
</div>
@endsection