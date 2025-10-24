@extends('layouts.vendor')

@section('title', 'My Addons')

@section('welcome')
Welcome, {{ Auth::guard('vendor')->check() ? Auth::guard('vendor')->user()->name : 'Vendor' }}!
@endsection

@section('logout_route', route('vendor.logout'))

@section('content')
<div class="px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Addons</h1>
        <a href="{{ route('vendor.addons.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium">
            Add New Addon
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            @if($addons && count($addons) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($addons as $addon)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $addon->addons ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($addon->price ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('vendor.addons.edit', $addon->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <form method="POST" action="{{ route('vendor.addons.destroy', $addon->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this addon?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No addons</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new addon.</p>
                    <div class="mt-6">
                        <a href="{{ route('vendor.addons.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Addon
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
