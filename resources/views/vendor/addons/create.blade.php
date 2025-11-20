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

            {{-- START: Kolom Harga Baru --}}
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

                {{-- 3. NTA (Net Transaction Amount) - Otomatis --}}
                <div>
                    <label for="nta" class="block text-sm font-medium text-gray-700 mb-2">NTA (Harga Jual Final)</label>
                    <input type="number" name="nta" id="nta" value="{{ old('nta') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    @error('nta')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            {{-- END: Kolom Harga Baru --}}
            
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

            <div class="mb-4">
                <label for="publish" class="flex items-center">
                    <input type="checkbox" name="publish" id="publish" value="1" {{ old('publish') ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Publish</span>
                </label>
                @error('publish')
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

    /**
     * Fungsi untuk menghitung NTA (asumsi NTA = Basic Price + Tax).
     * Basic Price dianggap harga eksklusif pajak.
     * Rumus: NTA = Basic Price * (1 + Tax Rate%)
     */
    function calculateNta() {
        // Ambil nilai dan ubah ke float. Jika kosong, anggap 0.
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

    // Hitung NTA saat halaman dimuat (untuk old() value)
    calculateNta();
});
</script>
@endsection