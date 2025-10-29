@extends('layouts.user')

@section('title', 'Booking Details')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="mb-6">
        <a href="{{ route('user.history') }}" class="text-blue-600 hover:underline">&larr; Back to History</a>
    </div>

    <h1 class="text-3xl font-bold text-gray-800 mb-6">Booking Details</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Booking Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold mb-4">Booking Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Booking ID</p>
                        <p class="font-semibold">#BK001</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Confirmed</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Check-in</p>
                        <p class="font-semibold">January 15, 2024</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Check-out</p>
                        <p class="font-semibold">January 17, 2024</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Guests</p>
                        <p class="font-semibold">2 Adults</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Nights</p>
                        <p class="font-semibold">2 nights</p>
                    </div>
                </div>
            </div>

            <!-- Hotel Details -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold mb-4">Hotel Details</h2>
                <div class="flex items-start space-x-4">
                    <img src="https://via.placeholder.com/150x100" alt="Hotel" class="w-24 h-16 object-cover rounded-md">
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold mb-2">Sample Hotel</h3>
                        <p class="text-gray-600 mb-2">123 Main Street, City, Country</p>
                        <p class="text-gray-600">Deluxe Room with City View</p>
                    </div>
                </div>
            </div>

            <!-- Guest Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold mb-4">Guest Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Full Name</p>
                        <p class="font-semibold">{{ auth()->user()->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-semibold">{{ auth()->user()->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Phone</p>
                        <p class="font-semibold">{{ auth()->user()->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Special Requests</p>
                        <p class="font-semibold">Late check-in preferred</p>
                    </div>
                </div>
            </div>

            <!-- Add-ons -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-semibold mb-4">Additional Services</h2>
                <div class="space-y-2">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span>Breakfast (2 days)</span>
                        <span class="font-semibold">$40</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span>Spa Treatment</span>
                        <span class="font-semibold">$50</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="font-semibold">Total Add-ons</span>
                        <span class="font-semibold">$90</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Payment Summary -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Payment Summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Room (2 nights)</span>
                        <span>$300</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Add-ons</span>
                        <span>$90</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Service Fee</span>
                        <span>$15</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tax</span>
                        <span>$48</span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span>$453</span>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Paid</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Actions</h3>
                <div class="space-y-2">
                    <button class="w-full bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">Download Invoice</button>
                    <button class="w-full bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">Contact Support</button>
                    <button class="w-full bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">Cancel Booking</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
