<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Package;  // Import Package model

class LandingController extends Controller
{
    /**
     * Show the application's landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request) // Accept request for pagination params
    {   
        if (Auth::check()) {
            return redirect()->route('user.home');
        }

        // Popular packages: oldest to newest, paginate 8 with custom page query param
        $popularPackages = Package::orderBy('created_at', 'asc')->paginate(8, ['*'], 'popular_page');

        // Newest packages: newest to oldest, paginate 8 with custom page query param
        $newestPackages = Package::orderBy('created_at', 'desc')->paginate(8, ['*'], 'newest_page');

        return view('user.landing', compact('popularPackages', 'newestPackages'));
    }
}
