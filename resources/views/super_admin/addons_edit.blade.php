@extends('layouts.superadmin')

@section('title', 'Edit Addon')

@section('welcome')
Edit Addon
@endsection

@section('logout_route', route('super_admin.logout'))

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Addon</h3>

        <form action="{{ route('super_admin.addons.update', $addon->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Addon Name</label>
                    <input type="text" name="addons" value="{{ old('addons', $addon->addons) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" name="location" value="{{ old('location', $addon->location) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $addon->phone) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="desc" class="mt-1 block w-full border border-gray-300 rounded-md p-2">{{ old('desc', $addon->desc) }}</textarea>
                </div>


                <div>
                    <label class="block text-sm font-medium text-gray-700">NTA</label>
                    <input id="nta" type="number" step="0.01" name="nta" value="{{ old('nta', $addon->nta ?? $addon->price) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Upsell (Fixed Amount Rp)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">Rp</span>
                        </div>
                        <input id="upsell" type="number" step="0.01" name="upsell" value="{{ old('upsell', $addon->upsell ?? 0) }}" class="mt-1 block w-full pl-10 border border-gray-300 rounded-md p-2">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Discount Type</label>
                    <select id="discount_type" name="discount_type" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                        <option value="" {{ $addon->discount_type ? '' : 'selected' }}>None</option>
                        <option value="fixed" {{ $addon->discount_type == 'fixed' ? 'selected' : '' }}>Fixed</option>
                        <option value="percentage" {{ $addon->discount_type == 'percentage' ? 'selected' : '' }}>Percentage</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Discount Value</label>
                    <input type="number" step="0.01" name="discount_value" value="{{ old('discount_value', $addon->discount_value) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Discount Expires At</label>
                    <input type="date" name="discount_expires_at" value="{{ old('discount_expires_at', optional($addon->discount_expires_at)->format('Y-m-d')) }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Final Total Price (NTA + upsell - Discount)</label>
                    <div class="p-2 bg-blue-50 rounded-md">
                        <span class="font-bold text-lg" id="final_total_display">Rp{{ number_format(($addon->nta ?? $addon->price) + ($addon->upsell ?? 0) - ($addon->discount_amount ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <input type="hidden" name="final_total_price" id="final_total_price" value="{{ old('final_total_price', ($addon->nta ?? $addon->price) + ($addon->upsell ?? 0) - ($addon->discount_amount ?? 0)) }}">
                </div>

                {{-- Price Summary Fields --}}
                <div class="md:col-span-2 bg-gradient-to-r from-purple-50 to-blue-50 p-4 rounded-lg border border-purple-200">
                    <h4 class="font-bold text-gray-800 mb-3">💰 Price Summary</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white p-3 rounded border-l-4 border-blue-500">
                            <p class="text-xs text-gray-600">Total Price (NTA + Upsell)</p>
                            <p class="text-lg font-bold text-blue-600">Rp{{ number_format(($addon->total_price_before_discount ?? (($addon->nta ?? 0) + ($addon->upsell ?? 0))), 0, ',', '.') }}</p>
                            <input type="hidden" name="total_price_before_discount" id="total_price_before_discount" value="{{ $addon->total_price_before_discount ?? (($addon->nta ?? 0) + ($addon->upsell ?? 0)) }}">
                        </div>
                        <div class="bg-white p-3 rounded border-l-4 border-red-500">
                            <p class="text-xs text-gray-600">Discount Amount</p>
                            <p class="text-lg font-bold text-red-600">- Rp{{ number_format(($addon->discount_amount ?? 0), 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white p-3 rounded border-l-4 border-green-500">
                            <p class="text-xs text-gray-600">Final Price</p>
                            <p class="text-lg font-bold text-green-600">Rp{{ number_format(($addon->final_price ?? (($addon->total_price_before_discount ?? (($addon->nta ?? 0) + ($addon->upsell ?? 0))) - ($addon->discount_amount ?? 0))), 0, ',', '.') }}</p>
                            <input type="hidden" name="final_price" id="final_price" value="{{ $addon->final_price ?? (($addon->total_price_before_discount ?? (($addon->nta ?? 0) + ($addon->upsell ?? 0))) - ($addon->discount_amount ?? 0)) }}">
                        </div>
                    </div>
                </div>

                {{-- Hidden discount amount field updated by JS --}}
                <input type="hidden" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', $addon->discount_amount ?? 0) }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                        <option value="available" {{ $addon->status == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ $addon->status == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                        <option value="draft" {{ $addon->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="publish" {{ $addon->status == 'publish' ? 'selected' : '' }}>Publish</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex items-center space-x-3">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">Update Addon</button>
                <a href="{{ route('super_admin.addons') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ntaInput = document.getElementById('nta');
        const upsellInput = document.getElementById('upsell');
        const discountTypeSelect = document.getElementById('discount_type');
        const discountValueInput = document.getElementById('discount_value');
        const finalDisplay = document.getElementById('final_total_display');
        const finalHidden = document.getElementById('final_total_price');
        const discountAmountHidden = document.getElementById('discount_amount');

        const formatter = new Intl.NumberFormat('id-ID');

        function calculate() {
            const nta = parseFloat(ntaInput.value) || 0;
            const upsell = parseFloat(upsellInput.value) || 0;
            const discountType = discountTypeSelect ? discountTypeSelect.value : 'fixed';
            const discountValue = parseFloat(discountValueInput ? discountValueInput.value : 0) || 0;

            let discount = 0;
            const base = nta + upsell;
            if (discountType === 'percentage') {
                discount = (base * discountValue) / 100;
            } else {
                discount = discountValue;
            }

            const finalTotal = base - discount;
            finalDisplay.textContent = 'Rp' + formatter.format(Math.round(finalTotal));
            finalHidden.value = Math.round(finalTotal);
            discountAmountHidden.value = Math.round(discount);
            
            // Save to price summary fields
            const totalPriceBeforeDiscount = document.getElementById('total_price_before_discount');
            const finalPriceField = document.getElementById('final_price');
            if (totalPriceBeforeDiscount) totalPriceBeforeDiscount.value = base;
            if (finalPriceField) finalPriceField.value = finalTotal;
        }

        [ntaInput, upsellInput, discountTypeSelect, discountValueInput].forEach(el => {
            if (el) el.addEventListener('input', calculate);
        });

        // initial calc
        calculate();
    });
</script>

@endsection
