<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    /**
     * Show the application's landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index() // <--- Ensure this method exists and is spelled correctly
    {   
        if (Auth::check()) {
            return redirect()->route('user.home');
        }
        return view('user.landing'); // Replace 'landing' with your actual view name
    }
}