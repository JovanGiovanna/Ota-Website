@extends('layouts.vendor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Create New Product</h1>

        <form action="{{ route('vendor.products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
            @csrf

            {{-- ... (Bagian Name dan Category tetap sama) ... --}}
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
            
            {{-- Bagian Harga yang Disesuaikan --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="mb-4 md:mb-0">
                    <label for="basic_price" class="block text-sm font-medium text-gray-700 mb-2">Basic Price (Harga Jual)</label>
                    <input type="number" name="basic_price" id="basic_price" value="{{ old('basic_price') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('basic_price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4 md:mb-0">
                    <label for="nta" class="block text-sm font-medium text-gray-700 mb-2">NTA (Net Transaction Amount)</label>
                    <input type="number" name="nta" id="nta" value="{{ old('nta') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('nta')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4 md:mb-0">
                    <label for="tax_rate" class="block text-sm font-medium text-gray-700 mb-2">Tax Rate (%)</label>
                    <input type="number" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', 0.00) }}" step="0.01" min="0" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('tax_rate')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            {{-- Akhir Bagian Harga yang Disesuaikan --}}

            {{-- ... (Sisa form tetap sama) ... --}}
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
                <label for="max_adults" class="block text-sm font-medium text-gray-700 mb-2">Max Adults</label>
                <input type="number" name="max_adults" id="max_adults" value="{{ old('max_adults', 2) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('max_adults')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="max_children" class="block text-sm font-medium text-gray-700 mb-2">Max Children</label>
                <input type="number" name="max_children" id="max_children" value="{{ old('max_children', 1) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('max_children')
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
    
    function calculateNta() {
        const basicPrice = parseFloat(basicPriceInput.value) || 0;
        const taxRate = parseFloat(taxRateInput.value) || 0;

        let nta = basicPrice; 

        if (basicPrice >= 0 && taxRate >= 0) {
            const multiplier = 1 + (taxRate / 100);
            nta = basicPrice * multiplier;
        }
        ntaInput.value = nta.toFixed(2); 
    }

    basicPriceInput.addEventListener('input', calculateNta);
    taxRateInput.addEventListener('input', calculateNta);

    calculateNta();
});
</script>
@endsection