@extends('layouts.admin')

@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Create New Packages</h1>

        {{-- Pastikan Route dan Method sudah disiapkan di Controller untuk menerima data array products[] dan addons[] --}}
        <form action="{{ route('admin.packages.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-xl rounded-xl p-8 space-y-8">
            @csrf

            {{-- SECTION 1: PACKAGE DETAILS (NAME, SLUG, IMAGE, DESC) --}}
            <div class="space-y-6">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Package Information</h2>
                
                {{-- **name_package** field --}}
                <div class="space-y-2">
                    <label for="name_package" class="block text-sm font-medium text-gray-700">Package Name</label>
                    <input type="text" name="name_package" id="name_package" value="{{ old('name_package') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name_package') border-red-500 @enderror" required>
                    @error('name_package')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- **SLUG** field --}}
                <div class="space-y-2">
                    <label for="slug" class="block text-sm font-medium text-gray-700">Slug (URL Friendly Name)</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm bg-gray-50 focus:ring-blue-500 focus:border-blue-500 @error('slug') border-red-500 @enderror">
                    <p class="text-xs text-gray-500">Ini akan menjadi bagian dari URL. Contoh: `nama-paket-saya`.</p>
                    @error('slug')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- **description** field --}}
                <div class="space-y-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- **image** field --}}
                <div class="space-y-2">
                    <label for="image" class="block text-sm font-medium text-gray-700">Package Image</label>
                    <input type="file" name="image" id="image" accept="image/*" class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500 @error('image') border-red-500 @enderror">
                    <p class="text-xs text-gray-500">Max 2MB. Format: JPEG, PNG, JPG, GIF.</p>
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- --- --}}

            {{-- SECTION 2: PRODUCTS (Multi-Select) --}}
            <div class="space-y-4 border p-4 rounded-lg bg-gray-50">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Select Products (Multi)</h2>
                <div class="space-y-3">
                    @forelse ($products as $product)
                        <div class="flex items-start space-x-3 product-item" data-price="{{ $product->price ?? 0 }}" data-pax-min="{{ $product->pax ?? 1 }}">
                            <input type="checkbox" id="product_{{ $product->id }}" name="products[]" value="{{ $product->id }}" class="mt-1 product-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="product_{{ $product->id }}" class="flex-1 block text-sm font-medium text-gray-700 cursor-pointer">
                                {{ $product->name }} (Rp{{ number_format($product->price ?? 0, 0, ',', '.') }} / Min Pax: {{ $product->pax ?? 1 }})
                            </label>
                            
                            {{-- Input Pax untuk Produk --}}
                            <div class="w-32">
                                <label for="product_pax_{{ $product->id }}" class="block text-xs text-gray-500 mb-1">Pax</label>
                                <input type="number" id="product_pax_{{ $product->id }}" name="product_pax[{{ $product->id }}]" min="{{ $product->pax ?? 1 }}" value="{{ $product->pax ?? 1 }}" class="pax-input w-full px-2 py-1 border border-gray-300 rounded-lg text-sm bg-white" disabled required>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No products available.</p>
                    @endforelse
                </div>
            </div>
            
            {{-- --- --}}

            {{-- SECTION 3: ADDONS (Multi-Select) --}}
            <div class="space-y-4 border p-4 rounded-lg bg-gray-50">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Select Addons (Multi)</h2>
                <div class="space-y-3">
                    @forelse ($addons as $addon)
                        <div class="flex items-start space-x-3 addon-item" data-price="{{ $addon->price ?? 0 }}" data-pax-min="{{ $addon->pax ?? 1 }}">
                            <input type="checkbox" id="addon_{{ $addon->id }}" name="addons[]" value="{{ $addon->id }}" class="mt-1 addon-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="addon_{{ $addon->id }}" class="flex-1 block text-sm font-medium text-gray-700 cursor-pointer">
                                {{ $addon->addons }} (Rp{{ number_format($addon->price ?? 0, 0, ',', '.') }} / Min Pax: {{ $addon->pax ?? 1 }})
                            </label>
                            
                            {{-- Input Pax untuk Addon --}}
                            <div class="w-32">
                                <label for="addon_pax_{{ $addon->id }}" class="block text-xs text-gray-500 mb-1">Pax</label>
                                <input type="number" id="addon_pax_{{ $addon->id }}" name="addon_pax[{{ $addon->id }}]" min="{{ $addon->pax ?? 1 }}" value="{{ $addon->pax ?? 1 }}" class="pax-input w-full px-2 py-1 border border-gray-300 rounded-lg text-sm bg-white" disabled required>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No addons available.</p>
                    @endforelse
                </div>
            </div>

            {{-- --- --}}

            {{-- SECTION 4: PRICE CALCULATION & PUBLISH FIELDS --}}
            <div class="space-y-6 pt-4">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Pricing & Publishing</h2>

                {{-- **REAL PRICE (Display Only - Akumulasi Otomatis)** --}}
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Real Price (Total Akumulasi Produk & Addon)</label>
                    <div class="p-3 bg-green-100 border border-green-400 rounded-lg">
                        <span class="text-lg font-bold text-green-700" id="real_price_display">Rp0</span>
                        <input type="hidden" name="price_real" id="price_real" value="0">
                    </div>
                </div>

                {{-- **price_publish** field (Bisa Di-edit / Overwrite) --}}
                <div class="space-y-2">
                    <label for="price_publish" class="block text-sm font-medium text-gray-700">Price (Publish) - *Bisa di-edit/override*</label>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="price_publish" id="price_publish" value="{{ old('price_publish') }}" step="1000" min="0" placeholder="0" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('price_publish') border-red-500 @enderror" required>
                    </div>
                    <p class="text-xs text-gray-500">Harga ini akan otomatis terisi dengan Real Price, namun Anda bebas mengeditnya untuk promosi atau diskon.</p>
                    @error('price_publish')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Start & End Publish Dates --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- **start_publish** field --}}
                    <div class="space-y-2">
                        <label for="start_publish" class="block text-sm font-medium text-gray-700">Start Publish Date</label>
                        <input type="date" name="start_publish" id="start_publish" value="{{ old('start_publish', date('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('start_publish') border-red-500 @enderror">
                        @error('start_publish')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- **end_publish** field --}}
                    <div class="space-y-2">
                        <label for="end_publish" class="block text-sm font-medium text-gray-700">End Publish Date (Optional)</label>
                        <input type="date" name="end_publish" id="end_publish" value="{{ old('end_publish') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('end_publish') border-red-500 @enderror">
                        @error('end_publish')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- **is_active** field --}}
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
            
            {{-- BUTTONS --}}
            <div class="flex justify-end space-x-4 pt-4">
                <a href="{{ route('admin.packages') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</a>
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

    // Fungsi utama untuk menghitung total harga nyata (Real Price)
    function calculateRealPrice() {
        let totalRealPrice = 0;

        // 1. Hitung Total dari Products
        document.querySelectorAll('.product-item').forEach(item => {
            const checkbox = item.querySelector('.product-checkbox');
            const paxInput = item.querySelector('.pax-input');
            
            if (checkbox.checked) {
                const price = parseFloat(item.dataset.price) || 0;
                const pax = parseInt(paxInput.value) || 1;
                
                // Kalkulasi: Harga * Pax (jika Anda ingin harga per pax, kalau tidak, Price saja)
                // Diasumsikan harga product adalah harga dasarnya, dan pax adalah jumlah yang dibeli/dimasukkan
                totalRealPrice += price * pax; 
            }
        });

        // 2. Hitung Total dari Addons
        document.querySelectorAll('.addon-item').forEach(item => {
            const checkbox = item.querySelector('.addon-checkbox');
            const paxInput = item.querySelector('.pax-input');

            if (checkbox.checked) {
                const price = parseFloat(item.dataset.price) || 0;
                const pax = parseInt(paxInput.value) || 1;

                // Kalkulasi: Harga * Pax (sesuai asumsi Product di atas)
                totalRealPrice += price * pax; 
            }
        });

        // Update display dan hidden field
        document.getElementById('real_price_display').textContent = formatRupiah(totalRealPrice);
        document.getElementById('price_real').value = totalRealPrice;
        
        // Update Price Publish (jika Price Publish masih kosong atau sama dengan Real Price sebelumnya)
        const pricePublishInput = document.getElementById('price_publish');
        const currentPublishValue = parseFloat(pricePublishInput.value) || 0;
        
        // HANYA update Price Publish jika Price Publish belum diisi (0) atau belum pernah di-edit oleh user.
        // Karena kita tidak bisa tahu apakah user sudah edit, kita cek apakah input kosong.
        if (pricePublishInput.value === "" || currentPublishValue === 0) {
             pricePublishInput.value = totalRealPrice;
        }

    }

    document.addEventListener('DOMContentLoaded', function () {
        const namePackageInput = document.getElementById('name_package');
        const slugInput = document.getElementById('slug');
        const itemsContainer = document.querySelector('.container'); // Container utama

        // --- 1. SLUG AUTOGENERATION ---
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
        
        // --- 2. LOGIC CHECKBOX & PAX ---
        
        // Event listener untuk semua checkbox (Product dan Addon)
        itemsContainer.querySelectorAll('.product-checkbox, .addon-checkbox').forEach(checkbox => {
            // Aktifkan atau nonaktifkan input Pax
            const paxInput = checkbox.closest('.flex').querySelector('.pax-input');

            // Set initial state based on old input value if available
            paxInput.disabled = !checkbox.checked;

            checkbox.addEventListener('change', function() {
                // Aktifkan/nonaktifkan input PAX
                paxInput.disabled = !this.checked;
                if (this.checked) {
                    // Set Pax ke nilai minimumnya saat dicentang
                    paxInput.value = paxInput.min;
                    paxInput.focus();
                } else {
                    // Reset Pax saat tidak dicentang
                    paxInput.value = paxInput.min; 
                }
                calculateRealPrice();
            });
        });
        
        // Event listener untuk semua input Pax
        itemsContainer.querySelectorAll('.pax-input').forEach(paxInput => {
            paxInput.addEventListener('input', function() {
                // Pastikan input tidak kurang dari nilai min
                let currentValue = parseInt(this.value);
                let minValue = parseInt(this.min);

                if (currentValue < minValue) {
                    this.value = minValue;
                }
                
                calculateRealPrice();
            });
        });
        
        // --- 3. INITIAL CALCULATION ---
        calculateRealPrice();
    });
</script>
@endpush

@endsection