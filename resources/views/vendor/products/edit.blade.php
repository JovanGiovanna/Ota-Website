@extends('layouts.vendor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Product</h1>

        <form action="{{ route('vendor.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if(Auth::guard('super_admin')->check())
            <div class="mb-4">
                <label for="id_vendor" class="block text-sm font-medium text-gray-700 mb-2">Vendor</label>
                <select name="id_vendor" id="id_vendor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Vendor</option>
                    @foreach(\App\Models\Vendor::all() as $vendor)
                        <option value="{{ $vendor->id }}" {{ old('id_vendor', $product->id_vendor) == $vendor->id ? 'selected' : '' }}>
                            {{ $vendor->name }} ({{ $vendor->email }})
                        </option>
                    @endforeach
                </select>
                @error('id_vendor')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <div class="mb-4">
                <label for="id_category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="id_category" id="id_category" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Category</option>
                    @foreach(\App\Models\Category::all() as $category)
                        <option value="{{ $category->id }}" {{ old('id_category', $product->id_category) == $category->id ? 'selected' : '' }}>
                            {{ $category->categories }}
                        </option>
                    @endforeach
                </select>
                @error('id_category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- START: Kolom Harga Baru (Mengganti Price) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                {{-- 1. Basic Price (Harga Dasar) --}}
                <div>
                    <label for="basic_price" class="block text-sm font-medium text-gray-700 mb-2">Basic Price (Harga Dasar)</label>
                    {{-- Asumsi kolom di model/database adalah basic_price, dan price lama diubah ke basic_price --}}
                    <input type="number" name="basic_price" id="basic_price" value="{{ old('basic_price', $product->basic_price ?? $product->price ?? 0) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('basic_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- 2. Tax Rate (%) --}}
                <div>
                    <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-2">Tax Rate (%)</label>
                    {{-- Asumsi kolom di model/database adalah tax_rate --}}
                    <input type="number" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', $product->tax_rate ?? 0.00) }}" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('tax_rate')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 3. NTA (Net Transaction Amount) - Otomatis --}}
                <div>
                    <label for="nta" class="block text-sm font-medium text-gray-700 mb-2">NTA (Harga Jual Final)</label>
                    {{-- Asumsi kolom di model/database adalah nta, atau menggunakan price jika nta belum ada --}}
                    <input type="number" name="nta" id="nta" value="{{ old('nta', $product->nta ?? $product->price ?? 0) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('nta')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            {{-- END: Kolom Harga Baru --}}

            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="pax" class="block text-sm font-medium text-gray-700 mb-2">Pax (Capacity / Min Quantity)</label>
                <input type="number" name="pax" id="pax" value="{{ old('pax', $product->pax) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('pax')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-2">Jumlah (Stock Quantity)</label>
                <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', $product->jumlah) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('jumlah')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="max_adults" class="block text-sm font-medium text-gray-700 mb-2">Max Adults</label>
                <input type="number" name="max_adults" id="max_adults" value="{{ old('max_adults', $product->max_adults) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('max_adults')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="max_children" class="block text-sm font-medium text-gray-700 mb-2">Max Children</label>
                <input type="number" name="max_children" id="max_children" value="{{ old('max_children', $product->max_children) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('max_children')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="available" {{ old('status', $product->status) == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ old('status', $product->status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="publish" class="flex items-center">
                    <input type="checkbox" name="publish" id="publish" value="1" {{ old('publish', $product->publish) ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Publish</span>
                </label>
                @error('publish')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Product Images</label>

                {{-- Current Images with Delete Selection --}}
                @if($product->images && is_array($product->images) && count($product->images) > 0)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Current Images (Select to Delete)</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($product->images as $index => $image)
                                <div class="relative group cursor-pointer" data-index="{{ $index }}">
                                    <img src="{{ asset('storage/' . $image) }}" alt="Current Image {{ $index + 1 }}" class="w-full h-24 object-cover rounded-md border-2 border-gray-200">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-md flex items-center justify-center">
                                        <label class="flex items-center space-x-2 bg-white bg-opacity-90 px-2 py-1 rounded cursor-pointer hover:bg-opacity-100 transition-all">
                                            <input type="checkbox" name="remove_images[]" value="{{ $index }}" class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500"
                                                {{ in_array($index, old('remove_images', [])) ? 'checked' : '' }}>
                                            <span class="text-xs font-medium text-gray-700">Delete</span>
                                        </label>
                                    </div>
                                    {{-- Red overlay when checked --}}
                                    <div class="absolute inset-0 bg-red-500 bg-opacity-0 rounded-md transition-all duration-200 {{ in_array($index, old('remove_images', [])) ? 'bg-opacity-20' : '' }}" id="overlay-{{ $index }}"></div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Click on images you want to remove. Selected images will be highlighted in red.</p>
                    </div>
                @endif

                {{-- Upload New Images --}}
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Add New Images</label>
                    <input type="file" name="images[]" id="images" accept="image/*" multiple class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Select multiple images to add. Leave empty to keep current images only.</p>
                </div>

                @error('images')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('remove_images')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('vendor.products') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Update Product</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Script Perhitungan Harga Baru ---
    const basicPriceInput = document.getElementById('basic_price');
    const taxRateInput = document.getElementById('tax_rate');
    const ntaInput = document.getElementById('nta');

    /**
     * Fungsi untuk menghitung NTA (asumsi NTA = Basic Price + Tax).
     * Rumus: NTA = Basic Price * (1 + Tax Rate%)
     */
    function calculateNta() {
        const basicPrice = parseFloat(basicPriceInput.value) || 0;
        const taxRate = parseFloat(taxRateInput.value) || 0;

        let nta = basicPrice; 

        if (basicPrice >= 0 && taxRate >= 0) {
            // Hitung NTA dengan asumsi Basic Price EKSKLUSIF Pajak
            const multiplier = 1 + (taxRate / 100);
            nta = basicPrice * multiplier;
        }

        // Tampilkan NTA, bulatkan ke 2 desimal
        ntaInput.value = nta.toFixed(2); 
    }

    // Panggil fungsi hitung saat ada perubahan pada Basic Price atau Tax Rate
    basicPriceInput.addEventListener('input', calculateNta);
    taxRateInput.addEventListener('input', calculateNta);

    // Hitung NTA saat halaman dimuat (untuk old() value atau $product->value)
    calculateNta();

    // --- Script untuk Hapus Gambar (dari kode asli Anda) ---
    // Handle checkbox changes for visual feedback
    document.querySelectorAll('input[name="remove_images[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const index = this.value;
            const overlay = document.getElementById('overlay-' + index);
            if (this.checked) {
                overlay.classList.add('bg-opacity-20');
            } else {
                overlay.classList.remove('bg-opacity-20');
            }
        });
        // Pastikan overlay diinisialisasi dengan benar jika ada 'old' value
        checkbox.dispatchEvent(new Event('change'));
    });

    // Make the entire image container clickable to toggle checkbox
    document.querySelectorAll('.relative.group.cursor-pointer').forEach(function(container) {
        container.addEventListener('click', function(e) {
            // Prevent triggering if clicking on the checkbox itself or related labels/spans
            if (e.target.type === 'checkbox' || e.target.tagName === 'LABEL' || e.target.tagName === 'SPAN') return;

            const checkbox = container.querySelector('input[name="remove_images[]"]');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                // Trigger change event
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });
    // --- Akhir Script untuk Hapus Gambar ---
});
</script>
@endsection