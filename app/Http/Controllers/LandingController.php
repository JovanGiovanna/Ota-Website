<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Show the application's landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index() // <--- Ensure this method exists and is spelled correctly
    {
        return view('landing'); // Replace 'landing' with your actual view name
    }
}