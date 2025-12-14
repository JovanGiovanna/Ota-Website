@extends('layouts.vendor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Create New Product</h1>

        <form action="{{ route('vendor.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
            @csrf


            {{-- ... (Product Name dan Category tetap sama) ... --}}
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="id_category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="id_category" id="id_category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('id_category') == $category->id ? 'selected' : '' }}>{{ $category->categories }}</option>
                    @endforeach
                </select>
                @error('id_category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            
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
                
                {{-- 3. NTA (Net Transaction Amount) - Otomatis & Readonly --}}
                <div>
                    <label for="nta" class="block text-sm font-medium text-gray-700 mb-2">NTA (Harga Jual Final)</label>
                    <input type="number" name="nta" id="nta" value="{{ old('nta') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-100 cursor-not-allowed" readonly>
                    @error('nta')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Location and Phone Fields --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <input type="text" name="location" id="location" value="{{ old('location') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('location')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Address (Alamat Lengkap) --}}
            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address (Alamat Lengkap)</label>
                <textarea name="address" id="address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Masukkan alamat lengkap lokasi produk (jalan, nomor, kecamatan/kabupaten, provinsi).</p>
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- **BAGIAN DISKON BARU: Fixed vs Percentage** --}}
            <h4 class="text-md font-semibold text-gray-800 mb-3 mt-4">Pilih Tipe Diskon</h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 items-end">
                {{-- Pilihan Tipe Diskon --}}
                <div>
                    <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Diskon</label>
                    <select name="discount_type" id="discount_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Tidak Ada Diskon</option>
                        <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Price (Rp)</option>
                    </select>
                    @error('discount_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            {{-- 4a. Discount Value (%) - Input --}}
                <div id="discount_value_wrapper" class="{{ old('discount_type') == 'percentage' ? '' : 'hidden' }}">
                    <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-2">Discount Value (%)</label>
                    <input type="number" name="discount_value" id="discount_value" value="{{ old('discount_value', 0.00) }}" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('discount_value')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 4b. Discount Value (Rp) - Input --}}
                <div id="discount_fixed_wrapper" class="{{ old('discount_type') == 'fixed' ? '' : 'hidden' }}">
                    <label for="discount_value_fixed" class="block text-sm font-medium text-gray-700 mb-2">Discount Value (Rp)</label>
                    <input type="number" name="discount_value" id="discount_value_fixed" value="{{ old('discount_value', 0) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('discount_value')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- 5. Discount Expires At --}}
            <div class="mb-4" id="discount_expires_wrapper" style="{{ old('discount_type') ? '' : 'display: none;' }}">
                <label for="discount_expires_at" class="block text-sm font-medium text-gray-700 mb-2">Discount Expires At (Optional)</label>
                <input type="datetime-local" name="discount_expires_at" id="discount_expires_at" value="{{ old('discount_expires_at') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Leave empty for no expiration</p>
                @error('discount_expires_at')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- ... (Kapasitas, Stok, Media, Status tetap sama) ... --}}

            
            <div class="mb-4">
                <label for="pax" class="block text-sm font-medium text-gray-700 mb-2">Pax (Capacity / Min Quantity)</label>
                <input type="number" name="pax" id="pax" value="{{ old('pax', 1) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('pax')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">Jumlah (Stock Quantity)</label>
                <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', 1) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('jumlah')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            
            <div class="mb-4">
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Product Images (Multiple)</label>
                <input 
                    type="file" 
                    name="images[]"            
                    id="images" 
                    accept="image/*" 
                    multiple                      
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                <p class="text-xs text-gray-500 mt-1">Pilih beberapa gambar untuk produk.</p>
                @error('images')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="text-red-500 text-sm mt-1">Satu atau lebih file gambar tidak valid.</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Refund Policy --}}
            <div class="mb-4">
                <label for="refund_policy" class="block text-sm font-medium text-gray-700 mb-2">Refund Policy</label>
                <select name="refund_policy" id="refund_policy" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="mendukung" {{ old('refund_policy', 'mendukung') == 'mendukung' ? 'selected' : '' }}>Mendukung (User dapat refund saat booking)</option>
                    <option value="tidak mendukung" {{ old('refund_policy') == 'tidak mendukung' ? 'selected' : '' }}>Tidak Mendukung (User tidak dapat refund saat booking)</option>
                </select>
                @error('refund_policy')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ old('status') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('vendor.products') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Create Product</button>
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
    const discountValueInput = document.getElementById('discount_value');
    const discountValueFixedInput = document.getElementById('discount_value_fixed');
    const discountExpiresWrapper = document.getElementById('discount_expires_wrapper');
    const discountValueWrapper = document.getElementById('discount_value_wrapper');
    const discountFixedWrapper = document.getElementById('discount_fixed_wrapper');

    /**
     * Mengatur tampilan input diskon berdasarkan tipe yang dipilih.
     */
    function toggleDiscountInput() {
        const type = discountTypeSelect.value;

        // Reset semua input diskon
        discountValueInput.value = 0.00;
        discountValueFixedInput.value = 0.00;

        // Sembunyikan semua wrapper
        discountValueWrapper.classList.add('hidden');
        discountFixedWrapper.classList.add('hidden');
        discountExpiresWrapper.style.display = 'none';

        if (type === 'percentage') {
            discountValueWrapper.classList.remove('hidden');
            discountExpiresWrapper.style.display = 'block';
        } else if (type === 'fixed') {
            discountFixedWrapper.classList.remove('hidden');
            discountExpiresWrapper.style.display = 'block';
        }

        calculateNta(); // Hitung ulang NTA setelah ganti tipe
    }

    /**
     * Fungsi untuk menghitung NTA, menggunakan Basic Price dan salah satu tipe diskon.
     */
    function calculateNta() {
        const basicPrice = Math.max(0, parseFloat(basicPriceInput.value) || 0);
        const taxRate = Math.max(0, parseFloat(taxRateInput.value) || 0);
        const discountType = discountTypeSelect.value;

        let discountPrice = 0;

        // 1. Tentukan Discount Price berdasarkan tipe input
        if (discountType === 'percentage') {
            const discountRate = Math.max(0, parseFloat(discountValueInput.value) || 0);
            const validDiscountRate = Math.min(100, discountRate);
            discountPrice = basicPrice * (validDiscountRate / 100);

            // Pastikan input FixedPrice dikosongkan saat submit jika tidak digunakan
            discountValueFixedInput.setAttribute('disabled', 'disabled');
            discountValueInput.removeAttribute('disabled');
        } else if (discountType === 'fixed') {
            const fixedDiscount = Math.max(0, parseFloat(discountValueFixedInput.value) || 0);
            discountPrice = fixedDiscount;

            // Pastikan input DiscountRate dikosongkan saat submit jika tidak digunakan
            discountValueInput.setAttribute('disabled', 'disabled');
            discountValueFixedInput.removeAttribute('disabled');
        } else {
            // Tipe 'none'
            discountValueInput.setAttribute('disabled', 'disabled');
            discountValueFixedInput.setAttribute('disabled', 'disabled');
        }

        // Batasi Discount Price agar tidak melebihi Basic Price
        discountPrice = Math.min(discountPrice, basicPrice);

        // 2. Hitung Harga Setelah Diskon (Net Price sebelum Pajak)
        const netPriceBeforeTax = basicPrice - discountPrice;

        // 3. Hitung NTA (Harga Final dengan Pajak)
        const validTaxRate = Math.min(100, taxRate);
        const multiplier = 1 + (validTaxRate / 100);
        const nta = netPriceBeforeTax * multiplier;

        // Tampilkan NTA
        ntaInput.value = nta.toFixed(2);
    }

    // Event Listeners
    basicPriceInput.addEventListener('input', calculateNta);
    taxRateInput.addEventListener('input', calculateNta);
    discountValueInput.addEventListener('input', calculateNta);
    discountValueFixedInput.addEventListener('input', calculateNta);
    discountTypeSelect.addEventListener('change', toggleDiscountInput);

    // Inisialisasi tampilan input dan perhitungan NTA saat halaman dimuat
    // Gunakan old('discount_type') untuk menentukan tipe default
    if (discountTypeSelect.value === 'percentage' || discountTypeSelect.value === 'fixed') {
        // Already set from old value
    } else if (parseFloat(discountValueInput.value) > 0) {
        discountTypeSelect.value = 'percentage';
    } else if (parseFloat(discountValueFixedInput.value) > 0) {
        discountTypeSelect.value = 'fixed';
    } else {
        discountTypeSelect.value = '';
    }
    toggleDiscountInput(); // Panggil toggle untuk set tampilan awal
});
</script>
@endsection