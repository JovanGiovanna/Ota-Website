@extends('layouts.superadmin')

@section('title', 'Addons Management')

@section('welcome')
Addons Management
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center border-b border-gray-200">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Addons List</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage all addons across vendors</p>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Addon</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($addons as $addon)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $addon->addons }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $addon->vendor->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($addon->nta ?? 0, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($addon->status == 'available')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                            @elseif($addon->status == 'unavailable')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Unavailable</span>
                            @elseif($addon->status == 'draft')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($addon->status) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('super_admin.addons.edit', $addon->id) }}" class="text-yellow-600 hover:text-yellow-900 text-sm">Edit</a>
                                <form action="{{ route('super_admin.addons.destroy', $addon->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this addon?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No addons found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <p class="text-sm text-gray-700">Showing <span class="font-medium">{{ $addons->firstItem() }}</span> to <span class="font-medium">{{ $addons->lastItem() }}</span> of <span class="font-medium">{{ $addons->total() }}</span> addons</p>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    {{ $addons->links() }}
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection
