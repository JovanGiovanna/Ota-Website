@extends('layouts.superadmin')

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
                                            <input type="checkbox" name="remove_images[]" value="{{ $img }}" id="remove_image_{{ $index }}" class="remove_image_checkbox absolute top-1 right-1 w-4 h-4 text-red-600 bg-white border-gray-300 rounded focus:ring-red-500 z-10 opacity-0 cursor-pointer">
                                            <label for="remove_image_{{ $index }}" class="absolute top-1 right-1 text-xs text-white bg-red-500 px-1 py-0.5 rounded cursor-pointer z-20 hover:bg-red-700 transition duration-150">Remove</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="mb-4">
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

            ---

            {{-- Bagian 2: Harga & Periode --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 p-6 bg-emerald-50/50 rounded-lg border border-emerald-100">
                <div class="md:col-span-3">
                    <h2 class="text-2xl font-semibold text-emerald-700 mb-4">Pricing & Publication Period</h2>
                </div>

                {{-- Harga Jual --}}
                <div>
                    <label for="price_publish_display" class="block text-sm font-semibold text-green-700 mb-2">Pax Paid (Rp) <span class="text-red-500">*</span></label>
                    <input type="text" id="price_publish_display" value="{{ number_format(old('price_publish', $package->price_publish), 0, ',', '.') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-800 font-bold" readonly>
                    
                    {{-- Input Hidden untuk dikirim ke backend --}}
                    <input type="hidden" name="price_publish" id="price_publish_input" value="{{ old('price_publish', $package->price_publish) }}">
                    <input type="hidden" name="price_real" id="price_real" value="{{ old('price_real', $package->price_real) }}">
                    <input type="hidden" name="discount_percentage" id="discount_percentage" value="0">
                    
                    <p class="text-xs text-gray-500 mt-1" id="price-publish-note">Harga ini adalah **Accumulated NTA** dari produk dan addon (yang akan dikirim sebagai 'price_publish').</p>
                    @error('price_publish')
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
                <div>
                    <label for="end_publish" class="block text-sm font-semibold text-gray-700 mb-2">End Publish Date (Optional)</label>
                    <input type="date" name="end_publish" id="end_publish" value="{{ old('end_publish', $package->end_publish ? \Carbon\Carbon::parse($package->end_publish)->format('Y-m-d') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 transition duration-150">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika paket ingin tayang tanpa batas waktu.</p>
                    @error('end_publish')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            

            {{-- Bagian 4: Products dan Addons (Langsung tampil semua) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">

                {{-- Pemilihan Products (Loading Semua) --}}
                <div class="space-y-4 border p-4 rounded-lg bg-blue-50/50">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2 text-blue-700">Products Included <span class="text-red-500">*</span></h3>

                    <div id="product-list-container" class="space-y-3 p-2 bg-white rounded-lg shadow-inner border max-h-96 overflow-y-auto min-h-[10rem]">
                        {{-- Konten akan langsung diganti oleh JS saat DOMContentLoaded --}}
                        <p class="text-center text-gray-500 py-8"><i class="fas fa-spinner fa-spin mr-2"></i> Loading Products...</p>

                        {{-- Data products/pax yang sudah dipilih (diparsing oleh JS) --}}
                        <input type="hidden" id="selected-products-data" value="{{ json_encode($package->products_data ?? []) }}">
                        <input type="hidden" id="old-products-ids" value="{{ json_encode(old('products') ?? []) }}">
                        <input type="hidden" id="old-products-pax" value="{{ json_encode(old('product_pax') ?? []) }}">
                    </div>

                    @error('products')
                        <p class="text-red-500 text-sm mt-1">Anda harus memilih setidaknya satu produk.</p>
                    @enderror
                </div>


                {{-- Pemilihan Addons (Loading Semua) --}}
                <div class="space-y-4 border p-4 rounded-lg bg-purple-50/50">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2 text-purple-700">Addons (Optional)</h3>

                    <div id="addon-list-container" class="space-y-3 p-2 bg-white rounded-lg shadow-inner border max-h-96 overflow-y-auto min-h-[10rem]">
                        {{-- Konten akan langsung diganti oleh JS saat DOMContentLoaded --}}
                         <p class="text-center text-gray-500 py-8"><i class="fas fa-spinner fa-spin mr-2"></i> Loading Addons...</p>

                          {{-- Data addons/pax yang sudah dipilih (diparsing oleh JS) --}}
                        <input type="hidden" id="selected-addons-data" value="{{ json_encode($package->addons_data ?? []) }}">
                        <input type="hidden" id="old-addons-ids" value="{{ json_encode(old('addons') ?? []) }}">
                        <input type="hidden" id="old-addons-pax" value="{{ json_encode(old('addon_pax') ?? []) }}">
                    </div>

                    @error('addons')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>


            {{-- Tombol Aksi --}}
            <div class="pt-6 border-t border-gray-200 flex justify-end space-x-4">
                <a href="{{ route('super_admin.packages') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition duration-150">Cancel</a>
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
        }).format(number).replace('IDR', 'Rp');
    }

    // FUNGSI UTAMA UNTUK PERHITUNGAN HARGA (Accumulated NTA)
    function calculatePrices() {
        // Menggunakan NTA Price sebagai basis perhitungan
        let totalNTAPrice = 0; 

        // 1. Hitung Total dari Products
        document.querySelectorAll('#product-list-container .product-item').forEach(item => {
            const checkbox = item.querySelector('.product-checkbox');
            const paxInput = item.querySelector('.pax-input');

            if (checkbox && checkbox.checked) {
                // Harga produk diasumsikan sebagai NTA Price per unit
                const price = parseFloat(item.dataset.price) || 0; 
                const pax = parseInt(paxInput.value) || 1;
                totalNTAPrice += price * pax;
            }
        });

        // 2. Hitung Total dari Addons
        document.querySelectorAll('#addon-list-container .addon-item').forEach(item => {
            const checkbox = item.querySelector('.addon-checkbox');
            const paxInput = item.querySelector('.pax-input');

            if (checkbox && checkbox.checked) {
                // Harga addon diasumsikan sebagai NTA Price per unit
                const price = parseFloat(item.dataset.price) || 0; 
                const pax = parseInt(paxInput.value) || 1;
                totalNTAPrice += price * pax;
            }
        });

        // 3. Update Harga Real (price_real) dan Harga Publish (price_publish)
        const priceRealInput = document.getElementById('price_real');
        const pricePublishInput = document.getElementById('price_publish_input');
        const pricePublishDisplay = document.getElementById('price_publish_display');

        priceRealInput.value = totalNTAPrice.toFixed(0);
        pricePublishInput.value = totalNTAPrice.toFixed(0);
        pricePublishDisplay.value = totalNTAPrice.toLocaleString('id-ID', { minimumFractionDigits: 0 });
    }


    // FUNGSI UNTUK MEMUAT PRODUCT/ADDON (SUDAH DIUBAH MENJADI loadItems)
    function loadItems(type) {
        const container = document.getElementById(`${type}-list-container`);
        const isProduct = (type === 'product');
        const listName = isProduct ? 'Products' : 'Addons';
        const checkboxClass = isProduct ? 'product-checkbox' : 'addon-checkbox';
        const nameAttribute = isProduct ? 'products[]' : 'addons[]';
        const paxNameAttribute = isProduct ? 'product_pax' : 'addon_pax';
        const itemClass = isProduct ? 'product-item' : 'addon-item';

        // Tampilkan loading
        container.innerHTML = `<p class="text-center text-gray-500 py-8">
            <i class="fas fa-spinner fa-spin mr-2"></i> Loading all ${listName}...
        </p>`;
        calculatePrices(); // Reset harga saat loading

        // URL untuk mengambil SEMUA item
        const url = `/admin/api/${type}s/all`; 
        
        // Ambil data yang sudah tersimpan atau dari old input
        const selectedData = JSON.parse(document.getElementById(`selected-${type}s-data`).value);
        const oldIds = JSON.parse(document.getElementById(`old-${type}s-ids`).value);
        const oldPax = JSON.parse(document.getElementById(`old-${type}s-pax`).value);
        
        // Tentukan data yang akan digunakan (prioritas: old input > data tersimpan)
        const activeIds = oldIds.length > 0 ? oldIds.map(id => parseInt(id)) : selectedData.map(item => item.id);

        
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(items => {
                if (items.length === 0) {
                    container.innerHTML = `<p class="text-center text-gray-500 py-8">No ${listName.toLowerCase()} available.</p>`;
                    calculatePrices();
                    return;
                }

                let htmlContent = '';
                items.forEach(item => {
                    const itemId = item.id;
                    
                    let isChecked = activeIds.includes(itemId);
                    let initialPax = 1;

                    // Tentukan nilai PAX yang benar
                    if (oldPax[itemId]) {
                        initialPax = oldPax[itemId]; // Dari old input
                    } else if (isChecked) {
                        // Dari data tersimpan
                        const savedItem = selectedData.find(d => d.id === itemId);
                        if (savedItem && savedItem.pax) {
                            initialPax = savedItem.pax;
                        }
                    }

                    const itemPrice = item.price || 0;
                    const itemName = isProduct ? item.name : item.addons;
                    
                    // HTML untuk setiap item
                    htmlContent += `
                        <div class="flex items-start space-x-3 ${itemClass} border-b border-gray-100 pb-2 mb-2 last:border-b-0 last:pb-0" data-id="${itemId}" data-price="${itemPrice}">
                            
                            {{-- Checkbox --}}
                            <input type="checkbox" id="${type}_${itemId}" name="${nameAttribute}" value="${itemId}"
                                class="mt-1 ${checkboxClass} h-4 w-4 ${isProduct ? 'text-blue-600 focus:ring-blue-500' : 'text-purple-600 focus:ring-purple-500'} border-gray-300 rounded" 
                                ${isChecked ? 'checked' : ''}>
                            
                            {{-- Label dan Harga (Asumsi Harga adalah NTA per unit) --}}
                            <label for="${type}_${itemId}" class="flex-1 block text-sm font-medium text-gray-700 cursor-pointer">
                                ${itemName} (NTA: ${formatRupiah(itemPrice)})
                            </label>
                            
                            {{-- Input Pax Paid --}}
                            <div class="w-32">
                                <label for="${type}_pax_${itemId}" class="block text-xs font-semibold text-gray-600 mb-1">Pax Paid</label>
                                <input type="number" id="${type}_pax_${itemId}" name="${paxNameAttribute}[${itemId}]" 
                                    min="1" value="${initialPax}" 
                                    class="pax-input w-full px-2 py-1 border border-gray-300 rounded-lg text-sm bg-white" 
                                    ${isChecked ? '' : 'disabled'} required>
                            </div>
                        </div>
                    `;
                });

                container.innerHTML = htmlContent;
                attachEventListenersToNewItems();
                calculatePrices(); // Hitung harga setelah item dimuat
            })
            .catch(error => {
                console.error(`Error loading ${listName}:`, error);
                container.innerHTML = `<p class="text-center text-red-500 py-8">Failed to load ${listName}. Please check the console and server logs.</p>`;
                calculatePrices();
            });
    }

    // FUNGSI UNTUK MENGHUBUNGKAN EVENT LISTENER KE ITEM BARU
    function attachEventListenersToNewItems() {
        document.querySelectorAll('.product-checkbox, .addon-checkbox').forEach(checkbox => {
            const paxInput = checkbox.closest('.flex').querySelector('.pax-input');

            paxInput.disabled = !checkbox.checked;

            checkbox.addEventListener('change', function() {
                paxInput.disabled = !this.checked;
                if (this.checked) {
                    paxInput.value = paxInput.min || 1; 
                    paxInput.focus();
                } else {
                    paxInput.value = paxInput.min || 1;
                }
                calculatePrices();
            });
        });

        document.querySelectorAll('.pax-input').forEach(paxInput => {
            paxInput.addEventListener('input', function() {
                let currentValue = parseInt(this.value);
                let minValue = parseInt(this.min) || 1;

                if (currentValue < minValue || isNaN(currentValue)) {
                    this.value = minValue;
                }

                calculatePrices();
            });
        });
    }


    document.addEventListener('DOMContentLoaded', function () {
        
        // --- 1. LOGIC ITEM LOADING (Langsung Tampil Semua) ---
        // Panggil fungsi loadItems untuk Product dan Addon
        loadItems('product'); 
        loadItems('addon'); 

        // Inisialisasi perhitungan harga saat DOM siap
        // Event listener untuk perhitungan harga tidak lagi terikat pada pemilihan Vendor yang sudah dihapus
        calculatePrices(); 


        // --- 2. LOGIC IMAGE REMOVAL VISUAL FEEDBACK ---
        document.querySelectorAll('.remove_image_checkbox').forEach(checkbox => {
            toggleImageVisibility(checkbox); 
            checkbox.addEventListener('change', function() {
                toggleImageVisibility(this);
            });
        });

        function toggleImageVisibility(checkbox) {
            const imageContainer = checkbox.closest('.relative');
            const label = imageContainer.querySelector('label[for="' + checkbox.id + '"]');
            if (checkbox.checked) {
                imageContainer.style.opacity = '0.4';
                imageContainer.style.filter = 'grayscale(100%)';
                if(label) label.textContent = 'Undo';
                label.classList.remove('bg-red-500', 'hover:bg-red-700');
                label.classList.add('bg-gray-500', 'hover:bg-gray-700');
            } else {
                imageContainer.style.opacity = '1';
                imageContainer.style.filter = 'none';
                if(label) label.textContent = 'Remove';
                label.classList.remove('bg-gray-500', 'hover:bg-gray-700');
                label.classList.add('bg-red-500', 'hover:bg-red-700');
            }
        }
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

        // Pastikan harga dihitung ulang setelah semua listener terpasang
        calculatePrices();
    });
</script>
@endsection