<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Category;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // -------------------------
        // Stats Cards
        // -------------------------
        $totalKategori = Category::count();
        $totalBooking = Booking::count();
        $totalUsers = User::count();
        $totalRevenue = Booking::sum('total_price');

        // -------------------------
        // 1️⃣ Booking Kamar per Bulan
        // -------------------------
        $bookings = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month');

        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

        $bookingData = [];
        foreach(range(1,12) as $m) {
            $bookingData[] = $bookings[$m] ?? 0;
        }

        // -------------------------
        // 2️⃣ Pendapatan per Bulan
        // -------------------------
        $pendapatan = Booking::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_price) as total')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month');

        $pendapatanData = [];
        foreach(range(1,12) as $m) {
            $pendapatanData[] = $pendapatan[$m] ?? 0;
        }

        // -------------------------
        // 3️⃣ Status Booking (Doughnut Chart)
        // -------------------------
        $statusDataRaw = Booking::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusLabels = $statusDataRaw->keys()->toArray();
        $statusData = $statusDataRaw->values()->toArray();


        // -------------------------
        // Return ke view
        // -------------------------
        return view('admin.dashboard', compact(
            'totalKategori','totalBooking','totalUsers','totalRevenue',
            'months','bookingData','pendapatanData',
            'statusLabels','statusData',
        ));
    }

    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    public function bookings()
    {
        $bookings = Booking::with('user')->paginate(10);
        return view('admin.bookings', compact('bookings'));
    }

    public function categories()
    {
        $categories = Category::paginate(10);
        return view('admin.categories', compact('categories'));
    }

    public function analytics()
    {
        // Analytics data
        $analytics = [
            'totalBookings' => Booking::count(),
            'totalRevenue' => Booking::sum('total_price'),
            'totalUsers' => User::count(),
            'totalCategories' => Category::count(),
        ];

        return view('admin.analytics', compact('analytics'));
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
