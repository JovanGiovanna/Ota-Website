<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Package;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;



class DashboardController extends Controller
{
    public function index()
    {
        // -------------------------
        // Stats Cards
        // -------------------------
        $totalPackages = Package::count();
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
            'totalPackages','totalBooking','totalUsers','totalRevenue',
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
    
    public function packages()
    {
        $packages = Package::paginate(10);
        return view('admin.packages', compact('packages'));
    }
    public function packagesCreate()
    {
        $packages = Package::paginate(10);
        return view('admin.packages.create', compact('packages'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Data
        $validator = Validator::make($request->all(), [
            'name_package' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price_publish' => 'required|numeric|min:0',
            'start_publish' => 'required|date',
            'end_publish' => 'nullable|date|after_or_equal:start_publish',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // 2. Tangani Pengunggahan Gambar
        if ($request->hasFile('image')) {
            try {
                $path = $request->file('image')->store('packages', 'public');
                $data['image'] = $path;
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal mengunggah gambar: ' . $e->getMessage())->withInput();
            }
        }

        // 3. Buat Slug
        $slug = Str::slug($data['name_package']);
        $originalSlug = $slug;
        $count = 1;
        while (Package::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }
        $data['slug'] = $slug;

        try {
            // 4. Simpan ke Database
            Package::create($data);
            return redirect()->route('admin.packages')->with('success', 'Paket berhasil ditambahkan!');
        } catch (\Exception $e) {
            // Jika penyimpanan gagal, hapus gambar yang sudah terunggah
            if (isset($data['image'])) {
                Storage::disk('public')->delete($data['image']);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan paket: ' . $e->getMessage())->withInput();
        }
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
