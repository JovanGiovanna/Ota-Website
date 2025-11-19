@extends('layouts.admin')

@section('title', 'My Packages')

@section('welcome')
Welcome, {{ Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : 'admin' }}!
@endsection

@section('logout_route', route('admin.logout'))

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-extrabold text-indigo-900 flex items-center space-x-3">
            <i class="fas fa-cube text-indigo-500"></i>
            <span>My Packages</span>
        </h1>
        <a href="{{ route('admin.packages.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-emerald-600 hover:bg-emerald-700 transition duration-150 ease-in-out shadow-md">
            <i class="fas fa-plus mr-2"></i>
            Add New Package
        </a>
    </div>

    {{-- Alert untuk Success/Error Message --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    {{-- End Alert --}}

    <div class="bg-white rounded-xl shadow-lg border border-indigo-100 overflow-hidden">
        <div class="p-6">
            @if(isset($packages) && count($packages) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-indigo-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-600 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-600 uppercase tracking-wider">Description</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-600 uppercase tracking-wider">Slug</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-indigo-600 uppercase tracking-wider">Discount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-600 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-indigo-600 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative px-6 py-3 text-right text-xs font-semibold text-indigo-600 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($packages as $package)
                            <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $package->name_package ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ Str::limit($package->description ?? 'N/A', 50) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $package->slug ?? 'N/A' }}</td>
                                {{-- DATA BARU: DISCOUNT PERCENTAGE --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold @if(($package->discount_percentage ?? 0) > 0) text-red-600 @else text-gray-500 @endif">
                                    {{ $package->discount_percentage ?? 0 }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-emerald-600">Rp {{ number_format($package->price_publish ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{-- PERBAIKAN LOGIKA STATUS BERDASARKAN is_active (boolean) --}}
                                    @if($package->is_active == 1)
                                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 border border-green-300">Active</span>
                                    @else
                                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 border border-yellow-300">Draft / Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                    {{-- PERBAIKAN ROUTE EDIT --}}
                                    {{-- Menggunakan route('admin.packages.edit', $package) untuk mendapatkan ID/slug --}}
                                    <a href="{{ route('admin.packages.edit', $package) }}" class="text-blue-600 hover:text-blue-800 transition duration-150 ease-in-out" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    {{-- PERBAIKAN ROUTE DELETE --}}
                                    {{-- Menggunakan route('admin.packages.destroy', $package) untuk mendapatkan ID/slug --}}
                                    <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this package? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 transition duration-150 ease-in-out" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-16 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <i class="fas fa-cube mx-auto h-12 w-12 text-indigo-400"></i>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900">No Packages Found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new package now.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.packages.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700">
                            <i class="fas fa-plus mr-2"></i>
                            Add Package
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection