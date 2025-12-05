@extends('layouts.mail')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-8 text-white">
            <div class="text-center">
                <h1 class="text-3xl font-bold mb-2">Booking Confirmation</h1>
                <p class="text-blue-100">Your booking has been successfully created</p>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="px-6 py-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Booking Details</h2>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Booking Code</p>
                        <p class="font-semibold text-gray-800">{{ $booking->booking_code }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Check-in</p>
                        <p class="font-semibold text-gray-800">{{ $booking->checkin_appointment_start->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Check-out</p>
                        <p class="font-semibold text-gray-800">{{ $booking->checkout_appointment_end->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Duration</p>
                        <p class="font-semibold text-gray-800">{{ $booking->duration_days }} days</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Amount</p>
                        <p class="font-semibold text-gray-800">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Guest Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Guest Information</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Name</p>
                            <p class="font-semibold text-gray-800">{{ $booking->booker_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-semibold text-gray-800">{{ $booking->booker_email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Phone</p>
                            <p class="font-semibold text-gray-800">{{ $booking->booker_telp }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Booked -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Items Booked</h3>
                <div class="space-y-3">
                    @if($booking->packages->count() > 0)
                        @foreach($booking->packages as $package)
                            <div class="flex justify-between items-center bg-blue-50 p-3 rounded-lg">
                                <div>
                                    <p class="font-semibold text-blue-800">{{ $package->package->name_package }}</p>
                                    <p class="text-sm text-blue-600">Package - {{ $booking->duration_days }} days</p>
                                </div>
                                <p class="font-semibold text-blue-800">Rp {{ number_format($package->package->nta * $booking->duration_days, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    @endif

                    @if($booking->products->count() > 0)
                        @foreach($booking->products as $product)
                            <div class="flex justify-between items-center bg-green-50 p-3 rounded-lg">
                                <div>
                                    <p class="font-semibold text-green-800">{{ $product->product->name }}</p>
                                    <p class="text-sm text-green-600">Product - Qty: {{ $product->amount }}</p>
                                </div>
                                <p class="font-semibold text-green-800">Rp {{ number_format($product->product->finalPrice * $product->amount, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    @endif

                    @if($booking->addons->count() > 0)
                        @foreach($booking->addons as $addon)
                            <div class="flex justify-between items-center bg-orange-50 p-3 rounded-lg">
                                <div>
                                    <p class="font-semibold text-orange-800">{{ $addon->addon->addons }}</p>
                                    <p class="text-sm text-orange-600">Addon - Qty: {{ $addon->amount }}</p>
                                </div>
                                <p class="font-semibold text-orange-800">Rp {{ number_format($addon->addon->finalPrice * $addon->amount, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">Next Steps</h3>
                <ol class="list-decimal list-inside text-yellow-700 space-y-1">
                    <li>Complete your payment to confirm the booking</li>
                    <li>You will receive a payment confirmation email</li>
                    <li>Check your booking details and prepare for your stay</li>
                </ol>
            </div>

            <!-- Account Activation (for guest users) -->
            @if($booking->user && $booking->user->activation_token && is_null($booking->user->password))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-green-800 mb-2">Complete Your Account</h3>
                    <p class="text-green-700 mb-3">
                        You booked as a guest. To manage your bookings and access your account history in the future, please activate your account by setting a password.
                    </p>
                    <div class="text-center">
                        <a href="{{ url('/account/activate/' . $booking->user->activation_token) }}"
                           class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                            Activate My Account
                        </a>
                    </div>
                    <p class="text-sm text-green-600 mt-2">
                        This link will expire in 7 days. You can also access your booking details anytime using this email.
                    </p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="text-center space-y-3">
                <a href="{{ route('user.detail_history', $booking->id) }}"
                   class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors mr-4">
                    View Booking Details
                </a>
                <a href="{{ route('user.payment', $booking->id) }}"
                   class="inline-block bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                    Proceed to Payment
                </a>
            </div>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-200 text-center text-gray-600">
                <p class="mb-2">Thank you for choosing our service!</p>
                <p class="text-sm">
                    If you have any questions, please contact our support team at
                    <a href="mailto:support@otawebsite.com" class="text-blue-600 hover:underline">support@otawebsite.com</a>
                </p>
                <p class="text-sm mt-2">
                    © {{ date('Y') }} OTA Website. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
