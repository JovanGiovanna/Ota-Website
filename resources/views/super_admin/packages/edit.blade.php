@extends('layouts.superadmin')

@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Package: {{ $package->name_package }}</h1>

        <form action="{{ route('super_admin.packages.update', $package) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-xl rounded-xl p-8 space-y-8">
            @csrf
            @method('PUT')

            {{-- SECTION 1: PACKAGE DETAILS --}}
            <div class="space-y-6">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Package Information</h2>

                <div class="space-y-2">
                    <label for="name_package" class="block text-sm font-medium text-gray-700">Package Name</label>
                    <input type="text" name="name_package" id="name_package" value="{{ old('name_package', $package->name_package) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name_package') border-red-500 @enderror" required>
                    @error('name_package')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $package->slug) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:ring-blue-500 focus:border-blue-500 @error('slug') border-red-500 @enderror">
                    @error('slug')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $package->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Images --}}
                <div class="space-y-4">
                    <label for="images" class="block text-sm font-medium text-gray-700">Package Images</label>

                    {{-- Current Images --}}
                    @if($package->images && is_array($package->images) && count($package->images) > 0)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-3">Current Images:</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach($package->images as $index => $img)
                                    <div class="relative group">
                                        <img src="{{ asset('storage/' . $img) }}" alt="Current Image" class="w-full h-32 object-cover rounded-lg border-2 border-gray-200 shadow-sm">
                                        <div class="absolute top-2 right-2 flex items-center space-x-2">
                                            <input type="checkbox" name="remove_images[]" value="{{ $index }}" id="remove_img_{{ $index }}" class="w-5 h-5 text-red-600 bg-white border-2 border-gray-300 rounded focus:ring-red-500 focus:ring-2">
                                            <label for="remove_img_{{ $index }}" class="text-xs text-white bg-red-600 px-2 py-1 rounded-md cursor-pointer hover:bg-red-700 transition-colors">Remove</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Check the boxes above images you want to remove.</p>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <label for="images" class="block text-sm font-medium text-gray-700">Add New Images</label>
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-l-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-colors @error('images') border-red-500 @enderror @error('images.*') border-red-500 @enderror">
                        <p class="text-xs text-gray-500">Leave empty to keep current images. Max 10 images, each 2MB. You can select multiple files.</p>
                    </div>

                    @error('images')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="text-red-500 text-xs mt-1">One or more images failed to upload: {{ $message }}</p>
                    @enderror
                    @error('remove_images')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- SECTION 2: PRODUCTS --}}
            <div class="space-y-4 border p-4 rounded-lg bg-gray-50">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Select Products</h2>

                <div class="space-y-3 p-2 bg-white rounded-lg shadow-inner border max-h-96 overflow-y-auto">
                    @forelse ($products as $product)
                        @php
                            $isSelected = in_array($product->id, $selectedProductIds);
                            $initialPax = 1;
                            if (old('product_pax') && isset(old('product_pax')[$product->id])) {
                                $initialPax = old('product_pax')[$product->id];
                            } elseif ($isSelected && $package->products_data) {
                                $paxData = collect($selectedProductsData)->firstWhere('id', $product->id);
                                $initialPax = $paxData['pax'] ?? 1;
                            }
                        @endphp

                        <div class="flex items-start space-x-3 product-item" data-price="{{ $product->price ?? 0 }}">
                            <input type="checkbox" id="product_{{ $product->id }}" name="products[]" value="{{ $product->id }}" class="mt-1 product-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ $isSelected ? 'checked' : '' }}>
                            <label for="product_{{ $product->id }}" class="flex-1 block text-sm font-medium text-gray-700 cursor-pointer">
                                {{ $product->name }} (Rp{{ number_format($product->price ?? 0, 0, ',', '.') }})
                            </label>
                            <div class="w-32">
                                <label for="product_pax_{{ $product->id }}" class="block text-xs text-gray-500 mb-1">Pax</label>
                                <input type="number" id="product_pax_{{ $product->id }}" name="product_pax[{{ $product->id }}]" min="1" value="{{ $initialPax }}" class="pax-input w-full px-2 py-1 border border-gray-300 rounded-lg text-sm bg-white" {{ $isSelected ? '' : 'disabled' }} required>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No products available.</p>
                    @endforelse
                </div>

                @error('products')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                @if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
                @endif
            </div>

            {{-- SECTION 3: ADDONS --}}
            <div class="space-y-4 border p-4 rounded-lg bg-gray-50">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Select Addons</h2>

                <div class="space-y-3 p-2 bg-white rounded-lg shadow-inner border max-h-96 overflow-y-auto">
                    @forelse ($addons as $addon)
                        @php
                            $isSelected = in_array($addon->id, $selectedAddonIds);
                            $initialPax = 1;
                            if (old('addon_pax') && isset(old('addon_pax')[$addon->id])) {
                                $initialPax = old('addon_pax')[$addon->id];
                            } elseif ($isSelected && $package->addons_data) {
                                $paxData = collect($selectedAddonsData)->firstWhere('id', $addon->id);
                                $initialPax = $paxData['pax'] ?? 1;
                            }
                        @endphp

                        <div class="flex items-start space-x-3 addon-item" data-price="{{ $addon->price ?? 0 }}">
                            <input type="checkbox" id="addon_{{ $addon->id }}" name="addons[]" value="{{ $addon->id }}" class="mt-1 addon-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ $isSelected ? 'checked' : '' }}>
                            <label for="addon_{{ $addon->id }}" class="flex-1 block text-sm font-medium text-gray-700 cursor-pointer">
                                {{ $addon->addons }} (Rp{{ number_format($addon->price ?? 0, 0, ',', '.') }})
                            </label>
                            <div class="w-32">
                                <label for="addon_pax_{{ $addon->id }}" class="block text-xs text-gray-500 mb-1">Pax</label>
                                <input type="number" id="addon_pax_{{ $addon->id }}" name="addon_pax[{{ $addon->id }}]" min="1" value="{{ $initialPax }}" class="pax-input w-full px-2 py-1 border border-gray-300 rounded-lg text-sm bg-white" {{ $isSelected ? '' : 'disabled' }} required>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No addons available.</p>
                    @endforelse
                </div>

                @if ($addons instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-4">
                    {{ $addons->links() }}
                </div>
                @endif
            </div>

            {{-- SECTION 4: VENDOR --}}
            <div class="space-y-4 border p-4 rounded-lg bg-yellow-50">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Select Vendor</h2>

                <div class="space-y-2">
                    <label for="id_vendor_info" class="block text-sm font-medium text-gray-700">Vendor</label>
                    <select name="id_vendor_info" id="id_vendor_info" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('id_vendor_info') border-red-500 @enderror" required>
                        <option value="">-- Choose Vendor --</option>
                        @foreach($vendorInfos as $vendorInfo)
                            <option value="{{ $vendorInfo->id }}" {{ old('id_vendor_info', $package->id_vendor_info) == $vendorInfo->id ? 'selected' : '' }}>
                                {{ $vendorInfo->name_corporate }} ({{ $vendorInfo->vendor->name ?? 'No Vendor Name' }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_vendor_info')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- SECTION 5: PRICING --}}
            <div class="space-y-6 pt-4">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Pricing & Publishing</h2>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Real Price</label>
                    <div class="p-3 bg-green-100 border border-green-400 rounded-lg">
                        <span class="text-lg font-bold text-green-700" id="real_price_display">Rp{{ number_format(old('price_real', $package->price_real), 0, ',', '.') }}</span>
                        <input type="hidden" name="price_real" id="price_real" value="{{ old('price_real', $package->price_real) }}">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="discount_percentage" class="block text-sm font-medium text-gray-700">Discount Percentage (%)</label>
                    <div class="relative rounded-lg shadow-sm">
                        <input type="number" name="discount_percentage" id="discount_percentage" value="{{ old('discount_percentage', $package->discount_percentage ?? 0) }}" min="0" max="100" class="w-full pr-6 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('discount_percentage') border-red-500 @enderror">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">%</span>
                        </div>
                    </div>
                    @error('discount_percentage')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="price_publish_input" class="block text-sm font-medium text-gray-700">Price Publish</label>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="price_publish_input" id="price_publish" value="{{ old('price_publish_input', $package->price_publish) }}" step="1" min="0" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('price_publish_input') border-red-500 @enderror" required>
                    </div>
                    @error('price_publish_input')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="start_publish" class="block text-sm font-medium text-gray-700">Start Publish Date</label>
                        <input type="date" name="start_publish" id="start_publish" value="{{ old('start_publish', \Carbon\Carbon::parse($package->start_publish)->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('start_publish') border-red-500 @enderror" required>
                        @error('start_publish')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="end_publish" class="block text-sm font-medium text-gray-700">End Publish Date</label>
                        <input type="date" name="end_publish" id="end_publish" value="{{ old('end_publish', $package->end_publish ? \Carbon\Carbon::parse($package->end_publish)->format('Y-m-d') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('end_publish') border-red-500 @enderror">
                        @error('end_publish')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Active Status</label>
                    <select name="is_active" id="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="1" {{ old('is_active', $package->is_active) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $package->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('super_admin.packages') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-md">Update Package</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function slugify(text) {
        return text.toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    function calculateRealPrice() {
        let totalRealPrice = 0;

        document.querySelectorAll('.product-item').forEach(item => {
            const checkbox = item.querySelector('.product-checkbox');
            const paxInput = item.querySelector('.pax-input');

            if (checkbox.checked) {
                const price = parseFloat(item.dataset.price) || 0;
                const pax = parseInt(paxInput.value) || 1;
                totalRealPrice += price * pax;
            }
        });

        document.querySelectorAll('.addon-item').forEach(item => {
            const checkbox = item.querySelector('.addon-checkbox');
            const paxInput = item.querySelector('.pax-input');

            if (checkbox.checked) {
                const price = parseFloat(item.dataset.price) || 0;
                const pax = parseInt(paxInput.value) || 1;
                totalRealPrice += price * pax;
            }
        });

        document.getElementById('real_price_display').textContent = formatRupiah(totalRealPrice);
        document.getElementById('price_real').value = totalRealPrice;

        const discountInput = document.getElementById('discount_percentage');
        const pricePublishInput = document.getElementById('price_publish');

        let discountPercentage = parseFloat(discountInput.value) || 0;
        discountPercentage = Math.min(100, Math.max(0, discountPercentage));

        const pricePublishFinal = totalRealPrice * (1 - (discountPercentage / 100));
        pricePublishInput.value = Math.round(pricePublishFinal);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const namePackageInput = document.getElementById('name_package');
        const slugInput = document.getElementById('slug');
        const discountInput = document.getElementById('discount_percentage');

        namePackageInput.addEventListener('keyup', function () {
            if (slugInput.value === '') {
                slugInput.value = slugify(namePackageInput.value);
            }
        });

        namePackageInput.addEventListener('blur', function () {
            if (slugInput.value === '') {
                slugInput.value = slugify(namePackageInput.value);
            }
        });

        document.querySelectorAll('.product-checkbox, .addon-checkbox').forEach(checkbox => {
            const paxInput = checkbox.closest('.flex').querySelector('.pax-input');
            checkbox.addEventListener('change', function() {
                paxInput.disabled = !this.checked;
                if (this.checked) {
                    paxInput.value = paxInput.min;
                    paxInput.focus();
                } else {
                    paxInput.value = paxInput.min;
                }
                calculateRealPrice();
            });
        });

        document.querySelectorAll('.pax-input').forEach(paxInput => {
            paxInput.addEventListener('input', function() {
                let currentValue = parseInt(this.value);
                let minValue = parseInt(this.min);

                if (currentValue < minValue) {
                    this.value = minValue;
                }
                calculateRealPrice();
            });
        });

        discountInput.addEventListener('input', calculateRealPrice);

        calculateRealPrice();
    });
</script>
@endpush

@endsection
