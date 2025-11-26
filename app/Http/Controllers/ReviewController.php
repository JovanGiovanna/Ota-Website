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
            'review_type' => 'required|in:package,product,addon',
            'item_id' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $booking = Booking::with('packages', 'products', 'bookPackageAddons', 'addons')->findOrFail($bookingId);

        // Check if user owns the booking
        if ($booking->id_user !== Auth::id()) {
            return redirect()->back()->with('error', 'You can only review your own bookings.');
        }

        // Check if booking is completed
        if ($booking->status !== 'completed') {
            return redirect()->back()->with('error', 'You can only review completed bookings.');
        }

        // Check if review already exists for this user, booking, and specific item
        $existingReview = Review::where('user_id', Auth::id())
            ->where('booking_id', $bookingId)
            ->where(function ($query) use ($request) {
                switch ($request->review_type) {
                    case 'package':
                        $query->where('package_id', $request->item_id);
                        break;
                    case 'product':
                        $query->where('product_id', $request->item_id);
                        break;
                    case 'addon':
                        $query->where('addon_id', $request->item_id);
                        break;
                }
            })
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'You have already reviewed this item.');
        }

        // Validate that the item exists in the booking
        $itemExists = false;
        switch ($request->review_type) {
            case 'package':
                $itemExists = $booking->packages->contains('package.id', $request->item_id);
                break;
            case 'product':
                $itemExists = $booking->products->contains('product.id', $request->item_id);
                break;
            case 'addon':
                $itemExists = $booking->addons->contains('addon.id', $request->item_id);
                break;
        }

        if (!$itemExists) {
            return redirect()->back()->with('error', 'Item not found in this booking.');
        }

        // Initialize IDs
        $packageId = null;
        $productId = null;
        $addonId = null;

        // Set the appropriate ID based on review_type and item_id
        switch ($request->review_type) {
            case 'package':
                $packageId = $request->item_id;
                break;
            case 'product':
                $productId = $request->item_id;
                break;
            case 'addon':
                $addonId = $request->item_id;
                break;
        }

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
