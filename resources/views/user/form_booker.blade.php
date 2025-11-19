@extends('layouts.user')

@section('title', 'Book Your Stay')

@section('welcome')
Book Your Package with Products and Add-ons!
@endsection

@section('content')
<!-- Booking Hero -->
<div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-800 rounded-2xl p-8 mb-8 text-white shadow-2xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2">Book Your Package</h1>
            <p class="text-blue-100">Select your package and add-ons for a perfect experience</p>
        </div>
        <div class="hidden md:block">
            <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center">
                <i class="fas fa-calendar-check text-3xl text-white"></i>
            </div>
        </div>
    </div>
</div>

<!-- Booking Form -->
<div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
    <form method="POST" action="{{ route('user.book') }}" id="booking-form" class="space-y-8">
        @csrf

        @php
            $preselectedPackage = request('package');
            $preselectedPackageData = null;
            if ($preselectedPackage) {
                $preselectedPackageData = \App\Models\Package::find($preselectedPackage);
            }

            $preselectedProduct = request('product');
            $preselectedProductData = null;
            if ($preselectedProduct) {
                $preselectedProductData = \App\Models\Product::find($preselectedProduct);
            }

            $preselectedAddon = request('addon');
            $preselectedAddonData = null;
            if ($preselectedAddon) {
                $preselectedAddonData = \App\Models\Addon::find($preselectedAddon);
            }
        @endphp

        <!-- Pre-selected Package Info -->
        @if($preselectedPackageData)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-6">
            <h3 class="text-lg font-bold text-blue-800 mb-4 flex items-center">
                <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                Selected Package
            </h3>
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center">
                    @php
                        $validImages = array_filter((array) ($preselectedPackageData->images ?? []), function($img) {
                            return is_string($img) && !empty($img);
                        });
                    @endphp
                    @if($validImages && count($validImages) > 0)
                        <img src="{{ asset('storage/' . reset($validImages)) }}" alt="{{ $preselectedPackageData->name_package }}" class="w-16 h-16 rounded-lg object-cover">
                    @else
                        <i class="fas fa-box text-blue-600 text-2xl"></i>
                    @endif
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">{{ $preselectedPackageData->name_package }}</h4>
                    <p class="text-gray-600 text-sm">{{ Str::limit($preselectedPackageData->description, 100) }}</p>
                    <div class="text-lg font-bold text-blue-600">Rp {{ number_format($preselectedPackageData->price_publish, 0, ',', '.') }}/night</div>
                </div>
            </div>
            <input type="hidden" name="id_package[]" value="{{ $preselectedPackageData->id }}">
            <input type="hidden" name="booking_types[]" value="package">
        </div>
        @endif

        <!-- Pre-selected Product Info -->
        @if($preselectedProductData)
        <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-6">
            <h3 class="text-lg font-bold text-green-800 mb-4 flex items-center">
                <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center mr-2">
                    <i class="fas fa-info-circle text-green-600"></i>
                </div>
                Selected Product
            </h3>
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-green-100 rounded-lg flex items-center justify-center">
                    @if($preselectedProductData->image)
                        <img src="{{ asset('storage/' . $preselectedProductData->image) }}" alt="{{ $preselectedProductData->name }}" class="w-16 h-16 rounded-lg object-cover">
                    @else
                        <i class="fas fa-shopping-cart text-green-600 text-2xl"></i>
                    @endif
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">{{ $preselectedProductData->name }}</h4>
                    <p class="text-gray-600 text-sm">{{ Str::limit($preselectedProductData->description, 100) }}</p>
                    <div class="text-lg font-bold text-green-600">Rp {{ number_format($preselectedProductData->price, 0, ',', '.') }}/unit</div>
                </div>
            </div>
            <input type="hidden" name="product_id[]" value="{{ $preselectedProductData->id }}">
            <input type="hidden" name="booking_types[]" value="product">
        </div>
        @endif

        <!-- Pre-selected Addon Info -->
        @if($preselectedAddonData)
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-6 mb-6">
            <h3 class="text-lg font-bold text-orange-800 mb-4 flex items-center">
                <div class="w-6 h-6 bg-orange-100 rounded-lg flex items-center justify-center mr-2">
                    <i class="fas fa-info-circle text-orange-600"></i>
                </div>
                Selected Addon
            </h3>
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-orange-100 rounded-lg flex items-center justify-center">
                    @if($preselectedAddonData->image)
                        <img src="{{ asset('storage/' . $preselectedAddonData->image) }}" alt="{{ $preselectedAddonData->name_addon }}" class="w-16 h-16 rounded-lg object-cover">
                    @else
                        <i class="fas fa-plus-circle text-orange-600 text-2xl"></i>
                    @endif
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">{{ $preselectedAddonData->name_addon }}</h4>
                    <p class="text-gray-600 text-sm">{{ Str::limit($preselectedAddonData->description, 100) }}</p>
                    <div class="text-lg font-bold text-orange-600">Rp {{ number_format($preselectedAddonData->price, 0, ',', '.') }}/unit</div>
                </div>
            </div>
            <input type="hidden" name="addon_id[]" value="{{ $preselectedAddonData->id }}">
            <input type="hidden" name="quantity[{{ $preselectedAddonData->id }}]" value="1">
            <input type="hidden" name="booking_types[]" value="addon">
        </div>
        @endif

        <!-- Booking Type Selection -->
        @if(!$preselectedPackage && !$preselectedProduct && !$preselectedAddon)
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-list text-purple-600"></i>
                </div>
                Select What You Want to Book
            </h3>
            <p class="text-gray-600 mb-6">You can select any combination of packages, products, and add-ons</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $types = ['package'=>'blue','product'=>'green','addon'=>'orange'];
                @endphp
                @foreach($types as $type => $color)
                <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-{{ $color }}-500 transition-all duration-200 cursor-pointer booking-type-option" data-type="{{ $type }}">
                    <div class="text-center mb-4">
                        <div class="w-12 h-12 bg-{{ $color }}-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-{{ $type=='package'?'box':($type=='product'?'shopping-cart':'plus-circle') }} text-{{ $color }}-600"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">{{ ucfirst($type) }}s</h4>
                        <p class="text-gray-600 text-sm">{{ $type=='package'?'Complete packages with accommodations':($type=='product'?'Individual products/services':'Additional services & extras') }}</p>
                    </div>
                    <input type="checkbox" name="booking_types[]" value="{{ $type }}" class="hidden booking-type-checkbox">
                </div>
                @endforeach
            </div>
        </div>

        <!-- Package Selection -->
        <div id="package-selection" class="{{ $preselectedPackage || $preselectedProduct || $preselectedAddon ? 'hidden' : 'hidden' }}">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-hotel text-blue-600"></i>
                </div>
                Select Your Package
            </h3>
            <!-- Search Bar -->
            <div class="mb-4">
                <input type="text" id="package-search" placeholder="Search packages..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-xl p-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    @forelse($packages ?? [] as $package)
                        <div class="relative border-2 border-gray-200 rounded-lg p-3 hover:border-blue-500 transition-all duration-200 cursor-pointer package-option bg-white hover:shadow-md" data-package="{{ $package->id }}" data-price="{{ $package->price_publish }}" data-name="{{ $package->name_package }}" data-desc="{{ $package->description }}">
                            <div class="absolute top-2 right-2 z-10">
                                <input type="checkbox" name="id_package[]" value="{{ $package->id }}" class="package-checkbox w-3 h-3 text-blue-600 bg-white border-2 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer">
                            </div>
                            <div class="flex items-start space-x-2 mb-2">
                                <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center flex-shrink-0">
                                    @php
                                        $validImages = array_filter((array) ($package->images ?? []), function($img) {
                                            return is_string($img) && !empty($img);
                                        });
                                    @endphp
                                    @if($validImages && count($validImages) > 0)
                                        <img src="{{ asset('storage/' . reset($validImages)) }}" alt="{{ $package->name_package }}" class="w-8 h-8 rounded object-cover">
                                    @else
                                        <i class="fas fa-bed text-blue-600 text-sm"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-800 text-xs leading-tight">{{ $package->name_package }}</h4>
                                    <p class="text-gray-600 text-xs mt-1 line-clamp-2">{{ Str::limit($package->description, 40) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-100 pt-2">
                                <div>
                                    <div class="text-sm font-bold text-blue-600">Rp {{ number_format($package->price_publish, 0, ',', '.') }}</div>
                                    <span class="text-gray-500 text-xs">per package</span>
                                </div>
                                    <a href="{{ route('user.package_detail', $package->id) }}?from=form_booker" class="bg-blue-100 text-blue-700 px-2 py-1 rounded font-semibold hover:bg-blue-200 transition-all duration-200 text-xs">
                                        View
                                    </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-6">
                            <i class="fas fa-box text-gray-300 text-3xl mb-3"></i>
                            <p class="text-gray-500 text-sm">No packages available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Product Selection -->
        <div id="product-selection" class="{{ $preselectedPackage || $preselectedProduct || $preselectedAddon ? 'hidden' : 'hidden' }}">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-shopping-cart text-green-600"></i>
                </div>
                Select Your Product
            </h3>
            <!-- Search Bar -->
            <div class="mb-4">
                <input type="text" id="product-search" placeholder="Search products..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-xl p-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    @forelse($products ?? [] as $product)
                        <div class="relative border-2 border-gray-200 rounded-lg p-3 hover:border-green-500 transition-all duration-200 cursor-pointer product-option bg-white hover:shadow-md" data-product="{{ $product->id }}" data-price="{{ $product->price }}" data-name="{{ $product->name }}" data-desc="{{ $product->description }}">
                            <div class="absolute top-2 right-2 z-10">
                                <input type="checkbox" name="product_id[]" value="{{ $product->id }}" class="product-checkbox w-3 h-3 text-green-600 bg-white border-2 border-gray-300 rounded focus:ring-green-500 focus:ring-2 cursor-pointer">
                            </div>
                            <div class="flex items-start space-x-2 mb-2">
                                <div class="w-8 h-8 bg-green-100 rounded flex items-center justify-center flex-shrink-0">
                                    @php
                                        $validImages = array_filter((array) ($product->images ?? []), function($img) {
                                            return is_string($img) && !empty($img);
                                        });
                                    @endphp
                                    @if($validImages && count($validImages) > 0)
                                        <img src="{{ asset('storage/' . reset($validImages)) }}" alt="{{ $product->name }}" class="w-8 h-8 rounded object-cover">
                                    @else
                                        <i class="fas fa-shopping-cart text-green-600 text-sm"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-800 text-xs leading-tight">{{ $product->name }}</h4>
                                    <p class="text-gray-600 text-xs mt-1 line-clamp-2">{{ Str::limit($product->description, 40) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-100 pt-2">
                                <div>
                                    <div class="text-sm font-bold text-green-600">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                                    <span class="text-gray-500 text-xs">per unit</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <input type="number" name="quantity[{{ $product->id }}]" value="1" min="1" class="w-12 px-1 py-1 border border-gray-300 rounded text-center text-xs focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <a href="{{ route('user.product_detail', $product->id) }}?from=form_booker" class="bg-green-100 text-green-700 px-2 py-1 rounded font-semibold hover:bg-green-200 transition-all duration-200 text-xs">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-6">
                            <i class="fas fa-shopping-cart text-gray-300 text-3xl mb-3"></i>
                            <p class="text-gray-500 text-sm">No products available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endif

        <!-- Addon Selection -->
        <div id="addon-selection" class="{{ $preselectedPackage || $preselectedProduct || $preselectedAddon ? '' : 'hidden' }}">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-plus-circle text-orange-600"></i>
                </div>
                Select Your Addon
            </h3>
            <!-- Search Bar -->
            <div class="mb-4">
                <input type="text" id="addon-search" placeholder="Search addons..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>
            <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-xl p-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    @forelse($addons ?? [] as $addon)
                        <div class="relative border-2 border-gray-200 rounded-lg p-3 hover:border-orange-500 transition-all duration-200 cursor-pointer addon-option bg-white hover:shadow-md" data-addon="{{ $addon->id }}" data-price="{{ $addon->price }}" data-name="{{ $addon->addons }}" data-desc="{{ $addon->desc }}">
                            <div class="absolute top-2 right-2 z-10">
                                <input type="checkbox" name="addon_id[]" value="{{ $addon->id }}" class="addon-checkbox w-3 h-3 text-orange-600 bg-white border-2 border-gray-300 rounded focus:ring-orange-500 focus:ring-2 cursor-pointer">
                            </div>
                            <div class="flex items-start space-x-2 mb-2">
                                <div class="w-8 h-8 bg-orange-100 rounded flex items-center justify-center flex-shrink-0">
                @php
                                        $validImages = array_filter((array) ($addon->images ?? []), function($img) {
                                            return is_string($img) && !empty($img);
                                        });
                @endphp
                                    @if($validImages && count($validImages) > 0)
                                        <img src="{{ asset('storage/' . reset($validImages)) }}" alt="{{ $addon->name }}" class="w-8 h-8 rounded object-cover">
                                    @else
                                        <i class="fas fa-plus-circle text-orange-600 text-sm"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-800 text-xs leading-tight">{{ $addon->addons }}</h4>
                                    <p class="text-gray-600 text-xs mt-1 line-clamp-2">{{ Str::limit($addon->desc, 40) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-100 pt-2">
                                <div>
                                    <div class="text-sm font-bold text-orange-600">Rp {{ number_format($addon->price, 0, ',', '.') }}</div>
                                    <span class="text-gray-500 text-xs">per unit</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <input type="number" name="quantity[{{ $addon->id }}]" value="1" min="1" class="w-12 px-1 py-1 border border-gray-300 rounded text-center text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                    <a href="{{ route('user.addon_detail', $addon->id) }}?from=form_booker" class="bg-orange-100 text-orange-700 px-2 py-1 rounded font-semibold hover:bg-orange-200 transition-all duration-200 text-xs">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-6">
                            <i class="fas fa-plus-circle text-gray-300 text-3xl mb-3"></i>
                            <p class="text-gray-500 text-sm">No addons available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-user text-blue-600"></i>
                </div>
                Personal Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" name="booker_name" value="{{ old('booker_name', auth()->user()->name ?? '') }}" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="booker_email" value="{{ old('booker_email', auth()->user()->email ?? '') }}" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <!-- Phone -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input type="tel" name="booker_telp" value="{{ old('booker_telp', auth()->user()->phone ?? '') }}" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="+62">
                    </div>
                </div>
                <!-- Guests -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Guests</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-users text-gray-400"></i>
                        </div>
                        <input type="number" name="amount" min="1" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="1">
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-calendar-alt text-green-600"></i>
                </div>
                Booking Details
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Check-in -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-in Date</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-check text-gray-400"></i>
                        </div>
                        <input type="date" name="checkin_appointment_start" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
                <!-- Check-out -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-out Date</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-times text-gray-400"></i>
                        </div>
                        <input type="date" name="checkout_appointment_end" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
                <!-- Duration -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Duration (Days)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-clock text-gray-400"></i>
                        </div>
                        <input type="number" name="duration_days" min="1" required value="1" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="1">
                    </div>
                </div>
            </div>
        </div>

        <!-- Special Requests -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-sticky-note text-indigo-600"></i>
                </div>
                Special Requests
            </h3>
            <div class="relative">
                <textarea name="requests" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none" placeholder="Any special requests, dietary restrictions, or additional notes..."></textarea>
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-receipt text-blue-600"></i>
                </div>
                Booking Summary
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Packages:</span>
                    <span class="font-semibold text-gray-800" id="summary-package">Not selected</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Products:</span>
                    <span class="font-semibold text-gray-800" id="summary-product">None</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Add-ons:</span>
                    <span class="font-semibold text-gray-800" id="summary-addons">None</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Duration:</span>
                    <span class="font-semibold text-gray-800" id="summary-duration">1 night</span>
                </div>
                <hr class="border-gray-300">
                <div class="flex justify-between items-center text-lg">
                    <span class="font-bold text-gray-800">Total:</span>
                    <span class="font-bold text-blue-600 text-xl" id="summary-total">Rp 0</span>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-12 py-4 rounded-xl font-bold text-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg transform hover:scale-105">
                <i class="fas fa-calendar-check mr-2"></i>
                Confirm Booking
            </button>
            <p class="text-gray-500 text-sm mt-3">You can cancel or modify your booking up to 24 hours before check-in</p>
        </div>
    </form>
</div>

<script>
// Search functionality using event delegation
document.addEventListener('input', function(e) {
    if (e.target.id === 'package-search') {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.package-option').forEach(option => {
            if (searchTerm !== '' && option.style.display === 'none') return; // skip items hidden by pagination only when searching
            const title = option.dataset.name.toLowerCase();
            const desc = option.dataset.desc.toLowerCase();
            if (searchTerm === '' || title.includes(searchTerm) || desc.includes(searchTerm)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    } else if (e.target.id === 'product-search') {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.product-option').forEach(option => {
            if (searchTerm !== '' && option.style.display === 'none') return; // skip items hidden by pagination only when searching
            const title = option.dataset.name.toLowerCase();
            const desc = option.dataset.desc.toLowerCase();
            if (searchTerm === '' || title.includes(searchTerm) || desc.includes(searchTerm)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    } else if (e.target.id === 'addon-search') {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.addon-option').forEach(option => {
            if (searchTerm !== '' && option.style.display === 'none') return; // skip items hidden by pagination only when searching
            const title = option.dataset.name.toLowerCase();
            const desc = option.dataset.desc.toLowerCase();
            if (searchTerm === '' || title.includes(searchTerm) || desc.includes(searchTerm)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    }
});

// Pagination functionality
function createPagination(container, items, itemsPerPage = 12) {
    const totalPages = Math.ceil(items.length / itemsPerPage);
    const paginationContainer = document.createElement('div');
    paginationContainer.className = 'flex justify-center mt-6 space-x-2';

    function showPage(page) {
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        items.forEach((item, index) => {
            item.style.display = (index >= start && index < end) ? 'block' : 'none';
        });
    }

    function createPageButton(page, text) {
        const button = document.createElement('button');
        button.textContent = text;
        button.className = 'px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors';
        button.addEventListener('click', () => {
            showPage(page);
            updateActiveButton(page);
        });
        return button;
    }

    function updateActiveButton(activePage) {
        paginationContainer.querySelectorAll('button').forEach((btn, index) => {
            if (index > 0 && index < paginationContainer.children.length - 1) {
                const pageNum = parseInt(btn.textContent);
                if (pageNum === activePage) {
                    btn.className = 'px-3 py-2 bg-blue-500 text-white border border-blue-500 rounded-lg';
                } else {
                    btn.className = 'px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors';
                }
            }
        });
    }

    if (totalPages > 1) {
        // Previous button
        const prevBtn = createPageButton(1, '«');
        prevBtn.addEventListener('click', () => {
            const currentPage = getCurrentPage();
            if (currentPage > 1) showPage(currentPage - 1);
        });
        paginationContainer.appendChild(prevBtn);

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.appendChild(createPageButton(i, i));
        }

        // Next button
        const nextBtn = createPageButton(totalPages, '»');
        nextBtn.addEventListener('click', () => {
            const currentPage = getCurrentPage();
            if (currentPage < totalPages) showPage(currentPage + 1);
        });
        paginationContainer.appendChild(nextBtn);

        container.appendChild(paginationContainer);
        showPage(1);
        updateActiveButton(1);
    }

    function getCurrentPage() {
        for (let i = 0; i < items.length; i++) {
            if (items[i].style.display !== 'none') {
                return Math.floor(i / itemsPerPage) + 1;
            }
        }
        return 1;
    }
}

// Initialize pagination after DOM load
document.addEventListener('DOMContentLoaded', function(){
    // Package pagination
    const packageItems = Array.from(document.querySelectorAll('.package-option'));
    const packageContainer = document.querySelector('#package-selection .grid');
    if (packageItems.length > 12) {
        createPagination(packageContainer, packageItems, 12);
    }

    // Product pagination
    const productItems = Array.from(document.querySelectorAll('.product-option'));
    const productContainer = document.querySelector('#product-selection .grid');
    if (productItems.length > 12) {
        createPagination(productContainer, productItems, 12);
    }

    // Addon pagination
    const addonItems = Array.from(document.querySelectorAll('.addon-option'));
    const addonContainer = document.querySelector('#addon-selection .grid');
    if (addonItems.length > 12) {
        createPagination(addonContainer, addonItems, 12);
    }
});

// Helper function
function toggleSelection(el, borderClass) {
    el.classList.toggle('selected');
    el.classList.toggle(borderClass);
    el.classList.toggle('border-gray-200');
}

// Update summary
function updateSummary() {
    let total = 0;
    let packages = [];
    let products = [];
    let addons = [];

    const durationInput = document.querySelector('input[name="duration_days"]');
    const duration = durationInput ? parseInt(durationInput.value) || 1 : 1;

    // Handle preselected package
    const preselectedPackageDiv = document.querySelector('.bg-blue-50.border.border-blue-200');
    if (preselectedPackageDiv) {
        const name = preselectedPackageDiv.querySelector('h4').textContent;
        const priceText = preselectedPackageDiv.querySelector('.text-lg.font-bold.text-blue-600').textContent;
        const price = parseInt(priceText.replace(/[^\d]/g, '')) || 0;
        const packageTotal = price * duration;
        packages.push(`${name} - Rp ${price.toLocaleString('id-ID')}/night`);
        total += packageTotal;
    }

    // Handle preselected product
    const preselectedProductDiv = document.querySelector('.bg-green-50.border.border-green-200');
    if (preselectedProductDiv) {
        const name = preselectedProductDiv.querySelector('h4').textContent;
        const priceText = preselectedProductDiv.querySelector('.text-lg.font-bold.text-green-600').textContent;
        const price = parseInt(priceText.replace(/[^\d]/g, '')) || 0;
        products.push(`${name} - Rp ${price.toLocaleString('id-ID')}`);
        total += price;
    }

    // Handle preselected addon
    const preselectedAddonDiv = document.querySelector('.bg-orange-50.border.border-orange-200');
    if (preselectedAddonDiv) {
        const name = preselectedAddonDiv.querySelector('h4').textContent;
        const priceText = preselectedAddonDiv.querySelector('.text-lg.font-bold.text-orange-600').textContent;
        const price = parseInt(priceText.replace(/[^\d]/g, '')) || 0;
        addons.push(`${name} - Rp ${price.toLocaleString('id-ID')}`);
        total += price;
    }

    // Handle selected packages from grid
    document.querySelectorAll('.package-option.selected').forEach(option => {
        const name = option.querySelector('h4').textContent;
        const price = parseInt(option.dataset.price) || 0;
        const packageTotal = price * duration;
        packages.push(`${name} - Rp ${price.toLocaleString('id-ID')}/night`);
        total += packageTotal;
    });

    document.querySelectorAll('.product-option.selected').forEach(option => {
        const name = option.querySelector('h4').textContent;
        const price = parseInt(option.dataset.price) || 0;
        const qtyInput = option.querySelector('input[type="number"]');
        const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
        products.push(`${name} x${qty} - Rp ${(price*qty).toLocaleString('id-ID')}`);
        total += price*qty;
    });
    document.querySelectorAll('.addon-option.selected').forEach(option => {
        const name = option.querySelector('h4').textContent;
        const price = parseInt(option.dataset.price) || 0;
        const qtyInput = option.querySelector('input[type="number"]');
        const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
        addons.push(`${name} x${qty} - Rp ${(price*qty).toLocaleString('id-ID')}`);
        total += price*qty;
    });

    document.getElementById('summary-package').textContent = packages.length ? packages.join(', ') : 'Not selected';
    document.getElementById('summary-product').textContent = products.length ? products.join(', ') : 'None';
    document.getElementById('summary-addons').textContent = addons.length ? addons.join(', ') : 'None';
    document.getElementById('summary-duration').textContent = duration + ' night' + (duration>1?'s':'');
    document.getElementById('summary-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// Event listeners for booking type selection
document.querySelectorAll('.booking-type-option').forEach(option => {
    option.addEventListener('click', function() {
        const type = this.dataset.type;
        const checkbox = this.querySelector('.booking-type-checkbox');

        // Toggle selection
        const colorClass = type==='package'?'border-blue-500':type==='product'?'border-green-500':'border-orange-500';
        toggleSelection(this, colorClass);

        // Update checkbox
        checkbox.checked = this.classList.contains('selected');

        // Show/hide selection sections based on selected types
        const selectedTypes = Array.from(document.querySelectorAll('.booking-type-option.selected')).map(opt => opt.dataset.type);

        ['package','product','addon'].forEach(t => {
            const section = document.getElementById(`${t}-selection`);
            if (selectedTypes.includes(t)) {
                section.classList.remove('hidden');
            } else {
                section.classList.add('hidden');
                // Deselect all options in hidden sections
                section.querySelectorAll(`.${t}-option.selected`).forEach(opt => {
                    opt.classList.remove('selected', `border-${t==='package'?'blue':t==='product'?'green':'orange'}-500`);
                    opt.classList.add('border-gray-200');
                    const cb = opt.querySelector('input[type="checkbox"]');
                    if (cb) cb.checked = false;
                });
            }
        });

        updateSummary();
    });
});

// Selection listeners
['package','product','addon'].forEach(type=>{
    document.querySelectorAll(`.${type}-option`).forEach(option=>{
        option.addEventListener('click', function(e){
            e.stopPropagation(); // avoid triggering type click
            const border = type==='package'?'border-blue-500':type==='product'?'border-green-500':'border-orange-500';
            toggleSelection(this, border);
            const checkbox = this.querySelector(`.${type}-checkbox`);
            if(checkbox) checkbox.checked = this.classList.contains('selected');
            updateSummary();
        });
    });
    // Checkbox listeners
    document.querySelectorAll(`.${type}-checkbox`).forEach(checkbox=>{
        checkbox.addEventListener('change', function(){
            const option = this.closest(`.${type}-option`);
            const border = type==='package'?'border-blue-500':type==='product'?'border-green-500':'border-orange-500';
            if(this.checked){
                option.classList.add('selected', border);
                option.classList.remove('border-gray-200');
            } else {
                option.classList.remove('selected', border);
                option.classList.add('border-gray-200');
            }
            updateSummary();
        });
    });
});

// Quantity update
document.querySelectorAll('.product-option input[type="number"], .addon-option input[type="number"]').forEach(input=>{
    input.addEventListener('input', updateSummary);
});

// Duration update
document.querySelector('input[name="duration_days"]').addEventListener('input', updateSummary);

// Pre-select URL
const urlParams = new URLSearchParams(window.location.search);
['package','product','addon'].forEach(type=>{
    if(urlParams.has(type)){
        const id = urlParams.get(type);
        // Only auto-select if not preselected
        if (!document.querySelector(`.bg-${type==='package'?'blue':'green'}-50.border.border-${type==='package'?'blue':'green'}-200`)) {
            document.querySelector(`.booking-type-option[data-type="${type}"]`)?.click();
            const option = document.querySelector(`.${type}-option[data-${type}="${id}"]`);
            if(option) option.click();
        }
    }
});

// Initial summary
updateSummary();
</script>
@endsection
