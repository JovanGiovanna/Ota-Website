@extends('layouts.user')

@section('title', 'Contact Support')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="mb-6">
        <a href="{{ route('user.detail_history', $booking->id) }}" class="text-blue-600 hover:underline">&larr; Back to Booking Details</a>
    </div>

    <h1 class="text-3xl font-bold text-gray-800 mb-6">Contact Support</h1>

    <div class="max-w-4xl mx-auto">
        <!-- Booking Summary -->
        <div class="bg-blue-50 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Booking Summary</h2>
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
                    <p class="text-sm text-gray-600">Total Amount</p>
                    <p class="font-semibold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Support Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">How can we help you?</h2>
            <p class="text-gray-600 mb-6">Please fill out the form below and our support team will get back to you as soon as possible.</p>

            <form action="{{ route('user.support.submit', $booking->id) }}" method="POST" class="space-y-6">
                @csrf

                <!-- Subject -->
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                    <select name="subject" id="subject" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select a subject</option>
                        <option value="booking_modification">Booking Modification</option>
                        <option value="payment_issue">Payment Issue</option>
                        <option value="cancellation">Cancellation Request</option>
                        <option value="technical_issue">Technical Issue</option>
                        <option value="general_inquiry">General Inquiry</option>
                        <option value="complaint">Complaint</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select name="priority" id="priority"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="low">Low</option>
                        <option value="normal" selected>Normal</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>

                <!-- Message -->
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                    <textarea name="message" id="message" rows="6" required
                              placeholder="Please describe your issue or question in detail..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <!-- Contact Information (pre-filled) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">Your Name</label>
                        <input type="text" name="contact_name" id="contact_name" value="{{ $booking->booker_name }}"
                               readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                    </div>
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Your Email</label>
                        <input type="email" name="contact_email" id="contact_email" value="{{ $booking->booker_email }}"
                               readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600 transition">
                        Submit Support Request
                    </button>
                </div>
            </form>
        </div>

        <!-- Alternative Contact Methods -->
        <div class="bg-gray-50 rounded-lg p-6 mt-6">
            <h3 class="text-lg font-semibold mb-4">Alternative Contact Methods</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-medium text-gray-800 mb-2">📧 Email Support</h4>
                    <p class="text-sm text-gray-600 mb-2">Send us an email directly</p>
                    <a href="mailto:support@otawebsite.com?subject=Support Request - Booking #{{ $booking->id }}"
                       class="text-blue-600 hover:underline text-sm">support@otawebsite.com</a>
                </div>
                <div>
                    <h4 class="font-medium text-gray-800 mb-2">📞 Phone Support</h4>
                    <p class="text-sm text-gray-600 mb-2">Call us during business hours</p>
                    <p class="text-sm text-gray-800">+62 123 456 7890</p>
                    <p class="text-xs text-gray-500">Mon-Fri: 9AM-6PM WIB</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
