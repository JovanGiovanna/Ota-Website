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
                    <label class="block text-sm font-medium text-gray-700">Upsale (Fixed Amount Rp)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">Rp</span>
                        </div>
                        <input id="upsale" type="number" step="0.01" name="upsale" value="{{ old('upsale', $addon->upsale ?? 0) }}" class="mt-1 block w-full pl-10 border border-gray-300 rounded-md p-2">
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

                        const formatter = new Intl.NumberFormat('id-ID');

                        function calculate() {
                            const nta = parseFloat(ntaInput.value) || 0;
                            const upsale = parseFloat(upsaleInput.value) || 0;
                            const discountType = discountTypeSelect ? discountTypeSelect.value : 'fixed';
                            const discountValue = parseFloat(discountValueInput ? discountValueInput.value : 0) || 0;

                            let discount = 0;
                            const base = nta + upsale;
                            if (discountType === 'percentage') {
                                discount = (base * discountValue) / 100;
                            } else {
                                discount = discountValue;
                            }

                            const finalTotal = base - discount;
                            finalDisplay.textContent = 'Rp' + formatter.format(Math.round(finalTotal));
                            finalHidden.value = Math.round(finalTotal);
                            discountAmountHidden.value = Math.round(discount);
                        }

                        [ntaInput, upsaleInput, discountTypeSelect, discountValueInput].forEach(el => {
                            if (el) el.addEventListener('input', calculate);
                        });

                        // initial calc
                        calculate();
                    });
                </script>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Discount Type</label>
                    <select name="discount_type" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
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
                    <label class="block text-sm font-medium text-gray-700">Final Total Price (NTA + Upsale - Discount)</label>
                    <div class="p-2 bg-blue-50 rounded-md">
                        <span class="font-bold text-lg" id="final_total_display">Rp{{ number_format(($addon->nta ?? $addon->price) + ($addon->upsale ?? 0) - ($addon->discount_amount ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <input type="hidden" name="final_total_price" id="final_total_price" value="{{ old('final_total_price', ($addon->nta ?? $addon->price) + ($addon->upsale ?? 0) - ($addon->discount_amount ?? 0)) }}">
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
@endsection
