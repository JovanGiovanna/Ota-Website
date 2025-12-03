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

            {{-- SECTION 5: PRICE CALCULATION & PUBLISH FIELDS (Diskon Dihapus) --}}
            <div class="space-y-6 pt-4">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Pricing & Publishing</h2>

                {{-- NTA (Nett Total Package Price) - Display --}}
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Nett Total Package Price (NTA) - Total Harga Pokok</label>
                    <div class="p-3 bg-green-100 border border-green-400 rounded-lg">
                        <span class="text-lg font-bold text-green-700" id="nta_display">Rp{{ number_format($package->nta, 0, ',', '.') }}</span>
                        {{-- Input hidden NTA --}}
                        <input type="hidden" name="nta" id="nta" value="{{ old('nta', $package->nta) }}">
                    </div>
                </div>

                {{-- Total Price (Gross) --}}
                <div class="space-y-2">
                    <label for="pax_paid_input" class="block text-sm font-medium text-gray-700">Total Price (Gross) - Total Harga Paket (NTA + Upsale - Diskon)</label>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        {{-- Input field disesuaikan dengan 'pax_paid_input' --}}
                        <input type="number" name="pax_paid_input" id="pax_paid_input" value="{{ old('pax_paid_input', $package->pax_paid) }}" step="1" min="0" placeholder="0" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('pax_paid_input') border-red-500 @enderror">
                    </div>
                    <p class="text-xs text-gray-500">Nilai ini menyimpan total harga paket (NTA + Upsale - Diskon). Untuk mendapatkan harga per orang, bagi nilai ini dengan total Pax.</p>
                    @error('pax_paid_input')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tax Rate --}}
                <div class="space-y-2">
                    <label for="tax_rate" class="block text-sm font-medium text-gray-700">Tax Rate (%)</label>
                    <div class="relative rounded-lg shadow-sm">
                        <input type="number" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', $package->tax_rate ?? 0) }}" step="0.01" min="0" max="100" placeholder="0.00" class="w-full pr-8 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('tax_rate') border-red-500 @enderror">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">%</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Persentase pajak yang akan diterapkan pada harga paket (0-100%).</p>
                    @error('tax_rate')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tax Amount - Display --}}
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Tax Amount</label>
                    <div class="p-3 bg-orange-100 border border-orange-400 rounded-lg">
                        <span class="text-lg font-bold text-orange-700" id="tax_amount_display">Rp{{ number_format(($package->nta * ($package->tax_rate ?? 0) / 100), 0, ',', '.') }}</span>
                        {{-- Input hidden tax_amount --}}
                        <input type="hidden" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', $package->nta * ($package->tax_rate ?? 0) / 100) }}">
                    </div>
                </div>

                {{-- Total Price - Display --}}
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Total Price (NTA + Tax)</label>
                    <div class="p-3 bg-green-100 border border-green-400 rounded-lg">
                        <span class="text-lg font-bold text-green-700" id="total_price_display">Rp{{ number_format($package->nta + ($package->nta * ($package->tax_rate ?? 0) / 100), 0, ',', '.') }}</span>
                        {{-- Input hidden total_price --}}
                        <input type="hidden" name="total_price" id="total_price" value="{{ old('total_price', $package->nta + ($package->nta * ($package->tax_rate ?? 0) / 100)) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="start_publish" class="block text-sm font-medium text-gray-700">Start Publish Date</label>
                        <input type="date" name="start_publish" id="start_publish" value="{{ old('start_publish', \Carbon\Carbon::parse($package->start_publish)->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('start_publish') border-red-500 @enderror">
                        @error('start_publish')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="end_publish" class="block text-sm font-medium text-gray-700">End Publish Date (Optional)</label>
                        <input type="date" name="end_publish" id="end_publish" value="{{ old('end_publish', $package->end_publish ? \Carbon\Carbon::parse($package->end_publish)->format('Y-m-d') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('end_publish') border-red-500 @enderror">
                        @error('end_publish')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


            </div>



            {{-- SECTION 2: PRODUCTS (Multi-Select) --}}
            <div class="space-y-4 border p-4 rounded-lg bg-gray-50">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Select Products (Multi)</h2>

                <div class="space-y-3 p-2 bg-white rounded-lg shadow-inner border max-h-96 overflow-y-auto">
                    @forelse ($products as $product)
                        @php
                            $isProductChecked = in_array($product->id, $selectedProductIds);
                            $paxValue = old('product_pax.' . $product->id, ($isProductChecked ? collect($selectedProductsData)->firstWhere('id', $product->id)['pax'] ?? 1 : 1));
                            // Ambil NTA atau fallback ke basic_price
                            $productNTA = $product->nta ?? $product->basic_price ?? 0;
                        @endphp

                        <div class="flex items-start space-x-3 product-item" data-nta="{{ $productNTA }}" data-pax-min="{{ $product->pax ?? 1 }}">
                            <input type="checkbox" id="product_{{ $product->id }}" name="products[]" value="{{ $product->id }}" class="mt-1 product-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ $isProductChecked ? 'checked' : '' }}>
                            <label for="product_{{ $product->id }}" class="flex-1 block text-sm font-medium text-gray-700 cursor-pointer">
                                {{ $product->name }} (<span class="font-bold text-green-700">NTA: Rp{{ number_format($productNTA, 0, ',', '.') }}</span> / Pax: {{ $product->pax ?? 1 }})
                            </label>

                            {{-- Input Pax untuk Produk --}}
                            <div class="w-32">
                                <label for="product_pax_{{ $product->id }}" class="block text-xs text-gray-500 mb-1">Pax</label>
                                <input type="number" id="product_pax_{{ $product->id }}" name="product_pax[{{ $product->id }}]" min="{{ $product->pax ?? 1 }}" value="{{ $paxValue }}" class="pax-input w-full px-2 py-1 border border-gray-300 rounded-lg text-sm bg-white" required {{ $isProductChecked ? '' : 'disabled' }}>
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

            ---

            {{-- SECTION 3: ADDONS (Multi-Select) --}}
            <div class="space-y-4 border p-4 rounded-lg bg-gray-50">
                <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2">Select Addons (Multi)</h2>

                <div class="space-y-3 p-2 bg-white rounded-lg shadow-inner border max-h-96 overflow-y-auto">
                    @forelse ($addons as $addon)
                        @php
                            $isAddonChecked = in_array($addon->id, $selectedAddonIds);
                            $paxValue = old('addon_pax.' . $addon->id, ($isAddonChecked ? collect($selectedAddonsData)->firstWhere('id', $addon->id)['pax'] ?? 1 : 1));
                            // Ambil NTA atau fallback ke basic_price
                            $addonNTA = $addon->nta ?? $addon->basic_price ?? 0;
                        @endphp

                        <div class="flex items-start space-x-3 addon-item" data-nta="{{ $addonNTA }}" data-pax-min="{{ $addon->pax ?? 1 }}">
                            <input type="checkbox" id="addon_{{ $addon->id }}" name="addons[]" value="{{ $addon->id }}" class="mt-1 addon-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ $isAddonChecked ? 'checked' : '' }}>
                            <label for="addon_{{ $addon->id }}" class="flex-1 block text-sm font-medium text-gray-700 cursor-pointer">
                                {{ $addon->addons }} (<span class="font-bold text-green-700">NTA: Rp{{ number_format($addonNTA, 0, ',', '.') }}</span> / Pax: {{ $addon->pax ?? 1 }})
                            </label>

                            {{-- Input Pax untuk Addon --}}
                            <div class="w-32">
                                <label for="addon_pax_{{ $addon->id }}" class="block text-xs text-gray-500 mb-1">Pax</label>
                                <input type="number" id="addon_pax_{{ $addon->id }}" name="addon_pax[{{ $addon->id }}]" min="{{ $addon->pax ?? 1 }}" value="{{ $paxValue }}" class="pax-input w-full px-2 py-1 border border-gray-300 rounded-lg text-sm bg-white" required {{ $isAddonChecked ? '' : 'disabled' }}>
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

    // Fungsi utama untuk menghitung total NTA dan Harga Jual Per Pax
    function calculatePrice() {
        let totalNTA = 0;
        let totalPaxCount = 0;

        // 1. Hitung Total NTA dan Total Pax dari Products
        document.querySelectorAll('.product-item').forEach(item => {
            const checkbox = item.querySelector('.product-checkbox');
            const paxInput = item.querySelector('.pax-input');

            if (checkbox.checked) {
                const nta = parseFloat(item.dataset.nta) || 0;
                const pax = parseInt(paxInput.value) || 1;

                totalNTA += nta * pax;
                totalPaxCount += pax; // PENTING: Pax produk dihitung sebagai Pax paket
            }
        });

        // 2. Hitung Total NTA dari Addons (Addons TIDAK menambah Total Pax Paket)
        document.querySelectorAll('.addon-item').forEach(item => {
            const checkbox = item.querySelector('.addon-checkbox');
            const paxInput = item.querySelector('.pax-input');

            if (checkbox.checked) {
                const nta = parseFloat(item.dataset.nta) || 0;
                const pax = parseInt(paxInput.value) || 1;

                totalNTA += nta * pax;
            }
        });

        // Update display dan hidden field NTA
        document.getElementById('nta_display').textContent = formatRupiah(totalNTA);
        document.getElementById('nta').value = totalNTA;

        // 3. Hitung Tax Amount dan Total Price
        const taxRateInput = document.getElementById('tax_rate');
        const taxRate = parseFloat(taxRateInput.value) || 0;
        const taxAmount = totalNTA * taxRate / 100;
        const totalPrice = totalNTA + taxAmount;

        // Update tax_amount display dan hidden
        document.getElementById('tax_amount_display').textContent = formatRupiah(taxAmount);
        document.getElementById('tax_amount').value = taxAmount;

        // Update total_price display dan hidden
        document.getElementById('total_price_display').textContent = formatRupiah(totalPrice);
        document.getElementById('total_price').value = totalPrice;

        // 4. Hitung Harga Jual Per Pax (Pax Paid)
        const paxPaidInput = document.getElementById('pax_paid_input');
        const currentPaxPaidValue = parseInt(paxPaidInput.value) || 0;

        const totalPricePublish = totalNTA;

        let paxPaidCalculated = 0;
        if (totalPaxCount > 0) {
            // Formula: Harga Per Pax = Total NTA / Total Pax Produk
            paxPaidCalculated = totalPricePublish / totalPaxCount;
        }

        // Update Pax Paid Input HANYA JIKA nilainya 0 atau belum diubah manual (reset)
        // Kita menggunakan `data-manual-edit` untuk melacak perubahan manual
        const isManualEdit = paxPaidInput.dataset.manualEdit === 'true';

        if (!isManualEdit || currentPaxPaidValue === 0) {
            paxPaidInput.value = Math.round(paxPaidCalculated);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const namePackageInput = document.getElementById('name_package');
        const paxPaidInput = document.getElementById('pax_paid_input');

        // --- Inisialisasi data-manual-edit ---
        paxPaidInput.dataset.manualEdit = 'false';

        // --- 2. CHECKBOX AND PAX LOGIC ---
        document.querySelectorAll('.product-checkbox, .addon-checkbox').forEach(checkbox => {
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

                // Reset manual edit flag dan nilai pax paid untuk memaksa perhitungan ulang
                paxPaidInput.dataset.manualEdit = 'false';
                paxPaidInput.value = 0;

                calculatePrice();
            });
        });

        document.querySelectorAll('.pax-input').forEach(paxInput => {
            paxInput.addEventListener('input', function() {
                let currentValue = parseInt(this.value);
                let minValue = parseInt(this.min);

                if (currentValue < minValue) {
                    this.value = minValue;
                }

                // Reset manual edit flag dan nilai pax paid untuk memaksa perhitungan ulang
                paxPaidInput.dataset.manualEdit = 'false';
                paxPaidInput.value = 0;

                calculatePrice();
            });
            paxInput.addEventListener('change', function() {
                 paxPaidInput.dataset.manualEdit = 'false';
                 paxPaidInput.value = 0;
            });
        });

        // --- 3. LOGIKA PAX PAID INPUT (Override Otomatis) ---
        // Jika user mengetik atau mengubah, kita set flag 'manual-edit' menjadi true
        paxPaidInput.addEventListener('input', function() {
            if (this.value !== "") {
                this.dataset.manualEdit = 'true';
            } else {
                this.dataset.manualEdit = 'false';
            }
        });
        // Jika user blur/keluar dari input dan nilainya kosong, hitung ulang otomatis
        paxPaidInput.addEventListener('blur', function() {
            if (this.value === "" || parseInt(this.value) === 0) {
                 this.dataset.manualEdit = 'false';
                 calculatePrice(); // Hitung ulang untuk mengisi nilai otomatis
            }
        });

        // --- 4. TAX RATE LOGIC ---
        const taxRateInput = document.getElementById('tax_rate');
        taxRateInput.addEventListener('input', function() {
            calculatePrice(); // Recalculate when tax rate changes
        });

        // Hitung harga saat halaman dimuat
        calculatePrice();
    });

</script>

@endpush
@endsection
