@extends('layouts.user')

@section('title', 'Booking Details')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="mb-6">
        <a href="{{ route('user.history') }}" class="text-blue-600 hover:underline">&larr; Back to History</a>
    </div>

    <h1 class="text-3xl font-bold text-gray-800 mb-6">Booking Details</h1>

    @if($booking)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Booking Info -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-semibold mb-4">Booking Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Booking ID</p>
                            <p class="font-semibold">#{{ $booking->id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                                @if($booking->status == 'confirmed') bg-green-100 text-green-800
                                @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->status == 'completed') bg-blue-100 text-blue-800
                                @elseif($booking->status == 'checked_in') bg-purple-100 text-purple-800
                                @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Check-in</p>
                            <p class="font-semibold">{{ $booking->checkin_appointment_start->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Check-out</p>
                            <p class="font-semibold">{{ $booking->checkout_appointment_end->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Duration</p>
                            <p class="font-semibold">{{ $booking->duration_days }} days</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Booking Type</p>
                            <p class="font-semibold">
                                @if($booking->packages->count() > 0 && $booking->products->count() > 0 && $booking->addons->count() > 0)
                                    Mixed Booking
                                @elseif($booking->packages->count() > 0)
                                    Package Booking
                                @elseif($booking->products->count() > 0)
                                    Product Booking
                                @elseif($booking->addons->count() > 0)
                                    Addon Booking
                                @else
                                    Unknown
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Packages -->
                @if($booking->packages->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-semibold mb-4">Packages</h2>
                        <div class="space-y-4">
                            @foreach($booking->packages as $package)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold mb-2">{{ $package->name_package }}</h3>
                                    <p class="text-gray-600 mb-2">{{ $package->description }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">Price per package</span>
                                        <span class="font-semibold">Rp {{ number_format($package->price_publish, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Products -->
                @if($booking->products->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-semibold mb-4">Products</h2>
                        <div class="space-y-4">
                            @foreach($booking->products as $product)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold mb-2">{{ $product->name }}</h3>
                                    <p class="text-gray-600 mb-2">{{ $product->description }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">price per product </span>
                                        <span class="font-semibold">Rp {{ number_format($product->pivot->total_price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Add-ons -->
                @if($booking->addons->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-semibold mb-4">Additional Services</h2>
                        <div class="space-y-4">
                            @foreach($booking->addons as $addon)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold mb-2">{{ $addon->addons}}</h3>
                                    <p class="text-gray-600 mb-2">{{ $addon->desc }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-500">Price per service (Qty: {{ $addon->pivot->quantity ?? 1 }})</span>
                                        <span class="font-semibold">Rp {{ number_format($addon->price * ($addon->pivot->quantity ?? 1), 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Guest Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-semibold mb-4">Guest Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Full Name</p>
                            <p class="font-semibold">{{ $booking->booker_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-semibold">{{ $booking->booker_email }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Phone</p>
                            <p class="font-semibold">{{ $booking->booker_telp }}</p>
                        </div>
                        @if($booking->note)
                            <div>
                                <p class="text-sm text-gray-600">Note</p>
                                <p class="font-semibold">{{ $booking->note }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Payment Summary -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">Payment Summary</h3>
                    <div class="space-y-2">
                        @if($booking->packages->count() > 0)
                            <div class="flex justify-between">
                                <span>Packages</span>
                                <span>Rp {{ number_format($booking->packages->sum('price_publish'), 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($booking->products->count() > 0)
                            <div class="flex justify-between">
                                <span>Products</span>
                                <span>Rp {{ number_format($booking->products->sum('pivot.total_price'), 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($booking->addons->count() > 0)
                            <div class="flex justify-between">
                                <span>Add-ons</span>
                                <span>Rp {{ number_format($booking->addons->sum(function($addon) { return $addon->price * ($addon->pivot->quantity ?? 1); }), 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <hr class="my-2">
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span>Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Actions</h3>
                    <div class="space-y-2">
                        @if(in_array($booking->status, ['pending', 'confirmed']))
                            <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">Cancel Booking</button>
                            </form>
                        @endif
                        <a href="{{ route('invoice.download', $booking->id) }}" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition inline-block text-center">Download Invoice</a>

                        <!-- Contact Support Options -->
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600 font-medium">Contact Support:</p>
                            <div class="grid grid-cols-1 gap-2">
                                <a href="mailto:support@otawebsite.com?subject=Support Request - Booking #{{ $booking->id }}&body=Dear Support Team,%0A%0AI need assistance with my booking #%{{ $booking->id }}.%0A%0ABooking Details:%0A- Status: {{ ucfirst(str_replace('_', ' ', $booking->status)) }}%0A- Check-in: {{ $booking->checkin_appointment_start->format('d M Y') }}%0A- Check-out: {{ $booking->checkout_appointment_end->format('d M Y') }}%0A- Total: Rp {{ number_format($booking->total_price, 0, ',', '.') }}%0A%0APlease describe your issue or question below:%0A%0A[Your message here]%0A%0ABest regards,%0A{{ $booking->booker_name }}%0A{{ $booking->booker_email }}"
                                   class="w-full bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition text-center text-sm">
                                    📧 Email Support
                                </a>
                                <a href="{{ route('user.support', $booking->id) }}" class="w-full bg-purple-500 text-white px-4 py-2 rounded-md hover:bg-purple-600 transition text-center text-sm">
                                    📝 Contact Form
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center">
            <p class="text-gray-500">Booking not found.</p>
        </div>
    @endif
</div>
@endsection
