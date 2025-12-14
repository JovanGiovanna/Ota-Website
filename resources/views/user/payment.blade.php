@extends('layouts.user')

@section('title', 'Payment')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Payment Header -->
        <div class="bg-gradient-to-r from-green-600 to-blue-600 rounded-2xl p-8 mb-8 text-white shadow-2xl">
            <div class="text-center">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-credit-card text-3xl"></i>
                </div>
                <h1 class="text-3xl font-bold mb-2">Complete Your Payment</h1>
                <p class="text-green-100">Secure payment processing for your booking</p>
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-receipt text-blue-600"></i>
                </div>
                Booking Summary
            </h2>

            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Booking Code:</span>
                    <span class="font-semibold text-gray-800">{{ $booking->booking_code }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Check-in:</span>
                    <span class="font-semibold text-gray-800">{{ $booking->checkin_appointment_start->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Check-out:</span>
                    <span class="font-semibold text-gray-800">{{ $booking->checkout_appointment_end->format('d M Y') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Duration:</span>
                    <span class="font-semibold text-gray-800">{{ $booking->duration_days }} days</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Guest:</span>
                    <span class="font-semibold text-gray-800">{{ $booking->booker_name }}</span>
                </div>
            </div>

            <!-- Items Breakdown -->
            <div class="border-t pt-4">
                <h3 class="font-semibold text-gray-800 mb-3">Items:</h3>
                <div class="space-y-2">
                    @if($booking->packages->count() > 0)
                        @foreach($booking->packages as $package)
                            <div class="flex justify-between items-center text-sm">
                                <span>{{ $package->package->name_package }} ({{ $booking->duration_days }} days)</span>
                                <span>Rp {{ number_format($package->package->nta * $booking->duration_days, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    @endif

                    @if($booking->products->count() > 0)
                        @foreach($booking->products as $product)
                            <div class="flex justify-between items-center text-sm">
                                <span>{{ $product->product->name }} (Qty: {{ $product->amount }})</span>
                                <span>Rp {{ number_format($product->product->finalPrice * $product->amount, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    @endif

                    @if($booking->packages->count() > 0)
                        @foreach($booking->packages as $package)
                            @if($package->bookPackageAddons->count() > 0)
                                @foreach($package->bookPackageAddons as $packageAddon)
                                    <div class="flex justify-between items-center text-sm">
                                        <span>{{ $packageAddon->addon->addons }} (for {{ $package->package->name_package }})</span>
                                        <span>Rp {{ number_format($packageAddon->addon->finalPrice, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    @endif

                    @if($booking->products->count() > 0)
                        @foreach($booking->products as $product)
                            @if($product->bookProductAddons->count() > 0)
                                @foreach($product->bookProductAddons as $productAddon)
                                    <div class="flex justify-between items-center text-sm">
                                        <span>{{ $productAddon->addon->addons }} (for {{ $product->product->name }}, Qty: {{ $productAddon->quantity }})</span>
                                        <span>Rp {{ number_format($productAddon->addon->finalPrice * $productAddon->quantity, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    @endif

                    @if($booking->addons->count() > 0)
                        @foreach($booking->addons as $addon)
                            <div class="flex justify-between items-center text-sm">
                                <span>{{ $addon->addon->addons }} (Qty: {{ $addon->amount }})</span>
                                <span>Rp {{ number_format($addon->addon->finalPrice * $addon->amount, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="border-t pt-4 mt-4">
                <div class="flex justify-between items-center text-lg font-bold">
                    <span>Total Amount:</span>
                    <span class="text-blue-600">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Payment Method Selection -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-money-bill-wave text-green-600"></i>
                </div>
                Payment Method
            </h2>

            <div class="space-y-4">
                <div class="border-2 border-blue-200 bg-blue-50 rounded-xl p-4">
                    <div class="flex items-center">
                        <input type="radio" id="dummy_payment" name="payment_method" value="dummy" class="text-blue-600 focus:ring-blue-500" checked>
                        <label for="dummy_payment" class="ml-3 flex items-center cursor-pointer">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-sim-card text-blue-600"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800">Dummy Payment</div>
                                <div class="text-sm text-gray-600">Test payment method - No real transaction</div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Action -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <div class="text-center">
                @if($booking->payment_expires_at && now()->isAfter($booking->payment_expires_at))
                    <!-- Payment Expired -->
                    <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-6">
                        <div class="text-red-600 mb-4">
                            <i class="fas fa-clock text-3xl mb-2"></i>
                            <h3 class="text-xl font-bold">Payment Time Expired</h3>
                        </div>
                        <p class="text-red-700">The payment deadline has passed. This booking has been cancelled.</p>
                        <p class="text-sm text-red-600 mt-2">Expired at: {{ $booking->payment_expires_at->format('d M Y H:i') }}</p>
                    </div>
                @else
                    <!-- Payment Timer -->
                    @if($booking->payment_expires_at)
                        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6">
                            <div class="text-orange-600 mb-2">
                                <i class="fas fa-clock text-xl"></i>
                                <span class="font-semibold ml-2">Payment Deadline</span>
                            </div>
                            <div id="payment-timer" class="text-2xl font-bold text-orange-700" data-expires="{{ $booking->payment_expires_at->toISOString() }}">
                                Calculating...
                            </div>
                            <p class="text-sm text-orange-600 mt-1">Complete your payment before the time runs out</p>
                        </div>
                    @endif

                    <p class="text-gray-600 mb-6">Click the button below to complete your payment</p>

                    <form method="POST" action="{{ route('user.payment.confirm', $booking->id) }}" id="payment-form">
                        @csrf
                        <button type="submit" id="pay-button" class="bg-gradient-to-r from-green-600 to-blue-600 text-white px-12 py-4 rounded-xl font-bold text-lg hover:from-green-700 hover:to-blue-700 transition-all duration-200 shadow-lg transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-credit-card mr-2"></i>
                            Pay Now (Dummy)
                        </button>
                    </form>

                    <p class="text-gray-500 text-sm mt-4">
                        <i class="fas fa-info-circle mr-1"></i>
                        This is a dummy payment for testing purposes. In production, this would integrate with real payment gateways.
                    </p>
                @endif
            </div>
        </div>

        @if($booking->payment_expires_at && !now()->isAfter($booking->payment_expires_at))
        <script>
            function updateTimer() {
                const timerElement = document.getElementById('payment-timer');
                const payButton = document.getElementById('pay-button');
                const paymentForm = document.getElementById('payment-form');
                const expiresAt = new Date(timerElement.dataset.expires);
                const now = new Date();
                const timeLeft = expiresAt - now;

                if (timeLeft <= 0) {
                    timerElement.innerHTML = '<span class="text-red-600">EXPIRED</span>';
                    payButton.disabled = true;
                    payButton.innerHTML = '<i class="fas fa-times-circle mr-2"></i>Payment Expired';
                    paymentForm.style.display = 'none';

                    // Auto refresh to show expired state
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                    return;
                }

                const minutes = Math.floor(timeLeft / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                timerElement.innerHTML = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                // Change color when less than 5 minutes
                if (minutes < 5) {
                    timerElement.style.color = '#dc2626'; // red-600
                } else if (minutes < 10) {
                    timerElement.style.color = '#ea580c'; // orange-600
                }
            }

            // Update timer every second
            updateTimer();
            setInterval(updateTimer, 1000);
        </script>
        @endif
    </div>
</div>
@endsection
