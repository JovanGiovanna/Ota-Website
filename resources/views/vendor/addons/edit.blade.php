@extends('layouts.vendor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Edit Addon</h1>

        <form action="{{ route('vendor.addons.update', $addon->id) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="addons" class="block text-sm font-medium text-gray-700 mb-2">Addon Name</label>
                <input type="text" name="addons" id="addons" value="{{ old('addons', $addon->addons) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
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
                        <option value="{{ $vendor->id }}" {{ old('id_vendor', $addon->id_vendor) == $vendor->id ? 'selected' : '' }}>
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
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                <input type="number" name="price" id="price" value="{{ old('price', $addon->price) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bidang Pax (Kapasitas/Jumlah) --}}
            <div class="mb-4">
                <label for="pax" class="block text-sm font-medium text-gray-700 mb-2">Pax / Capacity</label>
                {{-- Gunakan tipe number dan min 1 (sesuai validasi di Controller) --}}
                <input type="number" name="pax" id="pax" value="{{ old('pax', $addon->pax ?? 1) }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('pax')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="desc" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="desc" id="desc" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('desc', $addon->desc) }}</textarea>
                @error('desc')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="available" {{ old('status', $addon->status) == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="unavailable" {{ old('status', $addon->status) == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    <option value="draft" {{ old('status', $addon->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="publish" class="flex items-center">
                    <input type="checkbox" name="publish" id="publish" value="1" {{ old('publish', $addon->publish) ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Publish</span>
                </label>
                @error('publish')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Addon Images</label>

                {{-- Current Images with Delete Selection --}}
                @if($addon->images && is_array($addon->images) && count($addon->images) > 0)
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Current Images (Select to Delete)</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach($addon->images as $index => $image)
                                <div class="relative group cursor-pointer" data-index="{{ $index }}">
                                    <img src="{{ asset('storage/' . $image) }}" alt="Current Image {{ $index + 1 }}" class="w-full h-24 object-cover rounded-md border-2 border-gray-200">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-md flex items-center justify-center">
                                        <label class="flex items-center space-x-2 bg-white bg-opacity-90 px-2 py-1 rounded cursor-pointer hover:bg-opacity-100 transition-all">
                                            <input type="checkbox" name="remove_images[]" value="{{ $index }}" class="w-4 h-4 text-red-600 bg-gray-100 border-gray-300 rounded focus:ring-red-500">
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
                <a href="{{ route('vendor.addons') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Update Addon</button>
            </div>
        </form>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
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
    });

    // Make the entire image container clickable to toggle checkbox
    document.querySelectorAll('.relative.group.cursor-pointer').forEach(function(container) {
        container.addEventListener('click', function(e) {
            // Prevent triggering if clicking on the checkbox itself
            if (e.target.type === 'checkbox' || e.target.tagName === 'LABEL' || e.target.tagName === 'SPAN') return;

            const checkbox = container.querySelector('input[name="remove_images[]"]');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                // Trigger change event
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });
});
</script>
