<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorInfo;
use App\Models\Booking;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function profile()
    {
        $vendor = Auth::guard('vendor')->user();
        $vendorInfo = VendorInfo::where('vendor_id', $vendor->id)->first();

        return view('vendor.profile', compact('vendor', 'vendorInfo'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:vendor,email,' . Auth::guard('vendor')->id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $vendor = Auth::guard('vendor')->user();
        $vendor->update($request->only(['name', 'email']));

        VendorInfo::updateOrCreate(
            ['vendor_id' => $vendor->id],
            $request->only(['phone', 'address', 'description'])
        );

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    public function index()
    {
        $vendors = Vendor::paginate(10);

        return view('super_admin.vendors', compact('vendors'));
    }

    public function bookings()
    {
        $vendor = Auth::guard('vendor')->user();
        $bookings = Booking::where('vendor_id', $vendor->id)->with('user')->paginate(10);

        return view('vendor.bookings', compact('bookings'));
    }

    public function services()
    {
        $vendor = Auth::guard('vendor')->user();
        // Assuming services are related to vendor
        $services = []; // Replace with actual service model query

        return view('vendor.services', compact('services'));
    }

    public function storeService(Request $request)
    {
        // Implement service creation logic
        return redirect()->back()->with('success', 'Service created successfully');
    }

    public function updateService(Request $request, $serviceId)
    {
        // Implement service update logic
        return redirect()->back()->with('success', 'Service updated successfully');
    }

    public function deleteService($serviceId)
    {
        // Implement service deletion logic
        return redirect()->back()->with('success', 'Service deleted successfully');
    }

    public function pricing()
    {
        $vendor = Auth::guard('vendor')->user();
        // Assuming pricing data
        $pricing = []; // Replace with actual pricing model query

        return view('vendor.pricing', compact('pricing'));
    }

    public function updatePricing(Request $request)
    {
        // Implement pricing update logic
        return redirect()->back()->with('success', 'Pricing updated successfully');
    }

    public function availability()
    {
        $vendor = Auth::guard('vendor')->user();
        // Assuming availability data
        $availability = []; // Replace with actual availability model query

        return view('vendor.availability', compact('availability'));
    }

    public function updateAvailability(Request $request)
    {
        // Implement availability update logic
        return redirect()->back()->with('success', 'Availability updated successfully');
    }

    public function analytics()
    {
        $vendor = Auth::guard('vendor')->user();
        // Assuming analytics data
        $analytics = [
            'totalBookings' => Booking::where('vendor_id', $vendor->id)->count(),
            'totalRevenue' => Booking::where('vendor_id', $vendor->id)->sum('total_price'),
            'activeServices' => 0, // Replace with actual count
            'averageRating' => 0, // Replace with actual calculation
        ];

        return view('vendor.analytics', compact('analytics'));
    }
}
