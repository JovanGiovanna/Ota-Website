@extends('layouts.superadmin')

@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Create New Packages</h1>

        <form action="{{ route('super_admin.packages.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-xl rounded-xl p-8 space-y-8">
            @csrf

            <div class="space-y-6">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Package Information</h2>

                <div class="space-y-2">
                    <label for="name_package" class="block text-sm font-medium text-gray-700">Package Name</label>
                    <input type="text" name="name_package" id="name_package" value="{{ old('name_package') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name_package') border-red-500 @enderror" required>
                    @error('name_package')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="slug" class="block text-sm font-medium text-gray-700">Slug (URL Friendly Name)</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:ring-blue-500 focus:border-blue-500 @error('slug') border-red-500 @enderror">
                    <p class="text-xs text-gray-500">Ini akan menjadi bagian dari URL. Contoh: `nama-paket-saya`.</p>
                    @error('slug')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="location" class="block text-sm font-medium text-gray-700">Location / Address</label>
                    <input type="text" name="location" id="location" value="{{ old('location') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror">
                    @error('location')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Contact Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Contoh: +62812xxxx" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="refund_policy" class="block text-sm font-medium text-gray-700">Refund Policy</label>
                    <select name="refund_policy" id="refund_policy" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('refund_policy') border-red-500 @enderror">
                        <option value="">Pilih Refund Policy</option>
                        <option value="mendukung" {{ old('refund_policy', 'mendukung') == 'mendukung' ? 'selected' : '' }}>Mendukung (User dapat refund saat booking)</option>
                        <option value="tidak mendukung" {{ old('refund_policy') == 'tidak mendukung' ? 'selected' : '' }}>Tidak Mendukung (User tidak dapat refund saat booking)</option>
                    </select>
                    @error('refund_policy')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="images" class="block text-sm font-medium text-gray-700">Package Images (Multiple)</label>
                    
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500 @error('images') border-red-500 @enderror @error('images.*') border-red-500 @enderror">
                    
                    <p class="text-xs text-gray-500">Pilih satu atau lebih gambar. Max 2MB per file. Format: JPEG, PNG, JPG, GIF.</p>
                    
                    @error('images')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    
                    @error('images.*')
                        <p class="text-red-500 text-xs mt-1">Satu atau lebih file gambar gagal diunggah: {{ $message }}</p>
                    @enderror
                </div>
            </div>


            <div class="space-y-4 border p-4 rounded-lg bg-gray-50">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Select Products (Multi)</h2>
                
                <div class="space-y-3 p-2 bg-white rounded-lg shadow-inner border max-h-96 overflow-y-auto"> 
                    @forelse ($products as $product)
                        @php
                            $isProductChecked = is_array(old('products')) && in_array($product->id, old('products'));
                            $paxValue = old('product_pax.' . $product->id, $product->pax ?? 1);
                            $productNTA = $product->nta ?? $product->basic_price ?? 0;
                            $outOfStock = ($product->jumlah ?? 0) <= 0;
                        @endphp

                        <div class="flex items-start space-x-3 product-item {{ $outOfStock ? 'opacity-50' : '' }}" data-nta="{{ $productNTA }}" data-pax-min="{{ $product->pax ?? 1 }}">
                            <input type="checkbox" id="product_{{ $product->id }}" name="products[]" value="{{ $product->id }}" class="mt-1 product-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ $isProductChecked ? 'checked' : '' }} {{ $outOfStock ? 'disabled' : '' }}>
                            <label for="product_{{ $product->id }}" class="flex-1 block text-sm font-medium {{ $outOfStock ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 cursor-pointer' }}">
                                {{ $product->name }} (<span class="font-bold {{ $outOfStock ? 'text-red-500' : 'text-green-700' }}">Price: Rp{{ number_format($productNTA, 0, ',', '.') }}</span>)
                                <span class="ml-2 text-xs {{ $outOfStock ? 'text-red-500 font-semibold' : 'text-gray-500' }}">Stok: {{ $product->jumlah ?? 0 }}{{ $outOfStock ? ' (Habis)' : '' }}</span>
                            </label>
                            
                            <div class="w-32">
                                <label for="product_pax_{{ $product->id }}" class="block text-xs text-gray-500 mb-1">Jumlah (informasi saja)</label>
                                <input type="number" id="product_pax_{{ $product->id }}" name="product_pax[{{ $product->id }}]" min="{{ $product->pax ?? 1 }}" value="{{ $paxValue }}" class="pax-input w-full px-2 py-1 border border-gray-300 rounded-lg text-sm bg-white" required {{ $isProductChecked ? '' : 'disabled' }}>
                                <p class="text-[11px] text-gray-500 mt-1">Tidak mempengaruhi harga; hanya pencatatan.</p>
                                @error('product_pax.' . $product->id)
                                    <p class="text-red-500 text-xs mt-1">Wajib</p>
                                @enderror
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No products available.</p>
                    @endforelse
                    @error('products')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                @if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
                @endif
            </div>
            
            
            <div class="space-y-4 border p-4 rounded-lg bg-gray-50">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Select Addons (Multi)</h2>
                
                <div class="space-y-3 p-2 bg-white rounded-lg shadow-inner border max-h-96 overflow-y-auto">
                    @forelse ($addons as $addon)
                        @php
                            $isAddonChecked = is_array(old('addons')) && in_array($addon->id, old('addons'));
                            $paxValue = old('addon_pax.' . $addon->id, $addon->pax ?? 1);
                            $addonNTA = $addon->nta ?? $addon->basic_price ?? 0;
                            $outOfStock = ($addon->jumlah ?? 0) <= 0;
                        @endphp

                        <div class="flex items-start space-x-3 addon-item {{ $outOfStock ? 'opacity-50' : '' }}" data-nta="{{ $addonNTA }}" data-pax-min="{{ $addon->pax ?? 1 }}">
                            <input type="checkbox" id="addon_{{ $addon->id }}" name="addons[]" value="{{ $addon->id }}" class="mt-1 addon-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ $isAddonChecked ? 'checked' : '' }} {{ $outOfStock ? 'disabled' : '' }}>
                            <label for="addon_{{ $addon->id }}" class="flex-1 block text-sm font-medium {{ $outOfStock ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 cursor-pointer' }}">
                                {{ $addon->addons }} (<span class="font-bold {{ $outOfStock ? 'text-red-500' : 'text-green-700' }}">Price: Rp{{ number_format($addonNTA, 0, ',', '.') }}</span>)
                                <span class="ml-2 text-xs {{ $outOfStock ? 'text-red-500 font-semibold' : 'text-gray-500' }}">Stok: {{ $addon->jumlah ?? 0 }}{{ $outOfStock ? ' (Habis)' : '' }}</span>
                            </label>
                            
                            <div class="w-32">
                                <label for="addon_pax_{{ $addon->id }}" class="block text-xs text-gray-500 mb-1">Jumlah (informasi saja)</label>
                                <input type="number" id="addon_pax_{{ $addon->id }}" name="addon_pax[{{ $addon->id }}]" min="{{ $addon->pax ?? 1 }}" value="{{ $paxValue }}" class="pax-input w-full px-2 py-1 border border-gray-300 rounded-lg text-sm bg-white" required {{ $isAddonChecked ? '' : 'disabled' }}>
                                <p class="text-[11px] text-gray-500 mt-1">Tidak mempengaruhi harga; hanya pencatatan.</p>
                                @error('addon_pax.' . $addon->id)
                                    <p class="text-red-500 text-xs mt-1">Wajib</p>
                                @enderror
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

            
            <div class="space-y-6 pt-4">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Pricing & Publishing</h2>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Nett Total Package Price (NTA) - Total Harga Pokok</label>
                    <div class="p-3 bg-green-100 border border-green-400 rounded-lg">
                        <span class="text-lg font-bold text-green-700" id="nta_display">Rp0</span>
                        <input type="hidden" name="nta" id="nta" value="{{ old('nta', 0) }}">
                    </div>
                </div>

                <input type="hidden" name="total_price" id="total_price" value="{{ old('total_price', 0) }}">
                <input type="hidden" name="pax_paid_input" id="pax_paid_input" value="{{ old('pax_paid_input', 0) }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="start_publish" class="block text-sm font-medium text-gray-700">Start Publish Date</label>
                        <input type="date" name="start_publish" id="start_publish" value="{{ old('start_publish', date('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('start_publish') border-red-500 @enderror">
                        @error('start_publish')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="end_publish" class="block text-sm font-medium text-gray-700">End Publish Date (Optional)</label>
                        <input type="date" name="end_publish" id="end_publish" value="{{ old('end_publish') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('end_publish') border-red-500 @enderror">
                        @error('end_publish')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Active Status</label>
                    <select name="is_active" id="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active (Tayang)</option>
                        <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inactive / Draft (Belum Tayang)</option>
                    </select>
                    @error('is_active')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('super_admin.packages') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-md">Create Package</button>
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

    function calculatePrice() {
        let totalNTA = 0;

        // Price is sum of selected items only; quantity is informational
        document.querySelectorAll('.product-item').forEach(item => {
            const checkbox = item.querySelector('.product-checkbox');
            if (checkbox.checked) {
                const nta = parseFloat(item.dataset.nta) || 0;
                totalNTA += nta;
            }
        });

        document.querySelectorAll('.addon-item').forEach(item => {
            const checkbox = item.querySelector('.addon-checkbox');
            if (checkbox.checked) {
                const nta = parseFloat(item.dataset.nta) || 0;
                totalNTA += nta;
            }
        });

        document.getElementById('nta_display').textContent = formatRupiah(totalNTA);
        document.getElementById('nta').value = totalNTA;
        
        const totalPrice = totalNTA;
        
        document.getElementById('total_price').value = totalPrice;
        document.getElementById('pax_paid_input').value = totalPrice > 0 ? totalPrice : 0;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const namePackageInput = document.getElementById('name_package');
        const slugInput = document.getElementById('slug');
        const itemsContainer = document.querySelector('.container');
        
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

        itemsContainer.querySelectorAll('.product-checkbox, .addon-checkbox').forEach(checkbox => {
            const paxInput = checkbox.closest('.flex').querySelector('.pax-input');
            paxInput.disabled = !checkbox.checked;
            
            checkbox.addEventListener('change', function() {
                paxInput.disabled = !this.checked;
                if (this.checked) {
                    paxInput.value = paxInput.min;
                    paxInput.focus();
                } else {
                    paxInput.value = paxInput.min;
                }
                
                calculatePrice();
            });
        });

        itemsContainer.querySelectorAll('.pax-input').forEach(paxInput => {
            paxInput.addEventListener('input', function() {
                let currentValue = parseInt(this.value);
                let minValue = parseInt(this.min);

                if (currentValue < minValue) {
                    this.value = minValue;
                }
                
                calculatePrice();
            });
            paxInput.addEventListener('change', function() {
                 calculatePrice();
            });
        });
        
        // Upsell & discount fields removed: pricing derives only from selections

        calculatePrice();
    });

</script>

@endpush
@endsection
