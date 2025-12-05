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
                            <p class="font-semibold">{{ $booking->booking_code ?? '#' . strtoupper(substr((string)$booking->id, 0, 8)) }}</p>
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
                            <p class="font-semibold">{{ $booking->checkin_appointment_start ? $booking->checkin_appointment_start->format('d M Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Check-out</p>
                            <p class="font-semibold">{{ $booking->checkout_appointment_end ? $booking->checkout_appointment_end->format('d M Y') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Duration</p>
                            <p class="font-semibold">{{ $booking->duration_days ?? '-' }} days</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Booking Type</p>
                            <p class="font-semibold">
                                @if(optional($booking->packages)->count() > 0 && optional($booking->products)->count() > 0 && optional($booking->addons)->count() > 0)
                                    Mixed Booking
                                @elseif(optional($booking->packages)->count() > 0)
                                    Package Booking
                                @elseif(optional($booking->products)->count() > 0)
                                    Product Booking
                                @elseif(optional($booking->addons)->count() > 0)
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
                            @foreach($booking->packages as $bookPackage)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-20 h-20 flex-shrink-0">
                                            @if($bookPackage->package->images && count($bookPackage->package->images) > 0)
                                                <img src="{{ asset('storage/' . $bookPackage->package->images[0]) }}" alt="{{ $bookPackage->package->name_package }}" class="w-full h-full object-cover rounded-lg">
                                            @else
                                                <div class="w-full h-full bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-box text-gray-400 text-2xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold mb-2">{{ $bookPackage->package->name_package }}</h3>
                                            <p class="text-gray-600 mb-2">{{ $bookPackage->package->description }}</p>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-500">Total price for package</span>
                                                <span class="font-semibold">Rp {{ number_format($bookPackage->package->nta * $booking->duration_days, 0, ',', '.') }}</span>
                                            </div>
                                            <!-- Package Add-ons -->
                                            @if($bookPackage->bookPackageAddons->count() > 0)
                                                <div class="mt-4 pt-4 border-t border-gray-200">
                                                    <h4 class="text-md font-semibold mb-2">Additional Services for this Package:</h4>
                                                    <div class="space-y-2">
                                                        @foreach($bookPackage->bookPackageAddons as $packageAddon)
                                                            <div class="flex justify-between items-center text-sm">
                                                                <span>{{ $packageAddon->addon->addons ?? 'Unnamed Addon' }} (Qty: {{ $packageAddon->quantity ?? 1 }})</span>
                                                                <span>Rp {{ number_format($packageAddon->addon->finalPrice * ($packageAddon->quantity ?? 1), 0, ',', '.') }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
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
                            @foreach($booking->products as $bookProduct)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-20 h-20 flex-shrink-0">
                                            @if($bookProduct->product->images && count($bookProduct->product->images) > 0)
                                                <img src="{{ asset('storage/' . $bookProduct->product->images[0]) }}" alt="{{ $bookProduct->product->name }}" class="w-full h-full object-cover rounded-lg">
                                            @else
                                                <div class="w-full h-full bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold mb-2">{{ $bookProduct->product->name }}</h3>
                                            <p class="text-gray-600 mb-2">{{ $bookProduct->product->description }}</p>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-500">Total price for product (Qty: {{ $bookProduct->amount }})</span>
                                                <span class="font-semibold">Rp {{ number_format($bookProduct->product->finalPrice * $bookProduct->amount, 0, ',', '.') }}</span>
                                            </div>
                                            <!-- Product Add-ons -->
                                            @if($bookProduct->bookProductAddons->count() > 0)
                                                <div class="mt-4 pt-4 border-t border-gray-200">
                                                    <h4 class="text-md font-semibold mb-2">Additional Services for this Product:</h4>
                                                    <div class="space-y-2">
                                                        @foreach($bookProduct->bookProductAddons as $productAddon)
                                                            <div class="flex justify-between items-center text-sm">
                                                                <span>{{ $productAddon->addon->addons ?? 'Unnamed Addon' }} (Qty: {{ $productAddon->quantity ?? 1 }})</span>
                                                                <span>Rp {{ number_format($productAddon->addon->finalPrice * ($productAddon->quantity ?? 1), 0, ',', '.') }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Add-ons -->
                @if(optional($booking->addons)->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-semibold mb-4">Additional Services</h2>
                        <div class="space-y-4">
                            @foreach($booking->addons as $bookAddon)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-20 h-20 flex-shrink-0">
                                            @if($bookAddon->addon->images && count($bookAddon->addon->images) > 0)
                                                <img src="{{ asset('storage/' . $bookAddon->addon->images[0]) }}" alt="{{ $bookAddon->addon->addons ?? 'Addon image' }}" class="w-full h-full object-cover rounded-lg">
                                            @else
                                                <div class="w-full h-full bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-plus-circle text-gray-400 text-2xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold mb-2">{{ $bookAddon->addon->addons ?? 'Unnamed Addon' }}</h3>
                                            <p class="text-gray-600 mb-2">{{ $bookAddon->addon->desc ?? '' }}</p>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-500">Total price for service (Qty: {{ $bookAddon->amount }})</span>
                                                <span class="font-semibold">Rp {{ number_format($bookAddon->addon->finalPrice * $bookAddon->amount, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
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
                        @php
                            $totalPackages = 0;
                            $totalProducts = 0;
                            $totalAddons = 0;
                            $grandTotal = 0;
                        @endphp
                        @if(optional($booking->packages)->count() > 0)
                            @php
                                $totalPackages = $booking->packages->sum(function($bookPackage) use ($booking) {
                                    return $bookPackage->package->nta * $booking->duration_days;
                                });
                                $grandTotal += $totalPackages;
                            @endphp
                            <div class="flex justify-between">
                                <span>Packages ({{ optional($booking->packages)->count() }} item{{ optional($booking->packages)->count() > 1 ? 's' : '' }})</span>
                                <span>Rp {{ number_format($totalPackages, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if(optional($booking->products)->count() > 0)
                            @php
                                $totalProducts = $booking->products->sum(function($bookProduct) {
                                    return $bookProduct->product->finalPrice * $bookProduct->amount;
                                });
                                $grandTotal += $totalProducts;
                            @endphp
                            <div class="flex justify-between">
                                <span>Products ({{ optional($booking->products)->sum('amount') }} item{{ optional($booking->products)->sum('amount') > 1 ? 's' : '' }})</span>
                                <span>Rp {{ number_format($totalProducts, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @php
                            $totalAddons = 0;
                            // Standalone addons
                            if (optional($booking->addons)->count() > 0) {
                                $totalAddons += $booking->addons->sum(function($bookAddon) {
                                    return $bookAddon->addon->finalPrice * $bookAddon->amount;
                                });
                            }
                            // Package addons
                            if (optional($booking->packages)->count() > 0) {
                                foreach ($booking->packages as $bookPackage) {
                                    if ($bookPackage->bookPackageAddons->count() > 0) {
                                        $totalAddons += $bookPackage->bookPackageAddons->sum(function($packageAddon) {
                                            return $packageAddon->addon->finalPrice * ($packageAddon->quantity ?? 1);
                                        });
                                    }
                                }
                            }
                            // Product addons
                            if (optional($booking->products)->count() > 0) {
                                foreach ($booking->products as $bookProduct) {
                                    if ($bookProduct->bookProductAddons->count() > 0) {
                                        $totalAddons += $bookProduct->bookProductAddons->sum(function($productAddon) {
                                            return $productAddon->addon->finalPrice * ($productAddon->quantity ?? 1);
                                        });
                                    }
                                }
                            }
                            $grandTotal += $totalAddons;
                        @endphp
                        @if($totalAddons > 0)
                            <div class="flex justify-between">
                                <span>Add-ons ({{ $totalAddons > 0 ? 'Included' : '0' }} item{{ $totalAddons > 1 ? 's' : '' }})</span>
                                <span>Rp {{ number_format($totalAddons, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <hr class="my-2">
                        <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Actions</h3>
                    <div class="space-y-2">
                        @if($booking->status == 'book')
                            <a href="{{ route('user.payment', $booking->id) }}" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition inline-block text-center font-semibold">
                                💳 Continue Payment
                            </a>
                        @endif
                        @if(in_array($booking->status, ['pending', 'confirmed']))
                            <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">Cancel Booking</button>
                            </form>
                        @endif
                        @if(in_array($booking->status, ['paid', 'cancelled']))
                            <form action="{{ route('user.payment.request_refund', $booking->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin meminta pengembalian dana untuk booking ini?');">
                                @csrf
                                <button type="submit" class="w-full bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 transition">Request Refund</button>
                            </form>
                        @endif
                        <a href="{{ route('invoice.download', $booking->id) }}" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition inline-block text-center">Download Invoice</a>

                        <!-- Contact Support Options -->
                        <div class="space-y-2">
                            <p class="text-sm text-gray-600 font-medium">Contact Support:</p>
                            <div class="grid grid-cols-1 gap-2">
                            <a href="mailto:support@otawebsite.com?subject=Support Request - Booking #{{ $booking->id }}&body=Dear Support Team,%0A%0AI need assistance with my booking #{{ $booking->id }}.%0A%0ABooking Details:%0A- Status: {{ ucfirst(str_replace('_', ' ', $booking->status)) }}%0A- Check-in: {{ $booking->checkin_appointment_start ? $booking->checkin_appointment_start->format('d M Y') : '-' }}%0A- Check-out: {{ $booking->checkout_appointment_end ? $booking->checkout_appointment_end->format('d M Y') : '-' }}%0A- Total: Rp {{ number_format($booking->total_price ?? 0, 0, ',', '.') }}%0A%0APlease describe your issue or question below:%0A%0A[Your message here]%0A%0ABest regards,%0A{{ $booking->booker_name }}%0A{{ $booking->booker_email }}"
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

        <!-- Review Section -->
        @if($booking->status === 'completed')
            <div class="mt-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-semibold mb-6">Review & Rating</h2>

                    @php
                        $userReviews = $booking->reviews->where('user_id', auth()->id());
                        $reviewedItems = [];
                        if ($userReviews->count() > 0) {
                            foreach ($userReviews as $review) {
                                if ($review->package_id) $reviewedItems[] = 'package-' . $review->package_id;
                                if ($review->product_id) $reviewedItems[] = 'product-' . $review->product_id;
                                if ($review->addon_id) $reviewedItems[] = 'addon-' . $review->addon_id;
                            }
                        }
                        $totalItems = $booking->packages->count() + $booking->products->count() + $booking->addons->count();
                    @endphp

                    @if($userReviews->count() > 0)
                        <!-- Show existing reviews -->
                        <div class="space-y-4 mb-6">
                            @foreach($userReviews as $userReview)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold text-green-800 mb-2">
                                        Your Review for
                                        @if($userReview->package)
                                            {{ $userReview->package->name_package }} (Package)
                                        @elseif($userReview->product)
                                            {{ $userReview->product->name }} (Product)
                                        @elseif($userReview->addon)
                                            {{ $userReview->addon->addons }} (Addon)
                                        @endif
                                    </h3>
                                    <div class="flex items-center mb-2">
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $userReview->rating)
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600">{{ $userReview->rating }}/5 stars</span>
                                    </div>
                                    @if($userReview->comment)
                                        <p class="text-gray-700">{{ $userReview->comment }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500 mt-2">Reviewed on {{ $userReview->created_at->format('d M Y') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(count($reviewedItems) < $totalItems)
                        <!-- Review Form -->
                        <form action="{{ route('reviews.store', $booking->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="item_id" id="item_id" value="">

                            <!-- Item Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">What would you like to review?</label>
                                <div class="space-y-2">
                                    @if($booking->packages->count() > 0)
                                        @foreach($booking->packages as $bookPackage)
                                            @if(!in_array('package-' . $bookPackage->package->id, $reviewedItems))
                                                <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                    <input type="radio" name="review_type" value="package" data-id="{{ $bookPackage->package->id }}" class="text-blue-600 focus:ring-blue-500" required>
                                                    <div class="flex items-center space-x-3">
                                                        @if($bookPackage->package->images && count($bookPackage->package->images) > 0)
                                                            <img src="{{ asset('storage/' . $bookPackage->package->images[0]) }}" alt="{{ $bookPackage->package->name_package }}" class="w-12 h-12 object-cover rounded-lg">
                                                        @else
                                                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                                <i class="fas fa-box text-gray-400"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <span class="font-medium text-gray-800">{{ $bookPackage->package->name_package }}</span>
                                                            <span class="text-sm text-gray-500 block">Package</span>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endif
                                        @endforeach
                                    @endif

                                    @if($booking->products->count() > 0)
                                        @foreach($booking->products as $bookProduct)
                                            @if(!in_array('product-' . $bookProduct->product->id, $reviewedItems))
                                                <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                    <input type="radio" name="review_type" value="product" data-id="{{ $bookProduct->product->id }}" class="text-blue-600 focus:ring-blue-500">
                                                    <div class="flex items-center space-x-3">
                                                        @if($bookProduct->product->images && count($bookProduct->product->images) > 0)
                                                            <img src="{{ asset('storage/' . $bookProduct->product->images[0]) }}" alt="{{ $bookProduct->product->name }}" class="w-12 h-12 object-cover rounded-lg">
                                                        @else
                                                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                                <i class="fas fa-shopping-cart text-gray-400"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <span class="font-medium text-gray-800">{{ $bookProduct->product->name }}</span>
                                                            <span class="text-sm text-gray-500 block">Product (Qty: {{ $bookProduct->amount }})</span>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endif
                                        @endforeach
                                    @endif

                                    @if($booking->addons->count() > 0)
                                        @foreach($booking->addons as $bookAddon)
                                            @if(!in_array('addon-' . $bookAddon->addon->id, $reviewedItems))
                                                <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                    <input type="radio" name="review_type" value="addon" data-id="{{ $bookAddon->addon->id }}" class="text-blue-600 focus:ring-blue-500">
                                                    <div class="flex items-center space-x-3">
                                                        @if($bookAddon->addon->images && count($bookAddon->addon->images) > 0)
                                                            <img src="{{ asset('storage/' . $bookAddon->addon->images[0]) }}" alt="{{ $bookAddon->addon->addons }}" class="w-12 h-12 object-cover rounded-lg">
                                                        @else
                                                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                                <i class="fas fa-plus-circle text-gray-400"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <span class="font-medium text-gray-800">{{ $bookAddon->addon->addons }}</span>
                                                            <span class="text-sm text-gray-500 block">Addon (Qty: {{ $bookAddon->amount }})</span>
                                                        </div>
                                                    </div>
                                                </label>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                <div class="flex space-x-1 star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <input type="radio" id="rating-{{ $i }}" name="rating" value="{{ $i }}" class="sr-only" required>
                                        <label for="rating-{{ $i }}" class="cursor-pointer text-gray-300 hover:text-yellow-400 star" data-rating="{{ $i }}">
                                            <i class="fas fa-star text-2xl"></i>
                                        </label>
                                    @endfor
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Click on a star to rate</p>
                            </div>

                            <div>
                                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Comment (Optional)</label>
                                <textarea id="comment" name="comment" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Share your experience..."></textarea>
                            </div>

                            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 transition">
                                Submit Review
                            </button>
                        </form>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const stars = document.querySelectorAll('.star-rating .star');
                                const radioButtons = document.querySelectorAll('.star-rating input[type="radio"]');

                                function updateStars(rating) {
                                    stars.forEach((star, index) => {
                                        if (index < rating) {
                                            star.classList.remove('text-gray-300');
                                            star.classList.add('text-yellow-400');
                                        } else {
                                            star.classList.remove('text-yellow-400');
                                            star.classList.add('text-gray-300');
                                        }
                                    });
                                }

                                stars.forEach((star, index) => {
                                    star.addEventListener('click', function() {
                                        const rating = parseInt(this.dataset.rating);
                                        // Update radio button
                                        radioButtons[rating - 1].checked = true;
                                        // Update visual stars
                                        updateStars(rating);
                                    });

                                    star.addEventListener('mouseenter', function() {
                                        const rating = parseInt(this.dataset.rating);
                                        updateStars(rating);
                                    });
                                });

                                // Reset on mouse leave if no rating selected
                                document.querySelector('.star-rating').addEventListener('mouseleave', function() {
                                    const checkedRadio = document.querySelector('.star-rating input[type="radio"]:checked');
                                    if (checkedRadio) {
                                        updateStars(parseInt(checkedRadio.value));
                                    } else {
                                        updateStars(0);
                                    }
                                });

                                // Initialize with any pre-selected rating
                                const checkedRadio = document.querySelector('.star-rating input[type="radio"]:checked');
                                if (checkedRadio) {
                                    updateStars(parseInt(checkedRadio.value));
                                }

                                // Set item_id when radio button is selected
                                const radioInputs = document.querySelectorAll('input[name="review_type"]');
                                radioInputs.forEach(radio => {
                                    radio.addEventListener('change', function() {
                                        document.getElementById('item_id').value = this.getAttribute('data-id');
                                    });
                                });
                            });
                        </script>
                    @endif
                </div>
            </div>
        @endif
    @else
        <div class="text-center">
            <p class="text-gray-500">Booking not found.</p>
        </div>
    @endif
</div>
@endsection
