@component('mail::message')
# New Booking Received

A new booking has been created in the system.

## Booking Details

**Booking Code:** {{ $booking->booking_code }}  
**Customer Name:** {{ $booking->user->name ?? 'N/A' }}  
**Customer Email:** {{ $booking->user->email ?? 'N/A' }}  
**Booking Date:** {{ $booking->created_at->format('d M Y H:i') }}

@if($booking->packages && $booking->packages->count() > 0)
## Package(s)
@foreach($booking->packages as $package)
- {{ $package->name_package }} (Qty: {{ $package->pivot->quantity ?? 1 }})
@endforeach
@endif

@if($booking->products && $booking->products->count() > 0)
## Product(s)
@foreach($booking->products as $product)
- {{ $product->name }} (Qty: {{ $product->pivot->quantity ?? 1 }})
@endforeach
@endif

@if($booking->addons && $booking->addons->count() > 0)
## Addon(s)
@foreach($booking->addons as $addon)
- {{ $addon->addons }} (Qty: {{ $addon->pivot->quantity ?? 1 }})
@endforeach
@endif

**Total Amount:** Rp {{ number_format($booking->total_price, 0, ',', '.') }}  
**Payment Status:** {{ ucfirst($booking->payment_status) }}  
**Booking Status:** {{ ucfirst($booking->status) }}

@component('mail::button', ['url' => route('super_admin.dashboard')])
View Dashboard
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
