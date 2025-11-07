@extends('layouts.user')

@section('title', 'Book Your Stay')

@section('welcome')
Book Your Package with Products and Add-ons!
@endsection

@section('content')
<!-- Booking Hero -->
<div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-800 rounded-2xl p-8 mb-8 text-white shadow-2xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2">Book Your Package</h1>
            <p class="text-blue-100">Select your package and add-ons for a perfect experience</p>
        </div>
        <div class="hidden md:block">
            <div class="w-20 h-20 bg-white/10 rounded-full flex items-center justify-center">
                <i class="fas fa-calendar-check text-3xl text-white"></i>
            </div>
        </div>
    </div>
</div>

<!-- Booking Form -->
<div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
    <form method="POST" action="{{ route('user.book') }}" id="booking-form" class="space-y-8">
        @csrf

        <!-- Personal Information -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-user text-blue-600"></i>
                </div>
                Personal Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" name="booker_name" value="{{ old('booker_name', auth()->user()->name ?? '') }}" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="booker_email" value="{{ old('booker_email', auth()->user()->email ?? '') }}" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <!-- Phone -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input type="tel" name="booker_telp" value="{{ old('booker_telp', auth()->user()->phone ?? '') }}" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="+62">
                    </div>
                </div>
                <!-- Guests -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Guests</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-users text-gray-400"></i>
                        </div>
                        <input type="number" name="amount" min="1" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="1">
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-calendar-alt text-green-600"></i>
                </div>
                Booking Details
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Check-in -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-in Date</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-check text-gray-400"></i>
                        </div>
                        <input type="date" name="checkin_appointment_start" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
                <!-- Check-out -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-out Date</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-times text-gray-400"></i>
                        </div>
                        <input type="date" name="checkout_appointment_end" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
                <!-- Duration -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Duration (Days)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-clock text-gray-400"></i>
                        </div>
                        <input type="number" name="duration_days" min="1" required value="1" class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="1">
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Type Selection -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-list text-purple-600"></i>
                </div>
                Select What You Want to Book
            </h3>
            <p class="text-gray-600 mb-6">You can select any combination of packages, products, and add-ons</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $types = ['package'=>'blue','product'=>'green','addon'=>'orange'];
                @endphp
                @foreach($types as $type => $color)
                <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-{{ $color }}-500 transition-all duration-200 cursor-pointer booking-type-option" data-type="{{ $type }}">
                    <div class="text-center mb-4">
                        <div class="w-12 h-12 bg-{{ $color }}-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-{{ $type=='package'?'box':($type=='product'?'shopping-cart':'plus-circle') }} text-{{ $color }}-600"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">{{ ucfirst($type) }}s</h4>
                        <p class="text-gray-600 text-sm">{{ $type=='package'?'Complete packages with accommodations':($type=='product'?'Individual products/services':'Additional services & extras') }}</p>
                    </div>
                    <input type="checkbox" name="booking_types[]" value="{{ $type }}" class="hidden booking-type-checkbox">
                </div>
                @endforeach
            </div>
        </div>

        <!-- Package Selection -->
        <div id="package-selection" class="hidden">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-hotel text-purple-600"></i>
                </div>
                Select Your Package
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($packages ?? [] as $package)
                    <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-blue-500 transition-all duration-200 cursor-pointer package-option" data-package="{{ $package->id }}" data-price="{{ $package->price_publish }}">
                        <div class="text-center mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                @if($package->image)
                                    <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name_package }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <i class="fas fa-bed text-blue-600"></i>
                                @endif
                            </div>
                            <h4 class="font-bold text-gray-800">{{ $package->name_package }}</h4>
                            <p class="text-gray-600 text-sm">{{ Str::limit($package->description, 50) }}</p>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 mb-2">Rp {{ number_format($package->price_publish, 0, ',', '.') }}</div>
                            <span class="text-gray-500 text-sm">per night</span>
                        </div>
                        <input type="checkbox" name="id_package[]" value="{{ $package->id }}" class="hidden package-radio">
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-box text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">No packages available</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Product Selection -->
        <div id="product-selection" class="hidden">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-shopping-cart text-green-600"></i>
                </div>
                Select Your Product
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($products ?? [] as $product)
                    <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-green-500 transition-all duration-200 cursor-pointer product-option" data-product="{{ $product->id }}" data-price="{{ $product->price }}">
                        <div class="text-center mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <i class="fas fa-shopping-cart text-green-600"></i>
                                @endif
                            </div>
                            <h4 class="font-bold text-gray-800">{{ $product->name }}</h4>
                            <p class="text-gray-600 text-sm">{{ Str::limit($product->description, 50) }}</p>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600 mb-2">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            <span class="text-gray-500 text-sm">per unit</span>
                        </div>
                        <input type="checkbox" name="product_id[]" value="{{ $product->id }}" class="hidden product-radio">
                        <input type="number" name="quantity[{{ $product->id }}]" value="1" min="1" class="mt-2 w-full px-2 py-1 border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-shopping-cart text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">No products available</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Addon Selection -->
        <div id="addon-selection" class="hidden">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-plus-circle text-orange-600"></i>
                </div>
                Select Your Addon
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($addons ?? [] as $addon)
                    <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-orange-500 transition-all duration-200 cursor-pointer addon-option" data-addon="{{ $addon->id }}" data-price="{{ $addon->price }}">
                        <div class="text-center mb-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                @if($addon->image)
                                    <img src="{{ asset('storage/' . $addon->image) }}" alt="{{ $addon->name }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <i class="fas fa-plus-circle text-orange-600"></i>
                                @endif
                            </div>
                            <h4 class="font-bold text-gray-800">{{ $addon->addons }}</h4>
                            <p class="text-gray-600 text-sm">{{ Str::limit($addon->desc, 50) }}</p>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600 mb-2">Rp {{ number_format($addon->price, 0, ',', '.') }}</div>
                            <span class="text-gray-500 text-sm">per unit</span>
                        </div>
                        <input type="checkbox" name="addon_id[]" value="{{ $addon->id }}" class="hidden addon-radio">
                        <input type="number" name="quantity[{{ $addon->id }}]" value="1" min="1" class="mt-2 w-full px-2 py-1 border border-gray-300 rounded-xl text-gray-900 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-plus-circle text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500">No addons available</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Special Requests -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-sticky-note text-indigo-600"></i>
                </div>
                Special Requests
            </h3>
            <div class="relative">
                <textarea name="requests" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none" placeholder="Any special requests, dietary restrictions, or additional notes..."></textarea>
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-receipt text-blue-600"></i>
                </div>
                Booking Summary
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Packages:</span>
                    <span class="font-semibold text-gray-800" id="summary-package">Not selected</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Products:</span>
                    <span class="font-semibold text-gray-800" id="summary-product">None</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Add-ons:</span>
                    <span class="font-semibold text-gray-800" id="summary-addons">None</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Duration:</span>
                    <span class="font-semibold text-gray-800" id="summary-duration">1 night</span>
                </div>
                <hr class="border-gray-300">
                <div class="flex justify-between items-center text-lg">
                    <span class="font-bold text-gray-800">Total:</span>
                    <span class="font-bold text-blue-600 text-xl" id="summary-total">Rp 0</span>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-12 py-4 rounded-xl font-bold text-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg transform hover:scale-105">
                <i class="fas fa-calendar-check mr-2"></i>
                Confirm Booking
            </button>
            <p class="text-gray-500 text-sm mt-3">You can cancel or modify your booking up to 24 hours before check-in</p>
        </div>
    </form>
</div>

<script>
// Helper function
function toggleSelection(el, borderClass) {
    el.classList.toggle('selected');
    el.classList.toggle(borderClass);
    el.classList.toggle('border-gray-200');
}

// Update summary
function updateSummary() {
    let total = 0;
    let packages = [];
    let products = [];
    let addons = [];

    const durationInput = document.querySelector('input[name="duration_days"]');
    const duration = durationInput ? parseInt(durationInput.value) || 1 : 1;

    document.querySelectorAll('.package-option.selected').forEach(option => {
        const name = option.querySelector('h4').textContent;
        const price = parseInt(option.dataset.price) || 0;
        const packageTotal = price * duration;
        packages.push(`${name} - Rp ${price.toLocaleString('id-ID')}/night`);
        total += packageTotal;
    });
    document.querySelectorAll('.product-option.selected').forEach(option => {
        const name = option.querySelector('h4').textContent;
        const price = parseInt(option.dataset.price) || 0;
        const qtyInput = option.querySelector('input[type="number"]');
        const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
        products.push(`${name} x${qty} - Rp ${(price*qty).toLocaleString('id-ID')}`);
        total += price*qty;
    });
    document.querySelectorAll('.addon-option.selected').forEach(option => {
        const name = option.querySelector('h4').textContent;
        const price = parseInt(option.dataset.price) || 0;
        const qtyInput = option.querySelector('input[type="number"]');
        const qty = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
        addons.push(`${name} x${qty} - Rp ${(price*qty).toLocaleString('id-ID')}`);
        total += price*qty;
    });

    document.getElementById('summary-package').textContent = packages.length ? packages.join(', ') : 'Not selected';
    document.getElementById('summary-product').textContent = products.length ? products.join(', ') : 'None';
    document.getElementById('summary-addons').textContent = addons.length ? addons.join(', ') : 'None';
    document.getElementById('summary-duration').textContent = duration + ' night' + (duration>1?'s':'');
    document.getElementById('summary-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// Event listeners for booking type selection
document.querySelectorAll('.booking-type-option').forEach(option => {
    option.addEventListener('click', function() {
        const type = this.dataset.type;
        const checkbox = this.querySelector('.booking-type-checkbox');

        // Toggle selection
        const colorClass = type==='package'?'border-blue-500':type==='product'?'border-green-500':'border-orange-500';
        toggleSelection(this, colorClass);

        // Update checkbox
        checkbox.checked = this.classList.contains('selected');

        // Show/hide selection sections based on selected types
        const selectedTypes = Array.from(document.querySelectorAll('.booking-type-option.selected')).map(opt => opt.dataset.type);

        ['package','product','addon'].forEach(t => {
            const section = document.getElementById(`${t}-selection`);
            if (selectedTypes.includes(t)) {
                section.classList.remove('hidden');
            } else {
                section.classList.add('hidden');
                // Deselect all options in hidden sections
                section.querySelectorAll(`.${t}-option.selected`).forEach(opt => {
                    opt.classList.remove('selected', `border-${t==='package'?'blue':t==='product'?'green':'orange'}-500`);
                    opt.classList.add('border-gray-200');
                    const cb = opt.querySelector('input[type="checkbox"]');
                    if (cb) cb.checked = false;
                });
            }
        });

        updateSummary();
    });
});

// Selection listeners
['package','product','addon'].forEach(type=>{
    document.querySelectorAll(`.${type}-option`).forEach(option=>{
        option.addEventListener('click', function(e){
            e.stopPropagation(); // avoid triggering type click
            const border = type==='package'?'border-blue-500':type==='product'?'border-green-500':'border-orange-500';
            toggleSelection(this, border);
            const checkbox = this.querySelector('input[type="checkbox"]');
            if(checkbox) checkbox.checked = this.classList.contains('selected');
            updateSummary();
        });
    });
});

// Quantity update
document.querySelectorAll('.product-option input[type="number"], .addon-option input[type="number"]').forEach(input=>{
    input.addEventListener('input', updateSummary);
});

// Duration update
document.querySelector('input[name="duration_days"]').addEventListener('input', updateSummary);

// Pre-select URL
const urlParams = new URLSearchParams(window.location.search);
['package','product','addon'].forEach(type=>{
    if(urlParams.has(type)){
        const id = urlParams.get(type);
        document.querySelector(`.booking-type-option[data-type="${type}"]`)?.click();
        const option = document.querySelector(`.${type}-option[data-${type}="${id}"]`);
        if(option) option.click();
    }
});

// Initial summary
updateSummary();
</script>
@endsection
