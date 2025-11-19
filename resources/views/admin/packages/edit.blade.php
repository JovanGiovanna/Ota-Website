@extends('layouts.admin')


@section('content')

<div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        
        <h1 class="text-4xl font-extrabold text-gray-900 mb-6">
            Edit Package: <span class="text-indigo-600">{{ $package->name_package }}</span>
        </h1>

        <form id="package-form" action="{{ route('admin.packages.update', $package) }}" method="POST" enctype="multipart/form-data" class="bg-white p-8 shadow-xl rounded-xl border border-gray-200">
            @csrf
            {{-- Pastikan menggunakan PUT untuk update --}}
            @method('PUT') 

            {{-- Bagian 1: Info Dasar & Publish --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 p-6 bg-indigo-50/50 rounded-lg border border-indigo-100">
                <div class="lg:col-span-2">
                    <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Package Details</h2>

                    {{-- Nama Paket & Slug (jika ada) --}}
                    <div class="mb-4">
                        <label for="name_package" class="block text-sm font-semibold text-gray-700 mb-2">Package Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name_package" id="name_package" value="{{ old('name_package', $package->name_package) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150" required>
                        @error('name_package')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150">{{ old('description', $package->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Kolom Gambar --}}
                <div class="lg:col-span-1">
                    <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Image & Status</h2>

                    {{-- Gambar --}}
                    <div class="mb-4">
                        <label for="images" class="block text-sm font-semibold text-gray-700 mb-2">Package Images</label>
                        @php
                            $hasImages = $package->images && is_array($package->images) && count($package->images) > 0;
                        @endphp
                        @if($hasImages)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Current Images (Check individual images to remove):</p>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2" id="images_grid">
                                    @foreach($package->images as $index => $img)
                                        <div class="relative">
                                            <img src="{{ asset('storage/' . $img) }}" alt="Current Image" class="w-full h-20 object-cover rounded-md border border-gray-200">
                                            <input type="checkbox" name="remove_images[]" value="{{ $img }}" id="remove_image_{{ $index }}" class="remove_image_checkbox absolute top-1 right-1 w-4 h-4 text-red-600 bg-white border-gray-300 rounded focus:ring-red-500 z-10">
                                            <label for="remove_image_{{ $index }}" class="absolute top-1 right-1 text-xs text-white bg-red-500 px-1 py-0.5 rounded cursor-pointer z-20">Remove</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="mb-4">
                                <div class="flex items-center mb-2">
                                    <input type="checkbox" id="select_all_images" class="w-4 h-4 text-red-600 bg-white border-gray-300 rounded focus:ring-red-500" disabled>
                                    <label for="select_all_images" class="ml-2 text-sm text-gray-400 cursor-not-allowed">Select All Images to Remove (No images to remove)</label>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">No current images.</p>
                            </div>
                        @endif
                        <input type="file" name="images[]" id="images" accept="image/*" multiple class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition duration-150">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to keep current images. Max 10 images, each 2MB.</p>
                        @error('images')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        @error('images.*')
                            <p class="text-red-500 text-sm mt-1">One or more images failed to upload: {{ $message }}</p>
                        @enderror
                        @error('remove_images')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        @error('remove_images.*')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    {{-- Aktif/Non-aktif --}}
                    <div class="mb-4">
                        <label for="is_active" class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="is_active" id="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150">
                            <option value="1" {{ old('is_active', $package->is_active) == 1 ? 'selected' : '' }}>Active (Tayang)</option>
                            <option value="0" {{ old('is_active', $package->is_active) == 0 ? 'selected' : '' }}>Inactive (Draft)</option>
                        </select>
                        @error('is_active')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Bagian 2: Harga & Periode --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 p-6 bg-emerald-50/50 rounded-lg border border-emerald-100">
                <div class="md:col-span-4">
                    <h2 class="text-2xl font-semibold text-emerald-700 mb-4">Pricing & Publication Period</h2>
                </div>
                
                {{-- Harga Real (Akumulasi) --}}
                <div>
                    <label for="price_real_display" class="block text-sm font-semibold text-gray-700 mb-2">Accumulated Price (Rp)</label>
                    <input type="text" id="price_real_display" value="{{ number_format(old('price_real', $package->price_real), 0, ',', '.') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 font-medium" readonly>
                    {{-- Input Hidden untuk dikirim ke backend --}}
                    <input type="hidden" name="price_real" id="price_real" value="{{ old('price_real', $package->price_real) }}">
                    <p class="text-xs text-gray-500 mt-1">Dihitung otomatis dari produk & addon.</p>
                </div>
                
                {{-- Discount Percentage (Dengan perbaikan padding) --}}
                <div>
                    <label for="discount_percentage" class="block text-sm font-semibold text-pink-700 mb-2">Discount (%) <span class="text-red-500">*</span></label>
                    <div class="relative rounded-lg shadow-sm">
                        {{-- pr-6 memberi ruang di input field --}}
                        <input type="number" name="discount_percentage" id="discount_percentage" value="{{ old('discount_percentage', $package->discount_percentage) }}" min="0" max="100" class="w-full pr-6 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 transition duration-150" required>
                        {{-- pr-2 menggeser simbol % lebih jauh dari tepi --}}
                        <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">%</span>
                        </div>
                    </div>
                    @error('discount_percentage')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">0% = Harga Normal. 100% = Gratis.</p>
                </div>

                {{-- Harga Publish (Harga Jual) --}}
                <div>
                    <label for="price_publish_input" class="block text-sm font-semibold text-green-700 mb-2">Publish Price (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="price_publish_input" id="price_publish_input" value="{{ old('price_publish_input', $package->price_publish) }}" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition duration-150" required>
                    @error('price_publish_input')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1" id="price-publish-note">Harga di atas dihitung otomatis.</p>
                </div>
                
                {{-- Tanggal Mulai Publish --}}
                <div>
                    <label for="start_publish" class="block text-sm font-semibold text-gray-700 mb-2">Start Publish Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_publish" id="start_publish" value="{{ old('start_publish', \Carbon\Carbon::parse($package->start_publish)->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition duration-150" required>
                    @error('start_publish')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Tanggal Akhir Publish --}}
                <div class="md:col-span-4">
                    <label for="end_publish" class="block text-sm font-semibold text-gray-700 mb-2">End Publish Date (Optional)</label>
                    <input type="date" name="end_publish" id="end_publish" value="{{ old('end_publish', $package->end_publish ? \Carbon\Carbon::parse($package->end_publish)->format('Y-m-d') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition duration-150">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika paket ingin tayang tanpa batas waktu.</p>
                    @error('end_publish')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Bagian 3: Vendor Selection --}}
            <div class="mb-8 p-6 bg-yellow-50/50 rounded-lg border border-yellow-100">
                <h2 class="text-2xl font-semibold text-yellow-700 mb-4">Vendor Selection</h2>

                <div class="space-y-2">
                    <label for="id_vendor_info" class="block text-sm font-semibold text-gray-700 mb-2">Select Vendor <span class="text-red-500">*</span></label>
                    <select name="id_vendor_info" id="id_vendor_info" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 transition duration-150" required>
                        <option value="">-- Choose Vendor --</option>
                        @foreach($vendorInfos as $vendorInfo)
                            <option value="{{ $vendorInfo->id }}" {{ old('id_vendor_info', $package->id_vendor_info) == $vendorInfo->id ? 'selected' : '' }}>
                                {{ $vendorInfo->name_corporate }} ({{ $vendorInfo->vendor->name ?? 'No Vendor Name' }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_vendor_info')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Bagian 4: Products dan Addons (Menggunakan Checkbox) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                
                {{-- Pemilihan Products (Checkbox Style) --}}
                <div class="space-y-4 border p-4 rounded-lg bg-blue-50/50">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2 text-blue-700">Products Included <span class="text-red-500">*</span></h3>
                    
                    <div class="space-y-3 p-2 bg-white rounded-lg shadow-inner border max-h-96 overflow-y-auto"> 
                        @forelse ($products as $product)
                            {{-- Cek apakah produk ini sudah dipilih (baik dari data tersimpan atau old data) --}}
                            @php
                                // Cek data yang sudah ada di database
                                $isSelected = in_array($product->id, $selectedProductIds); 
                                
                                // Cek data dari old input (jika ada error validasi)
                                // Jika ada old input, kita prioritaskan old input
                                if (old('products')) {
                                    $isSelected = in_array($product->id, old('products'));
                                }

                                // Ambil PAX yang sudah tersimpan atau dari old input
                                $initialPax = 1;
                                if (old('product_pax') && isset(old('product_pax')[$product->id])) {
                                    $initialPax = old('product_pax')[$product->id];
                                } elseif ($isSelected && $package->products_data) {
                                    // Ambil dari data tersimpan jika tidak ada old input
                                    $paxData = collect($selectedProductsData)->firstWhere('id', $product->id);
                                    $initialPax = $paxData['pax'] ?? 1;
                                }
                                $minPax = 1; // Jika produk tidak memiliki min pax

                            @endphp
                            
                            <div class="flex items-start space-x-3 product-item" data-id="{{ $product->id }}" data-price="{{ $product->price ?? 0 }}">
                                
                                {{-- Checkbox --}}
                                <input type="checkbox" id="product_{{ $product->id }}" name="products[]" value="{{ $product->id }}" 
                                    class="mt-1 product-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ $isSelected ? 'checked' : '' }}>
                                
                                {{-- Label --}}
                                <label for="product_{{ $product->id }}" class="flex-1 block text-sm font-medium text-gray-700 cursor-pointer">
                                    {{ $product->name }} (Rp{{ number_format($product->price ?? 0, 0, ',', '.') }})
                                </label>
                                
                                {{-- Input Pax untuk Produk --}}
                                <div class="w-32">
                                    <label for="product_pax_{{ $product->id }}" class="block text-xs text-gray-500 mb-1">Pax</label>
                                    <input type="number" id="product_pax_{{ $product->id }}" name="product_pax[{{ $product->id }}]" 
                                        min="{{ $minPax }}" value="{{ $initialPax }}" 
                                        class="pax-input w-full px-2 py-1 border border-gray-300 rounded-lg text-sm bg-white" 
                                        {{ $isSelected ? '' : 'disabled' }} required>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">No products available.</p>
                        @endforelse
                    </div>

                    @error('products')
                        <p class="text-red-500 text-sm mt-1">Anda harus memilih setidaknya satu produk.</p>
                    @enderror

                @if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
                @endif
                </div>


                {{-- Pemilihan Addons (Checkbox Style) --}}
                <div class="space-y-4 border p-4 rounded-lg bg-purple-50/50">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2 text-purple-700">Addons (Optional)</h3>
                    
                    <div class="space-y-3 p-2 bg-white rounded-lg shadow-inner border max-h-96 overflow-y-auto">
                        @forelse ($addons as $addon)
                            @php
                                // Cek data yang sudah ada di database
                                $isSelected = in_array($addon->id, $selectedAddonIds); 

                                // Cek data dari old input (jika ada error validasi)
                                if (old('addons')) {
                                    $isSelected = in_array($addon->id, old('addons'));
                                }

                                // Ambil PAX yang sudah tersimpan atau dari old input
                                $initialPax = 1;
                                if (old('addon_pax') && isset(old('addon_pax')[$addon->id])) {
                                    $initialPax = old('addon_pax')[$addon->id];
                                } elseif ($isSelected && $package->addons_data) {
                                    $paxData = collect($selectedAddonsData)->firstWhere('id', $addon->id);
                                    $initialPax = $paxData['pax'] ?? 1;
                                }
                                $minPax = 1; // Jika addon tidak memiliki min pax
                            @endphp

                            <div class="flex items-start space-x-3 addon-item" data-id="{{ $addon->id }}" data-price="{{ $addon->price ?? 0 }}">
                                
                                {{-- Checkbox --}}
                                <input type="checkbox" id="addon_{{ $addon->id }}" name="addons[]" value="{{ $addon->id }}" 
                                    class="mt-1 addon-checkbox h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500" {{ $isSelected ? 'checked' : '' }}>
                                
                                {{-- Label --}}
                                <label for="addon_{{ $addon->id }}" class="flex-1 block text-sm font-medium text-gray-700 cursor-pointer">
                                    {{ $addon->addons }} (Rp{{ number_format($addon->price ?? 0, 0, ',', '.') }})
                                </label>
                                
                                {{-- Input Pax untuk Addon --}}
                                <div class="w-32">
                                    <label for="addon_pax_{{ $addon->id }}" class="block text-xs text-gray-500 mb-1">Pax</label>
                                    <input type="number" id="addon_pax_{{ $addon->id }}" name="addon_pax[{{ $addon->id }}]" 
                                        min="{{ $minPax }}" value="{{ $initialPax }}" 
                                        class="pax-input w-full px-2 py-1 border border-gray-300 rounded-lg text-sm bg-white" 
                                        {{ $isSelected ? '' : 'disabled' }} required>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500">No addons available.</p>
                        @endforelse
                    </div>

                    @error('addons')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror

                @if ($addons instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-4">
                    {{ $addons->links() }}
                </div>
                @endif
            </div>
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="pt-6 border-t border-gray-200 flex justify-end space-x-4">
                <a href="{{ route('admin.packages') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-150">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
                    <i class="fas fa-save mr-2"></i> Update Package
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // FUNGSI UTILITY
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }
    
    // FUNGSI UTAMA UNTUK PERHITUNGAN HARGA
    function calculatePrices() {
        let totalRealPrice = 0;

        // 1. Hitung Total dari Products
        document.querySelectorAll('.product-item').forEach(item => {
            const checkbox = item.querySelector('.product-checkbox');
            const paxInput = item.querySelector('.pax-input');
            
            if (checkbox.checked) {
                const price = parseFloat(item.dataset.price) || 0;
                const pax = parseInt(paxInput.value) || 1;
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
                totalRealPrice += price * pax; 
            }
        });

        // 3. Update Harga Real
        const priceRealInput = document.getElementById('price_real');
        const priceRealDisplay = document.getElementById('price_real_display');
        priceRealInput.value = totalRealPrice.toFixed(0);
        priceRealDisplay.value = totalRealPrice.toLocaleString('id-ID', { minimumFractionDigits: 0 });

        // 4. Hitung Harga Publish Berdasarkan Diskon
        const discountPercentageInput = document.getElementById('discount_percentage');
        const pricePublishInput = document.getElementById('price_publish_input');
        const pricePublishNote = document.getElementById('price-publish-note');

        let discountPercentage = parseInt(discountPercentageInput.value) || 0;
        discountPercentage = Math.min(100, Math.max(0, discountPercentage)); // Batasi 0-100
        
        // Pastikan harga real tidak negatif sebelum diskon
        const priceBeforeDiscount = Math.max(0, totalRealPrice); 
        const discountedPrice = priceBeforeDiscount * (1 - discountPercentage / 100);
        
        // 5. Update Harga Publish dan Note
        const autoCalculatedValue = Math.round(discountedPrice);

        if (discountPercentage > 0) {
            pricePublishInput.value = autoCalculatedValue;
            pricePublishNote.innerHTML = `**Harga di atas dihitung otomatis** (${discountPercentage}% diskon). Anda bisa mengubahnya.`;
            pricePublishNote.classList.remove('text-gray-500', 'text-red-500');
            pricePublishNote.classList.add('text-indigo-600');
        } else {
            pricePublishInput.value = autoCalculatedValue; // Sama dengan totalRealPrice
            pricePublishNote.innerHTML = `**Harga di atas sama dengan Harga Real** (Diskon 0%). Anda bisa mengubahnya.`;
            pricePublishNote.classList.remove('text-indigo-600', 'text-red-500');
            pricePublishNote.classList.add('text-gray-500');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const discountPercentageInput = document.getElementById('discount_percentage');
        const pricePublishInput = document.getElementById('price_publish_input');
        const pricePublishNote = document.getElementById('price-publish-note');
        const priceRealInput = document.getElementById('price_real');

        // --- 1. LOGIC CHECKBOX & PAX ---

        // Event listener untuk semua checkbox (Product dan Addon)
        document.querySelectorAll('.product-checkbox, .addon-checkbox').forEach(checkbox => {
            const paxInput = checkbox.closest('.flex').querySelector('.pax-input');
            const initialPaxValue = paxInput.value;

            // Event saat checkbox berubah
            checkbox.addEventListener('change', function() {
                paxInput.disabled = !this.checked;
                if (this.checked) {
                    paxInput.value = initialPaxValue;
                    paxInput.focus();
                } else {
                    paxInput.value = paxInput.min;
                }
                calculatePrices();
            });
        });

        // Event listener untuk semua input Pax
        document.querySelectorAll('.pax-input').forEach(paxInput => {
            paxInput.addEventListener('input', function() {
                let currentValue = parseInt(this.value);
                let minValue = parseInt(this.min);

                if (currentValue < minValue) {
                    this.value = minValue;
                }

                calculatePrices();
            });
        });

        // Event listener untuk Discount Percentage
        discountPercentageInput.addEventListener('input', calculatePrices);
        discountPercentageInput.addEventListener('change', calculatePrices);

        // Listener untuk Harga Publish: Jika user mengubahnya manual
        pricePublishInput.addEventListener('input', function() {
            // Hitung nilai yang seharusnya (otomatis)
            const totalRealPrice = parseFloat(priceRealInput.value) || 0;
            const discountPercentage = parseFloat(discountPercentageInput.value) || 0;
            const autoCalculatedValue = Math.round(totalRealPrice * (1 - discountPercentage / 100));

            // Bandingkan dengan nilai input user
            if (parseFloat(this.value) != autoCalculatedValue) {
                pricePublishNote.innerHTML = 'Anda **memasukkan harga jual secara manual**. Perhitungan diskon otomatis diabaikan.';
                pricePublishNote.classList.remove('text-gray-500', 'text-indigo-600');
                pricePublishNote.classList.add('text-red-500');
            } else {
                 calculatePrices(); // Jika dikembalikan ke harga hasil hitungan
            }
        });



        // --- 4. INDIVIDUAL IMAGE REMOVAL VISUAL FEEDBACK ---
        document.querySelectorAll('.remove_image_checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                toggleImageVisibility(this);
            });
        });

        function toggleImageVisibility(checkbox) {
            const imageContainer = checkbox.closest('.relative');
            if (checkbox.checked) {
                imageContainer.style.opacity = '0.3';
                imageContainer.style.filter = 'grayscale(100%)';
            } else {
                imageContainer.style.opacity = '1';
                imageContainer.style.filter = 'none';
            }
        }

        // --- 5. FORM SUBMISSION CONFIRMATION ---
        document.getElementById('package-form').addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.remove_image_checkbox:checked');
            if (checkedBoxes.length > 0) {
                const count = checkedBoxes.length;
                const confirmMessage = `Are you sure you want to remove ${count} image${count > 1 ? 's' : ''}? This action cannot be undone.`;
                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                    return false;
                }
            }
        });

        // --- 2. INITIAL CALCULATION ---
        calculatePrices();
    });
</script>
@endsection