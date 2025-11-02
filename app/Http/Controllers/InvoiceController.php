<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function download($bookingId)
    {
        // Find the booking and ensure it belongs to the authenticated user
        $booking = Booking::with(['packages', 'products', 'addons'])->findOrFail($bookingId);

        // Check if the booking belongs to the current user
        if ($booking->id_user != Auth::id()) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        // Generate PDF from the invoice view
        $pdf = Pdf::loadView('user.invoice', compact('booking'));

        // Download the PDF
        return $pdf->download('invoice_' . $booking->id . '.pdf');
    }
}
