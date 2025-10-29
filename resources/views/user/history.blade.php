@extends('layouts.user')

@section('title', 'Booking History')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Booking History</h1>

    <!-- Booking History -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Recent Bookings</h2>
        </div>

        <div class="divide-y divide-gray-200">
            <!-- Sample Booking 1 -->
            <div class="p-6 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold mb-2">Sample Hotel - Deluxe Room</h3>
                        <p class="text-gray-600 mb-2">Booking ID: #BK001</p>
                        <p class="text-sm text-gray-500">Check-in: 2024-01-15 | Check-out: 2024-01-17</p>
                        <p class="text-sm text-gray-500">Guests: 2 Adults</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                            @if(true) bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">
                            Confirmed
                        </span>
                        <p class="text-lg font-bold text-blue-600 mt-2">$300</p>
                        <a href="{{ route('user.detail_history', ['id' => 1]) }}" class="inline-block mt-2 text-blue-600 hover:underline text-sm">View Details</a>
                    </div>
                </div>
            </div>

            <!-- Sample Booking 2 -->
            <div class="p-6 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold mb-2">Luxury Resort - Suite</h3>
                        <p class="text-gray-600 mb-2">Booking ID: #BK002</p>
                        <p class="text-sm text-gray-500">Check-in: 2024-02-10 | Check-out: 2024-02-15</p>
                        <p class="text-sm text-gray-500">Guests: 4 Adults, 2 Children</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            Pending
                        </span>
                        <p class="text-lg font-bold text-blue-600 mt-2">$1,250</p>
                        <a href="{{ route('user.detail_history', ['id' => 2]) }}" class="inline-block mt-2 text-blue-600 hover:underline text-sm">View Details</a>
                    </div>
                </div>
            </div>

            <!-- Sample Booking 3 -->
            <div class="p-6 hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold mb-2">Budget Hotel - Standard Room</h3>
                        <p class="text-gray-600 mb-2">Booking ID: #BK003</p>
                        <p class="text-sm text-gray-500">Check-in: 2023-12-20 | Check-out: 2023-12-22</p>
                        <p class="text-sm text-gray-500">Guests: 1 Adult</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            Completed
                        </span>
                        <p class="text-lg font-bold text-blue-600 mt-2">$160</p>
                        <a href="{{ route('user.detail_history', ['id' => 3]) }}" class="inline-block mt-2 text-blue-600 hover:underline text-sm">View Details</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-600">Showing 1 to 3 of 3 bookings</p>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50" disabled>Previous</button>
                    <button class="px-3 py-1 bg-blue-500 text-white border border-blue-500 rounded-md text-sm" disabled>1</button>
                    <button class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50" disabled>Next</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
