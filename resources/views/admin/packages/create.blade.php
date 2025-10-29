@extends('layouts.admin')

@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Create New Packages</h1>

        <form action="{{ route('admin.packages.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
            @csrf

            {{-- **name_package** field --}}
            <div class="mb-4">
                <label for="name_package" class="block text-sm font-medium text-gray-700 mb-2">Packages Name</label>
                <input type="text" name="name_package" id="name_package" value="{{ old('name_package') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('name_package')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- **SLUG** field (Tambahan Baru) --}}
            <div class="mb-4">
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug (URL Friendly Name)</label>
                {{-- Slug biasanya otomatis terisi tapi bisa diubah manual --}}
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('slug')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Ini akan menjadi bagian dari URL. Contoh: `nama-paket-saya`.</p>
            </div>

            {{-- **price_publish** field --}}
            <div class="mb-4">
                <label for="price_publish" class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                <input type="number" name="price_publish" id="price_publish" value="{{ old('price_publish') }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('price_publish')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- **image** field --}}
            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Package Image</label>
                <input type="file" name="image" id="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('image')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- **description** field --}}
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- **start_publish** field --}}
            <div class="mb-4">
                <label for="start_publish" class="block text-sm font-medium text-gray-700 mb-2">Start Publish Date</label>
                <input type="date" name="start_publish" id="start_publish" value="{{ old('start_publish') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('start_publish')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- **end_publish** field --}}
            <div class="mb-4">
                <label for="end_publish" class="block text-sm font-medium text-gray-700 mb-2">End Publish Date</label>
                <input type="date" name="end_publish" id="end_publish" value="{{ old('end_publish') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('end_publish')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- **is_active** field --}}
            <div class="mb-4">
                <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">Active Status</label>
                <select name="is_active" id="is_active" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inactive / Draft</option>
                </select>
                @error('is_active')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.packages.store') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Create Package</button>
            </div>
        </form>
    </div>
</div>

{{-- Untuk fungsi auto-generate slug, Anda perlu menambahkan JavaScript. --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const namePackageInput = document.getElementById('name_package');
        const slugInput = document.getElementById('slug');

        // Fungsi untuk mengkonversi teks menjadi slug
        function slugify(text) {
            return text.toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '') // Hapus karakter non-alfanumerik kecuali spasi dan strip
                .replace(/[\s-]+/g, '-')     // Ganti spasi dan strip dengan satu strip
                .replace(/^-+|-+$/g, '');    // Hapus strip di awal atau akhir
        }

        // Event listener untuk membuat slug otomatis
        namePackageInput.addEventListener('keyup', function () {
            // Hanya isi slug jika field slug masih kosong
            if (slugInput.value === '') {
                slugInput.value = slugify(namePackageInput.value);
            }
        });
        
        // Agar slug tetap terisi otomatis saat input nama berubah, 
        // Anda mungkin ingin menghapus pengecekan 'slugInput.value === '''
        // tergantung pada preferensi UX Anda.
        namePackageInput.addEventListener('blur', function () {
            // Ini memastikan slug terisi otomatis jika pengguna tidak mengisinya
            if (slugInput.value === '') {
                 slugInput.value = slugify(namePackageInput.value);
            }
        });
    });
</script>
@endpush

@endsection