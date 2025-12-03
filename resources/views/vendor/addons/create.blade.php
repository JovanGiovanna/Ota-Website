@extends('layouts.vendor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Create New Addon</h1>

        <form action="{{ route('vendor.addons.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
            @csrf

            <div class="mb-4">
                <label for="addons" class="block text-sm font-medium text-gray-700 mb-2">Addon Name</label>
                <input type="text" name="addons" id="addons" value="{{ old('addons') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('addons')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if(Auth::guard('super_admin')->check())
            <div class="mb-4">
                <label for="id_vendor" class="block text-sm font-medium text-gray-700 mb-2">Vendor</label>
                <select name="id_vendor" id="id_vendor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Vendor</option>
                    @foreach(\App\Models\Vendor::all() as $vendor)
                        <option value="{{ $vendor->id }}" {{ old('id_vendor') == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }} ({{ $vendor->email }})
                        </option>
                    @endforeach
                </select>
                @error('id_vendor')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif
            
         
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                {{-- 1. Basic Price (Harga Dasar) --}}
                <div>
                    <label for="basic_price" class="block text-sm font-medium text-gray-700 mb-2">Basic Price (Harga Dasar)</label>
                    <input type="number" name="basic_price" id="basic_price" value="{{ old('basic_price') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('basic_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- 2. Tax Rate (%) --}}
                <div>
                    <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-2">Tax Rate (%)</label>
                    <input type="number" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', 0.00) }}" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('tax_rate')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 3. NTA (Net Transaction Amount) - Otomatis dan Readonly --}}
                <div>
                    <label for="nta" class="block text-sm font-medium text-gray-700 mb-2">NTA (Harga Jual Final)</label>
                    <input type="number" name="nta" id="nta" value="{{ old('nta') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-100 cursor-not-allowed" readonly>
                    @error('nta')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            {{-- **BAGIAN DISKON BARU: Fixed vs Percentage** --}}
            <h4 class="text-md font-semibold text-gray-800 mb-3 mt-4">Pilih Tipe Diskon</h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 items-end">
                {{-- Pilihan Tipe Diskon --}}
                <div>
                    <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Diskon</label>
                    <select name="discount_type" id="discount_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tidak Ada Diskon</option>
                        <option value="percentage" {{ old('discount_rate') > 0 ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="fixed" {{ old('discount_fixed') > 0 ? 'selected' : '' }}>Fixed Price (Rp)</option>
                    </select>
                    @error('discount_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 4a. Discount Rate (%) - Input --}}
                <div id="discount_rate_wrapper" class="{{ old('discount_rate') > 0 ? '' : 'hidden' }}">
                    <label for="discount_rate" class="block text-sm font-medium text-gray-700 mb-2">Discount Rate (%)</label>
                    <input type="number" name="discount_rate" id="discount_rate" value="{{ old('discount_rate', 0.00) }}" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('discount_rate')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 4b. Discount Fixed (Rp) - Input --}}
                <div id="discount_fixed_wrapper" class="{{ old('discount_fixed') > 0 ? '' : 'hidden' }}">
                    <label for="discount_fixed" class="block text-sm font-medium text-gray-700 mb-2">Discount Fixed (Rp)</label>
                    <input type="number" name="discount_fixed" id="discount_fixed" value="{{ old('discount_fixed', 0) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('discount_fixed')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- 5. Discount Expires At (Optional) --}}
            <div class="mb-4" id="discount_expires_wrapper" style="{{ old('discount_type') ? '' : 'display: none;' }}">
                <label for="discount_expires_at" class="block text-sm font-medium text-gray-700 mb-2">Discount Expires At (Optional)</label>
                <input type="datetime-local" name="discount_expires_at" id="discount_expires_at" value="{{ old('discount_expires_at') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Leave empty for no expiration</p>
                @error('discount_expires_at')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- 6. Discount Price (Nilai rupiah yang digunakan) - Otomatis & Readonly --}}
            <div class="mb-4">
                <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-2">Potongan Harga (Rp)</label>
                <input type="number" name="discount_price" id="discount_price" value="{{ old('discount_price', 0) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-100 cursor-not-allowed" readonly>
                <p class="text-xs text-gray-500 mt-1">Nilai potongan harga ini akan dikirim ke database bersama Basic Price dan Tax Rate.</p>
                @error('discount_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            

            
            <div class="mb-4">
                <label for="pax" class="block text-sm font-medium text-gray-700 mb-2">Pax / Capacity</label>
                <input type="number" name="pax" id="pax" value="{{ old('pax', 1) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('pax')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="desc" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="desc" id="desc" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('desc') }}</textarea>
                @error('desc')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ old('status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bidang Upload Multiple Images --}}
            <div class="mb-6">
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Addon Images (Multiple)</label>
                <input 
                    type="file" 
                    name="images[]" 
                    id="images" 
                    multiple
                    accept="image/*"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                @error('images')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="text-red-500 text-sm mt-1">Satu atau lebih file gambar tidak valid.</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">File yang diizinkan: jpeg, png, jpg, gif, svg (Max 2MB per file)</p>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('vendor.addons') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Create Addon</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const basicPriceInput = document.getElementById('basic_price');
    const taxRateInput = document.getElementById('tax_rate');
    const ntaInput = document.getElementById('nta');

    // Elemen Diskon
    const discountTypeSelect = document.getElementById('discount_type');
    const discountRateInput = document.getElementById('discount_rate');
    const discountFixedInput = document.getElementById('discount_fixed');
    const discountPriceInput = document.getElementById('discount_price');
    const discountExpiresWrapper = document.getElementById('discount_expires_wrapper');
    const discountRateWrapper = document.getElementById('discount_rate_wrapper');
    const discountFixedWrapper = document.getElementById('discount_fixed_wrapper');

    /**
     * Mengatur tampilan input diskon berdasarkan tipe yang dipilih.
     */
    function toggleDiscountInput() {
        const type = discountTypeSelect.value;

        // Reset semua input diskon
        discountRateInput.value = 0.00;
        discountFixedInput.value = 0.00;

        // Sembunyikan semua wrapper
        discountRateWrapper.classList.add('hidden');
        discountFixedWrapper.classList.add('hidden');
        discountExpiresWrapper.style.display = 'none';

        // Nonaktifkan semua input diskon agar hanya yang aktif yang terkirim
        discountRateInput.setAttribute('disabled', 'disabled');
        discountFixedInput.setAttribute('disabled', 'disabled');

        if (type === 'percentage') {
            discountRateWrapper.classList.remove('hidden');
            discountExpiresWrapper.style.display = 'block';
            discountRateInput.removeAttribute('disabled');
        } else if (type === 'fixed') {
            discountFixedWrapper.classList.remove('hidden');
            discountExpiresWrapper.style.display = 'block';
            discountFixedInput.removeAttribute('disabled');
        }

        calculateNta(); // Hitung ulang NTA setelah ganti tipe
    }

    /**
     * Fungsi untuk menghitung NTA (Harga Final) setelah Diskon dan Pajak.
     * NTA = (Basic Price - Discount Price) * (1 + Tax Rate%)
     */
    function calculateNta() {
        const basicPrice = Math.max(0, parseFloat(basicPriceInput.value) || 0);
        const taxRate = Math.max(0, parseFloat(taxRateInput.value) || 0);
        const discountType = discountTypeSelect.value;

        let discountPrice = 0;

        // 1. Tentukan Discount Price berdasarkan tipe input
        if (discountType === 'percentage') {
            const discountRate = Math.max(0, parseFloat(discountRateInput.value) || 0);
            const validDiscountRate = Math.min(100, discountRate);
            discountPrice = basicPrice * (validDiscountRate / 100);
        } else if (discountType === 'fixed') {
            const fixedDiscount = Math.max(0, parseFloat(discountFixedInput.value) || 0);
            discountPrice = fixedDiscount;
        }

        // Batasi Discount Price agar tidak melebihi Basic Price
        discountPrice = Math.min(discountPrice, basicPrice);

        // Tampilkan Discount Price (nilai rupiah diskon final)
        discountPriceInput.value = discountPrice.toFixed(2);

        // 2. Hitung Harga Setelah Diskon (Net Price sebelum Pajak)
        const netPriceBeforeTax = basicPrice - discountPrice;

        // 3. Hitung NTA (Harga Final dengan Pajak)
        const validTaxRate = Math.min(100, taxRate);
        const multiplier = 1 + (validTaxRate / 100);
        const nta = netPriceBeforeTax * multiplier;

        // Tampilkan NTA
        ntaInput.value = nta.toFixed(2);
    }

    // Panggil fungsi hitung saat ada perubahan pada input
    basicPriceInput.addEventListener('input', calculateNta);
    taxRateInput.addEventListener('input', calculateNta);
    discountRateInput.addEventListener('input', calculateNta);
    discountFixedInput.addEventListener('input', calculateNta);
    discountTypeSelect.addEventListener('change', toggleDiscountInput);

    // Inisialisasi tampilan input dan perhitungan NTA saat halaman dimuat
    if (parseFloat(discountRateInput.value) > 0) {
        discountTypeSelect.value = 'percentage';
    } else if (parseFloat(discountFixedInput.value) > 0) {
        discountTypeSelect.value = 'fixed';
    } else {
        discountTypeSelect.value = '';
    }
    toggleDiscountInput();
});
</script>
@endsection