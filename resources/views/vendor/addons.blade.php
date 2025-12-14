@extends('layouts.vendor')

@section('title', 'My Add-ons')

@section('welcome')
Welcome, {{ Auth::guard('vendor')->check() ? Auth::guard('vendor')->user()->name : 'Vendor' }}!
@endsection

@section('logout_route', route('vendor.logout'))

@section('content')
<div class="px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Add-ons</h1>
        <a href="{{ route('vendor.addons.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium">
            Add New Add-on
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            @if($addons && count($addons) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Add-on</th>
                                {{-- KOLOM HARGA BARU --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NTA / Basic Price / Tax</th>
                                {{-- END KOLOM HARGA BARU --}}
                                {{-- KOLOM DISKON BARU --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                                {{-- END KOLOM DISKON BARU --}}
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Refund Policy</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($addons as $addon)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($addon->images && is_array($addon->images) && count($addon->images) > 0)
                                        <div class="flex space-x-1">
                                            @foreach(array_slice($addon->images, 0, 2) as $image)
                                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $addon->addons }}" class="w-12 h-12 object-cover rounded-lg">
                                            @endforeach
                                            @if(count($addon->images) > 2)
                                                <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center text-xs font-medium text-gray-600">
                                                    +{{ count($addon->images) - 2 }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $addon->addons ?? 'N/A' }}</td>
                                
                                {{-- KOLOM HARGA BARU --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="font-bold text-gray-900">
                                        NTA: Rp {{ number_format($addon->nta ?? $addon->price ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Basic: Rp {{ number_format($addon->basic_price ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Tax: {{ number_format($addon->tax_rate ?? 0, 2, ',', '.') }}%
                                    </div>
                                </td>
                                {{-- END KOLOM HARGA BARU --}}

                                {{-- KOLOM DISKON DENGAN HANYA MENAMPILKAN discount_fixed --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if(($addon->discount_value ?? 0) > 0)
                                        <span class="text-red-600 font-semibold">
                                            - Rp {{ number_format($addon->discount_value, 0, ',', '.') }}
                                        </span>
                                        <div class="text-xs text-gray-500">
                                            (Fixed Discount)
                                        </div>
                                    @else
                                        <span class="text-gray-500">None</span>
                                    @endif
                                </td>
                                {{-- END KOLOM DISKON --}}

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ Str::limit($addon->refund_policy ?? 'N/A', 50) }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{-- Status Add-on --}}
                                    @if(isset($addon->status) && $addon->status == 'active')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @elseif(isset($addon->status) && $addon->status == 'draft')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('vendor.addons.edit', $addon->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <form method="POST" action="{{ route('vendor.addons.destroy', $addon->id) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this add-on?')">Delete</button>
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No add-ons</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new add-on.</p>
                    <div class="mt-6">
                        <a href="{{ route('vendor.addons.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Add-on
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection