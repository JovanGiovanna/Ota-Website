<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, $bookingId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $booking = Booking::with('packages', 'bookProducts', 'bookPackageAddons')->findOrFail($bookingId);

        // Check if user owns the booking
        if ($booking->id_user !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only review your own bookings.');
        }

        // Check if booking is completed (you might want to adjust this condition)
        if ($booking->status !== 'completed') {
            return redirect()->back()->with('error', 'You can only review completed bookings.');
        }

        // Check if review already exists
        $existingReview = Review::where('user_id', Auth::id())
            ->where('booking_id', $bookingId)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'You have already reviewed this booking.');
        }

        // Get the first package ID from the booking's packages relation
        $packageId = $booking->packages->first()->id ?? null;

        // Get the first product ID from the booking's products relation
        $productId = $booking->bookProducts->first()->id_product ?? null;

        // Get the first addon ID from the booking's addons relation
        $addonId = $booking->bookPackageAddons->first()->id_addons ?? null;

        Review::create([
            'user_id' => Auth::id(),
            'booking_id' => $bookingId,
            'package_id' => $packageId,
            'product_id' => $productId,
            'addon_id' => $addonId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully!');
    }

    /**
     * Update the specified review in storage.
     */
    public function update(Request $request, Review $review)
    {
        // Check if user owns the review
        if ($review->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only edit your own reviews.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Review updated successfully!');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review)
    {
        // Check if user owns the review
        if ($review->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only delete your own reviews.');
        }

        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully!');
    }
}
