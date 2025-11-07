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
                        <label for="image" class="block text-sm font-semibold text-gray-700 mb-2">Package Image</label>
                        @if($package->image)
                            <div class="mb-2 p-2 border border-gray-300 rounded-lg bg-white shadow-sm">
                                <p class="text-xs text-gray-500 mb-1">Current Image:</p>
                                <img src="{{ asset('storage/' . $package->image) }}" alt="Current Image" class="w-full h-40 object-cover rounded-md border border-gray-200">
                            </div>
                        @endif
                        <input type="file" name="image" id="image" accept="image/*" class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition duration-150">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image. Max 2MB.</p>
                        @error('image')
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 p-6 bg-emerald-50/50 rounded-lg border border-emerald-100">
                <div class="md:col-span-3">
                    <h2 class="text-2xl font-semibold text-emerald-700 mb-4">Pricing & Publication Period</h2>
                </div>
                
                {{-- Harga Publish (Harga Jual) --}}
                <div>
                    <label for="price_publish" class="block text-sm font-semibold text-green-700 mb-2">Publish Price (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="price_publish" id="price_publish" value="{{ old('price_publish', $package->price_publish) }}" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition duration-150" required>
                    @error('price_publish')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Harga Real (Akumulasi) --}}
                <div>
                    <label for="price_real_display" class="block text-sm font-semibold text-gray-700 mb-2">Accumulated Price (Rp)</label>
                    <input type="text" id="price_real_display" value="{{ number_format(old('price_real', $package->price_real), 0, ',', '.') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 font-medium" readonly>
                    {{-- Input Hidden untuk dikirim ke backend --}}
                    <input type="hidden" name="price_real" id="price_real" value="{{ old('price_real', $package->price_real) }}">
                    @error('price_real')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
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
                <div class="md:col-span-3">
                    <label for="end_publish" class="block text-sm font-semibold text-gray-700 mb-2">End Publish Date (Optional)</label>
                    <input type="date" name="end_publish" id="end_publish" value="{{ old('end_publish', $package->end_publish ? \Carbon\Carbon::parse($package->end_publish)->format('Y-m-d') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition duration-150">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika paket ingin tayang tanpa batas waktu.</p>
                    @error('end_publish')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Bagian 3: Products dan Addons --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                
                {{-- Pemilihan Products --}}
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Products Included <span class="text-red-500">*</span></h3>
                    <label for="products" class="block text-sm font-medium text-gray-700 mb-2">Select Products (Hold CTRL/CMD to select multiple)</label>
                    
                    {{-- Select Box Products --}}
                    <select name="products[]" id="products" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-48" multiple required>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" 
                                {{ in_array($product->id, old('products', $selectedProductIds ?? [])) ? 'selected' : '' }}>
                                {{ $product->name }} (Rp{{ number_format($product->price) }})
                            </option>
                        @endforeach
                    </select>
                    @error('products')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    
                    {{-- Container untuk PAX Products --}}
                    <div id="product-pax-container" class="mt-4 p-4 rounded-lg bg-blue-50 border border-blue-200 shadow-inner">
                        <h4 class="text-md font-medium text-blue-700 mb-3">Product Quantity (PAX)</h4>
                        {{-- PHP Loop untuk data awal --}}
                        @foreach($selectedProductsData ?? [] as $selectedProduct)
                            <div class="flex items-center space-x-2 mb-2 product-pax-input" data-product-id="{{ $selectedProduct['id'] }}">
                                <label class="w-3/5 text-sm text-gray-700 truncate" title="{{ $selectedProduct['name'] }}">{{ $selectedProduct['name'] }} (Rp{{ number_format($selectedProduct['price']) }}):</label>
                                <input type="number" name="product_pax[{{ $selectedProduct['id'] }}]" 
                                    value="{{ old('product_pax.' . $selectedProduct['id'], $selectedProduct['pax']) }}" 
                                    min="1" class="w-2/5 px-2 py-1 border border-gray-300 rounded-md text-sm pax-input focus:ring-blue-500" required>
                            </div>
                        @endforeach
                        {{-- Pesan default jika belum ada yang dipilih --}}
                        <p id="no-product-selected" class="{{ count($selectedProductsData ?? []) > 0 ? 'hidden' : '' }} text-sm text-gray-500 italic">Select product(s) above to set the quantity.</p>
                    </div>
                    @error('product_pax')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pemilihan Addons --}}
                <div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Addons (Optional)</h3>
                    <label for="addons" class="block text-sm font-medium text-gray-700 mb-2">Select Addons (Hold CTRL/CMD to select multiple)</label>
                    
                    {{-- Select Box Addons --}}
                    <select name="addons[]" id="addons" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 h-48" multiple>
                        @foreach($addons as $addon)
                            <option value="{{ $addon->id }}" data-price="{{ $addon->price }}" 
                                {{ in_array($addon->id, old('addons', $selectedAddonIds ?? [])) ? 'selected' : '' }}>
                                {{ $addon->addons }} (Rp{{ number_format($addon->price) }})
                            </option>
                        @endforeach
                    </select>
                    @error('addons')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    
                    {{-- Container untuk PAX Addons --}}
                    <div id="addon-pax-container" class="mt-4 p-4 rounded-lg bg-purple-50 border border-purple-200 shadow-inner">
                        <h4 class="text-md font-medium text-purple-700 mb-3">Addon Quantity (PAX)</h4>
                        {{-- PHP Loop untuk data awal --}}
                        @foreach($selectedAddonsData ?? [] as $selectedAddon)
                            <div class="flex items-center space-x-2 mb-2 addon-pax-input" data-addon-id="{{ $selectedAddon['id'] }}">
                                <label class="w-3/5 text-sm text-gray-700 truncate" title="{{ $selectedAddon['name'] }}">{{ $selectedAddon['name'] }} (Rp{{ number_format($selectedAddon['price']) }}):</label>
                                <input type="number" name="addon_pax[{{ $selectedAddon['id'] }}]" 
                                    value="{{ old('addon_pax.' . $selectedAddon['id'], $selectedAddon['pax']) }}" 
                                    min="1" class="w-2/5 px-2 py-1 border border-gray-300 rounded-md text-sm pax-input focus:ring-purple-500" required>
                            </div>
                        @endforeach
                        {{-- Pesan default jika belum ada yang dipilih --}}
                        <p id="no-addon-selected" class="{{ count($selectedAddonsData ?? []) > 0 ? 'hidden' : '' }} text-sm text-gray-500 italic">Select addon(s) above to set the quantity.</p>
                    </div>
                    @error('addon_pax')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
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
    document.addEventListener('DOMContentLoaded', function () {
        const productSelect = document.getElementById('products');
        const addonSelect = document.getElementById('addons');
        const productPaxContainer = document.getElementById('product-pax-container');
        const addonPaxContainer = document.getElementById('addon-pax-container');
        const priceRealInput = document.getElementById('price_real');
        const priceRealDisplay = document.getElementById('price_real_display');
        const noProductSelected = document.getElementById('no-product-selected');
        const noAddonSelected = document.getElementById('no-addon-selected');

        // Fungsi untuk mengupdate input PAX dan menghitung harga real
        function updatePaxInputsAndPrice(selectElement, paxContainer, isProduct = true) {
            const selectedOptions = Array.from(selectElement.selectedOptions);
            let totalRealPrice = 0;
            const currentPaxInputs = {};
            
            // Simpan nilai PAX yang sudah ada
            // Ambil dari input yang sudah ada saat ini (untuk mempertahankan nilai saat change)
            paxContainer.querySelectorAll('.pax-input').forEach(input => {
                const itemId = input.name.match(/\[(.*?)\]/)[1];
                currentPaxInputs[itemId] = input.value;
            });

            // Kosongkan container dan siapkan ulang judul
            paxContainer.innerHTML = `<h4 class="text-md font-medium text-${isProduct ? 'blue' : 'purple'}-700 mb-3">${isProduct ? 'Product' : 'Addon'} Quantity (PAX)</h4>`;
            
            if (selectedOptions.length === 0) {
                if (isProduct) {
                    paxContainer.appendChild(noProductSelected);
                    noProductSelected.classList.remove('hidden');
                } else {
                    paxContainer.appendChild(noAddonSelected);
                    noAddonSelected.classList.remove('hidden');
                }
                return 0; 
            }

            // Tambahkan input PAX untuk item yang dipilih
            selectedOptions.forEach(option => {
                const itemId = option.value;
                const itemName = option.text.split(' (Rp')[0];
                const itemPrice = parseFloat(option.getAttribute('data-price'));
                // Gunakan nilai yang sudah ada atau default 1
                const paxValue = currentPaxInputs[itemId] || 1; 

                // Buat elemen input PAX
                const div = document.createElement('div');
                div.className = `flex items-center space-x-2 mb-2 ${isProduct ? 'product-pax-input' : 'addon-pax-input'}`;
                div.setAttribute(`data-${isProduct ? 'product' : 'addon'}-id`, itemId);
                div.innerHTML = `
                    <label class="w-3/5 text-sm text-gray-700 truncate" title="${itemName}">${itemName} (Rp${itemPrice.toLocaleString('id-ID')}):</label>
                    <input type="number" name="${isProduct ? 'product_pax' : 'addon_pax'}[${itemId}]" 
                        value="${paxValue}" 
                        min="1" class="w-2/5 px-2 py-1 border border-gray-300 rounded-md text-sm pax-input focus:ring-${isProduct ? 'blue' : 'purple'}-500" required>
                `;
                paxContainer.appendChild(div);

                // Hitung subtotal
                totalRealPrice += itemPrice * parseInt(paxValue);
            });
            
            if (isProduct) {
                noProductSelected.classList.add('hidden');
            } else {
                noAddonSelected.classList.add('hidden');
            }

            return totalRealPrice;
        }

        // Fungsi untuk menghitung total harga real
        function calculateTotalRealPrice() {
            // Hitung harga real produk
            const productPrice = Array.from(productPaxContainer.querySelectorAll('.pax-input')).reduce((total, input) => {
                const itemId = input.name.match(/\[(.*?)\]/)[1];
                const option = productSelect.querySelector(`option[value="${itemId}"]`);
                if (option) {
                    const price = parseFloat(option.getAttribute('data-price'));
                    const pax = parseInt(input.value) || 0;
                    return total + (price * pax);
                }
                return total;
            }, 0);

            // Hitung harga real addon
            const addonPrice = Array.from(addonPaxContainer.querySelectorAll('.pax-input')).reduce((total, input) => {
                const itemId = input.name.match(/\[(.*?)\]/)[1];
                const option = addonSelect.querySelector(`option[value="${itemId}"]`);
                if (option) {
                    const price = parseFloat(option.getAttribute('data-price'));
                    const pax = parseInt(input.value) || 0;
                    return total + (price * pax);
                }
                return total;
            }, 0);

            const total = productPrice + addonPrice;

            // Update field harga real
            priceRealInput.value = total.toFixed(2); // Simpan dengan 2 desimal
            priceRealDisplay.value = total.toLocaleString('id-ID', { minimumFractionDigits: 0 }); // Tampilkan dalam format mata uang tanpa desimal
        }

        // --- Inisialisasi ---
        // Panggil fungsi untuk mengisi ulang input PAX dan menghitung harga saat load
        // Ini mengatasi masalah ketika data awal hilang setelah ada perubahan select box
        // Kita perlu menjalankan ini dua kali: sekali untuk mengisi ulang PAX container berdasarkan data PHP, 
        // dan sekali lagi setelah event listener ditambahkan.

        // Inisialisasi data PAX berdasarkan data awal dari PHP (sudah ada di HTML)
        calculateTotalRealPrice(); 

        // Event listener untuk perubahan Products
        productSelect.addEventListener('change', function() {
            updatePaxInputsAndPrice(this, productPaxContainer, true);
            calculateTotalRealPrice();
        });

        // Event listener untuk perubahan Addons
        addonSelect.addEventListener('change', function() {
            updatePaxInputsAndPrice(this, addonPaxContainer, false);
            calculateTotalRealPrice();
        });

        // Event listener untuk perubahan nilai PAX (menggunakan event delegation)
        productPaxContainer.addEventListener('change', calculateTotalRealPrice);
        addonPaxContainer.addEventListener('change', calculateTotalRealPrice);
        productPaxContainer.addEventListener('input', calculateTotalRealPrice);
        addonPaxContainer.addEventListener('input', calculateTotalRealPrice);

    });
</script>
@endsection