@extends('layouts.superadmin')

@section('title', 'Edit Product')

@section('welcome')
Edit Product
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Product</h3>

        <form action="{{ route('super_admin.products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category_id" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $product->id_category == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" class="mt-1 block w-full border border-gray-300 rounded-md p-2">{{ old('description', $product->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" name="location" value="{{ old('location', $product->location) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $product->phone) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Basic Price</label>
                    <input type="number" step="0.01" name="basic_price" value="{{ old('basic_price', $product->basic_price) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">NTA</label>
                    <input id="nta" type="number" step="0.01" name="nta" value="{{ old('nta', $product->nta) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Upsale (Fixed Amount Rp)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">Rp</span>
                        </div>
                        <input type="number" step="0.01" name="upsale" id="upsale" value="{{ old('upsale', $product->upsale ?? 0) }}" class="mt-1 block w-full pl-10 border border-gray-300 rounded-md p-2">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Discount Type</label>
                    <select id="discount_type" name="discount_type" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                        <option value="" {{ $product->discount_type ? '' : 'selected' }}>None</option>
                        <option value="fixed" {{ $product->discount_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                        <option value="percentage" {{ $product->discount_type == 'percentage' ? 'selected' : '' }}>Percentage</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Discount Value</label>
                    <input id="discount_value" type="number" step="0.01" name="discount_value" value="{{ old('discount_value', $product->discount_value) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Discount Expires At</label>
                    <input type="date" name="discount_expires_at" value="{{ old('discount_expires_at', optional($product->discount_expires_at)->format('Y-m-d')) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Final Total Price (NTA + Upsale - Discount)</label>
                    <div class="p-2 bg-blue-50 rounded-md">
                        <span class="font-bold text-lg" id="final_total_display">Rp{{ number_format(($product->nta ?? $product->basic_price) + ($product->upsale ?? 0) - ($product->discount_amount ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <input type="hidden" name="final_total_price" id="final_total_price" value="{{ old('final_total_price', ($product->nta ?? $product->basic_price) + ($product->upsale ?? 0) - ($product->discount_amount ?? 0)) }}">
                </div>

                {{-- Hidden discount amount field updated by JS --}}
                <input type="hidden" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', $product->discount_amount ?? 0) }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                        <option value="available" {{ $product->status == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ $product->status == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                        <option value="draft" {{ $product->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="publish" {{ $product->status == 'publish' ? 'selected' : '' }}>Publish</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex items-center space-x-3">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Update Product</button>
                <a href="{{ route('super_admin.products') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ntaInput = document.getElementById('nta');
        const upsaleInput = document.getElementById('upsale');
        const discountTypeSelect = document.getElementById('discount_type');
        const discountValueInput = document.getElementById('discount_value');
        const finalDisplay = document.getElementById('final_total_display');
        const finalHidden = document.getElementById('final_total_price');
        const discountAmountHidden = document.getElementById('discount_amount');

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number || 0);
        }

        function calculate() {
            const nta = parseFloat(ntaInput.value) || 0;
            const upsale = parseFloat(upsaleInput ? upsaleInput.value : 0) || 0;
            const totalBefore = nta + upsale;

            const discountType = discountTypeSelect ? discountTypeSelect.value : '';
            const discountValue = parseFloat(discountValueInput ? discountValueInput.value : 0) || 0;

            let discountAmount = 0;
            if (discountType === 'percentage' && discountValue > 0) {
                discountAmount = (totalBefore * discountValue) / 100;
            } else if (discountType === 'fixed' && discountValue > 0) {
                discountAmount = discountValue;
            }

            const finalTotal = Math.max(0, totalBefore - discountAmount);

            if (finalDisplay) finalDisplay.textContent = formatRupiah(finalTotal);
            if (finalHidden) finalHidden.value = finalTotal;
            if (discountAmountHidden) discountAmountHidden.value = Math.max(0, Math.round(discountAmount * 100) / 100);
        }

        ['input', 'change'].forEach(evt => {
            if (ntaInput) ntaInput.addEventListener(evt, calculate);
            if (upsaleInput) upsaleInput.addEventListener(evt, calculate);
            if (discountTypeSelect) discountTypeSelect.addEventListener(evt, calculate);
            if (discountValueInput) discountValueInput.addEventListener(evt, calculate);
        });

        calculate();
    });
</script>

@endsection
