@extends('layouts.user')

@section('title', 'Book Your Stay')

@section('welcome')
Complete your booking details!
@endsection

@section('content')
<!-- Booking Hero -->
<div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-800 rounded-2xl p-8 mb-8 text-white shadow-2xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2">Complete Your Booking</h1>
            <p class="text-blue-100">Fill in your details and confirm your perfect stay</p>
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
    <form method="POST" action="{{ route('user.book') }}" class="space-y-8">
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
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input type="tel" name="phone" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="+62">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Number of Guests</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-users text-gray-400"></i>
                        </div>
                        <input type="number" name="guests" min="1" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="1">
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-in Date</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-check text-gray-400"></i>
                        </div>
                        <input type="date" name="checkin" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check-out Date</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-calendar-times text-gray-400"></i>
                        </div>
                        <input type="date" name="checkout" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </div>

        <!-- Package Selection -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-hotel text-purple-600"></i>
                </div>
                Select Your Package
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Standard Room -->
                <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-blue-500 transition-all duration-200 cursor-pointer package-option" data-package="1">
                    <div class="text-center mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-bed text-blue-600"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">Standard Room</h4>
                        <p class="text-gray-600 text-sm">Comfortable room with basic amenities</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600 mb-2">Rp 450.000</div>
                        <span class="text-gray-500 text-sm">per night</span>
                    </div>
                    <input type="radio" name="package_id" value="1" class="hidden package-radio">
                </div>

                <!-- Deluxe Room -->
                <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-green-500 transition-all duration-200 cursor-pointer package-option" data-package="2">
                    <div class="text-center mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-concierge-bell text-green-600"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">Deluxe Room</h4>
                        <p class="text-gray-600 text-sm">Enhanced comfort with premium amenities</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 mb-2">Rp 750.000</div>
                        <span class="text-gray-500 text-sm">per night</span>
                    </div>
                    <input type="radio" name="package_id" value="2" class="hidden package-radio">
                </div>

                <!-- Suite -->
                <div class="border-2 border-gray-200 rounded-xl p-6 hover:border-purple-500 transition-all duration-200 cursor-pointer package-option" data-package="3">
                    <div class="text-center mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-crown text-purple-600"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">Suite</h4>
                        <p class="text-gray-600 text-sm">Luxury suite with panoramic views</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600 mb-2">Rp 1.250.000</div>
                        <span class="text-gray-500 text-sm">per night</span>
                    </div>
                    <input type="radio" name="package_id" value="3" class="hidden package-radio">
                </div>
            </div>
        </div>

        <!-- Add-ons -->
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-plus-circle text-orange-600"></i>
                </div>
                Additional Services
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-all duration-200 cursor-pointer">
                    <input type="checkbox" name="addons[]" value="breakfast" class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500">
                    <div class="ml-3">
                        <div class="font-semibold text-gray-800">Daily Breakfast</div>
                        <div class="text-sm text-gray-600">Rp 75.000 per day</div>
                    </div>
                </label>

                <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-all duration-200 cursor-pointer">
                    <input type="checkbox" name="addons[]" value="spa" class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500">
                    <div class="ml-3">
                        <div class="font-semibold text-gray-800">Spa Treatment</div>
                        <div class="text-sm text-gray-600">Rp 250.000 per session</div>
                    </div>
                </label>

                <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-all duration-200 cursor-pointer">
                    <input type="checkbox" name="addons[]" value="transport" class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500">
                    <div class="ml-3">
                        <div class="font-semibold text-gray-800">Airport Transfer</div>
                        <div class="text-sm text-gray-600">Rp 150.000 per trip</div>
                    </div>
                </label>

                <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-all duration-200 cursor-pointer">
                    <input type="checkbox" name="addons[]" value="guide" class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500">
                    <div class="ml-3">
                        <div class="font-semibold text-gray-800">Tour Guide</div>
                        <div class="text-sm text-gray-600">Rp 300.000 per day</div>
                    </div>
                </label>

                <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-all duration-200 cursor-pointer">
                    <input type="checkbox" name="addons[]" value="laundry" class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500">
                    <div class="ml-3">
                        <div class="font-semibold text-gray-800">Laundry Service</div>
                        <div class="text-sm text-gray-600">Rp 50.000 per load</div>
                    </div>
                </label>

                <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-orange-300 transition-all duration-200 cursor-pointer">
                    <input type="checkbox" name="addons[]" value="parking" class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500">
                    <div class="ml-3">
                        <div class="font-semibold text-gray-800">Valet Parking</div>
                        <div class="text-sm text-gray-600">Rp 25.000 per day</div>
                    </div>
                </label>
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
                    <span class="text-gray-600">Package:</span>
                    <span class="font-semibold text-gray-800" id="summary-package">Not selected</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Duration:</span>
                    <span class="font-semibold text-gray-800" id="summary-duration">0 nights</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Add-ons:</span>
                    <span class="font-semibold text-gray-800" id="summary-addons">None</span>
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
// Package selection functionality
document.querySelectorAll('.package-option').forEach(option => {
    option.addEventListener('click', function() {
        // Remove selected class from all options
        document.querySelectorAll('.package-option').forEach(opt => {
            opt.classList.remove('border-blue-500', 'border-green-500', 'border-purple-500');
            opt.classList.add('border-gray-200');
        });

        // Add selected class to clicked option
        const packageId = this.dataset.package;
        if (packageId == 1) {
            this.classList.remove('border-gray-200');
            this.classList.add('border-blue-500');
        } else if (packageId == 2) {
            this.classList.remove('border-gray-200');
            this.classList.add('border-green-500');
        } else if (packageId == 3) {
            this.classList.remove('border-gray-200');
            this.classList.add('border-purple-500');
        }

        // Check the radio button
        this.querySelector('.package-radio').checked = true;

        // Update summary
        updateSummary();
    });
});

// Update booking summary
function updateSummary() {
    const selectedPackage = document.querySelector('input[name="package_id"]:checked');
    const packageNames = {
        1: 'Standard Room - Rp 450.000/night',
        2: 'Deluxe Room - Rp 750.000/night',
        3: 'Suite - Rp 1.250.000/night'
    };

    if (selectedPackage) {
        document.getElementById('summary-package').textContent = packageNames[selectedPackage.value];
    }

    // Calculate total (basic implementation)
    let total = 0;
    if (selectedPackage) {
        const prices = {1: 450000, 2: 750000, 3: 1250000};
        total = prices[selectedPackage.value] || 0;
    }

    document.getElementById('summary-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

// Update summary when add-ons change
document.querySelectorAll('input[name="addons[]"]').forEach(addon => {
    addon.addEventListener('change', updateSummary);
});
</script>
@endsection
