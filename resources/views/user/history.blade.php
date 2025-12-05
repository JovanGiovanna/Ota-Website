@extends('layouts.user')

@section('title', 'Booking History')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Booking History</h1>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Recent Bookings</h2>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($bookings as $booking)
            @php
                $types = [];

                // --- Packages ---
                if($booking->packages && $booking->packages->count()) {
                    foreach($booking->packages as $package) {
                        $hasAddons = $package->bookPackageAddons->isNotEmpty();
                        $types[] = $hasAddons ? 'Package + Addon' : 'Package';
                    }
                }

                // --- Products ---
                if($booking->products && $booking->products->count()) {
                    foreach($booking->products as $product) {
                        $hasAddons = $product->bookProductAddons->isNotEmpty();
                        $types[] = $hasAddons ? 'Product + Addon' : 'Product';
                    }
                }

                // --- Standalone Addons ---
                if($booking->addons && $booking->addons->count()) {
                    $types[] = 'Addon';
                }

                if(empty($types)) {
                    $types[] = 'Unknown Booking Type';
                }

                $types = array_unique($types);
            @endphp

            <div class="p-6 hover:bg-gray-50 flex justify-between items-start">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold mb-2">
                        {{ count($types) > 1 ? 'Mixed Booking (' . implode(', ', $types) . ')' : $types[0] }}
                    </h3>

                    <p class="text-gray-600 mb-2">
                        Booking ID: {{ $booking->booking_code ?? '#' . strtoupper(substr($booking->id, 0, 8)) }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Check-in: {{ optional($booking->checkin_appointment_start)->format('d M Y') }} |
                        Check-out: {{ optional($booking->checkout_appointment_end)->format('d M Y') }}
                    </p>
                    <p class="text-sm text-gray-500">Duration: {{ $booking->duration_days ?? 0 }} days</p>

                    <div class="mt-2 text-sm text-gray-600">

                        {{-- Packages --}}
                        @if($booking->packages && $booking->packages->count())
                            <p class="font-medium">Packages:</p>
                            <ul class="ml-4 list-disc">
@foreach($booking->packages as $bookPackage)
    @php
        $durationDays = $booking->duration_days ?? 1;
        $pricePerNight = $bookPackage->package->nta ?? 0;
        $totalPricePackage = $pricePerNight * $durationDays;
        $packageAddons = $bookPackage->bookPackageAddons ?? collect();
    @endphp
    <li>
        {{ $bookPackage->package->name_package ?? 'Package' }}
        <span class="text-gray-600 ml-2">
            Rp {{ number_format($pricePerNight, 0, ',', '.') }} x {{ $durationDays }} days = Rp {{ number_format($totalPricePackage, 0, ',', '.') }}
        </span>
        @if($packageAddons->count())
            <ul class="ml-4 list-disc text-gray-500 text-sm">
                @foreach($packageAddons as $pAddon)
                    @php
                        $addonPrice = $pAddon->addon?->finalPrice ?? 0;
                        $addonQty = $pAddon->quantity ?? 1;
                    @endphp
                    <li>
                        {{ $pAddon->addon?->name ?? 'Addon' }} (x{{ $addonQty }})
                        <span class="ml-1">
                            - Rp {{ number_format($addonPrice * $addonQty, 0, ',', '.') }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </li>
@endforeach
                            </ul>
                        @endif

                        {{-- Products --}}
                        @if($booking->products && $booking->products->count())
                            <p class="font-medium mt-2">Products:</p>
                            <ul class="ml-4 list-disc">
                                @foreach($booking->products as $bookProduct)
                                    @php
                                        $amount = $bookProduct->amount ?? 1;
                                        $unitPrice = $bookProduct->product->finalPrice ?? 0;
                                        $productAddons = $bookProduct->bookProductAddons ?? collect();
                                    @endphp
                                    <li>
                                        {{ $bookProduct->product->name ?? 'Product' }}
                                        <span class="text-gray-600 ml-2">
                                            Rp {{ number_format($unitPrice, 0, ',', '.') }} x {{ $amount }} = Rp {{ number_format($unitPrice * $amount, 0, ',', '.') }}
                                        </span>
                                        @if($productAddons->count())
                                            <ul class="ml-4 list-disc text-gray-500 text-sm">
                                                @foreach($productAddons as $prAddon)
                                                    @php
                                                        $addonPrice = $prAddon->addon?->finalPrice ?? 0;
                                                        $addonQty = $prAddon->quantity ?? 1;
                                                    @endphp
                                                    <li>
                                                        {{ $prAddon->addon?->name ?? 'Addon' }} (x{{ $addonQty }})
                                                        <span class="ml-1">
                                                            - Rp {{ number_format($addonPrice * $addonQty, 0, ',', '.') }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        {{-- Standalone Addons --}}
                        @if($booking->addons && $booking->addons->count())
                            <p class="font-medium mt-2">Standalone Add-ons:</p>
                            <ul class="ml-4 list-disc">
                                @foreach($booking->addons as $bookAddon)
                                    @php
                                        $amount = $bookAddon->amount ?? 1;
                                        $unitPriceAddon = $bookAddon->addon->finalPrice ?? 0;
                                        $totalPrice = $unitPriceAddon * $amount;
                                    @endphp
                                    <li>
                                        {{ $bookAddon->addon?->name ?? 'Addon' }} (x{{ $amount }})
                                        <span class="ml-1">
                                            - Rp {{ number_format($unitPriceAddon, 0, ',', '.') }} x {{ $amount }} = Rp {{ number_format($totalPrice, 0, ',', '.') }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                    </div>
                </div>

                {{-- Right panel --}}
                <div class="text-right">
                    @php
                        $durationDays = $booking->duration_days ?? 1;
                        $totalPrice = 0;
                        
                        foreach ($booking->packages as $bookPackage) {
                            $pricePerNight = $bookPackage->package->final_price ?? 0;
                            $totalPrice += $pricePerNight * $durationDays;
                            
                            foreach ($bookPackage->bookPackageAddons as $pAddon) {
                                $addonPrice = $pAddon->addon?->finalPrice ?? 0;
                                $addonQty = $pAddon->quantity ?? 1;
                                $totalPrice += $addonPrice * $addonQty;
                            }
                        }
                        
                        foreach ($booking->products as $bookProduct) {
                            $unitPrice = $bookProduct->product->finalPrice ?? 0;
                            $amount = $bookProduct->amount ?? 1;
                            $totalPrice += $unitPrice * $amount;
                            
                            foreach ($bookProduct->bookProductAddons as $prAddon) {
                                $addonPrice = $prAddon->addon?->finalPrice ?? ($prAddon->price ?? 0);
                                $addonQty = $prAddon->quantity ?? 1;
                                $totalPrice += $addonPrice * $addonQty;
                            }
                        }
                        
                        foreach ($booking->addons as $bookAddon) {
                            $unitPriceAddon = $bookAddon->addon->finalPrice ?? 0;
                            $amount = $bookAddon->amount ?? 1;
                            $totalPrice += $unitPriceAddon * $amount;
                        }
                    @endphp
                    
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                        @if($booking->status == 'confirmed') bg-green-100 text-green-800
                        @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($booking->status == 'completed') bg-blue-100 text-blue-800
                        @elseif($booking->status == 'checked_in') bg-purple-100 text-purple-800
                        @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                    <p class="text-lg font-bold text-blue-600 mt-2">
                        Rp {{ number_format($totalPrice, 0, ',', '.') }}
                    </p>
                    <a href="{{ route('user.detail_history', $booking) }}" class="inline-block mt-2 text-blue-600 hover:underline text-sm">
                        View Details
                    </a>
                </div>
            </div>
            @empty
            <div class="p-6 text-center text-gray-500">
                <p>No bookings found.</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-600">Showing {{ $bookings->count() }} bookings</p>
                <div class="flex space-x-2">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
