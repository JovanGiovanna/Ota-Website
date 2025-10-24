<?php

namespace App\Http\Controllers; // <-- THIS LINE IS CRUCIAL

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuperAdmin; // Menggunakan model SuperAdmin
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SuperAdminController extends Controller
{
    // Pastikan nama tabel di Model SuperAdmin adalah 'super_admin' untuk validasi.

    // =====================================
    // --- WEB/SESSION ENDPOINTS (Form) ---
    // =====================================

    /**
     * Menampilkan form login untuk Super Admin.
     */
    public function showLoginForm()
    {
        // Use the same login form as regular users
        return view('auth.login');
    }

    /**
     * Menampilkan form registrasi untuk Super Admin.
     */
    public function showRegistrationForm()
    {
        // Biasanya Super Admin tidak bisa mendaftar sendiri, tapi ini disediakan
        return view('super_admin_auth.register');
    }

    /**
     * Login Super Admin (Berbasis Web/Form).
     */
    public function loginWeb(Request $request)
    {
        // 1. Validasi Input Login
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        
        $credentials = $request->only('email', 'password');
        
        // Coba otentikasi menggunakan guard 'super_admin'
        if (Auth::guard('super_admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('super_admin.dashboard')); 
        }

        // 2. Tangani Kegagalan Otentikasi
        return back()->withErrors([
            'email' => 'Kredensial yang Anda masukkan tidak cocok dengan catatan kami. Mohon periksa email dan password.',
        ])->withInput();
    }
    
    /**
     * Logout Super Admin (Berbasis Web/Session).
     */
    public function logoutWeb(Request $request)
    {
        Auth::guard('super_admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('super_admin.login');
    }


    // =====================================
    // --- API/JSON ENDPOINTS (Sanctum) ---
    // =====================================

    /**
     * Registrasi Super Admin (Berbasis API/JSON) - Mengembalikan Token.
     */
    public function registerApi(Request $request)
    {
        // 1. Validasi Input API
        $validator = Validator::make($request->all() ,[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:super_admin,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $superAdmin = SuperAdmin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Buat token Sanctum. Scope opsional (misalnya, 'admin:access')
            $token = $superAdmin->createToken('super_admin_token', ['admin:access'])->plainTextToken;

            // 3. Respons Sukses Registrasi (HTTP 201 Created)
            return response()->json([
                'message' => 'Super Admin berhasil didaftarkan',
                'super_admin' => [
                    'id' => $superAdmin->id,
                    'name' => $superAdmin->name,
                    'email' => $superAdmin->email,
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        } catch (\Exception $e) {
            Log::error('Super Admin API Registration Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses registrasi.',
            ], 500);
        }
    }

    /**
     * Login Super Admin (Berbasis API/JSON) - Mengembalikan Token.
     */
    public function loginApi(Request $request)
    {
        // 1. Validasi Input API
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Coba otentikasi langsung dari model SuperAdmin
        $superAdmin = SuperAdmin::where('email', $request->email)->first();

        if ($superAdmin && Hash::check($request->password, $superAdmin->password)) {

            // Hapus token lama yang mungkin masih ada, lalu buat token baru
            $superAdmin->tokens()->delete();
            $token = $superAdmin->createToken('super_admin_token', ['admin:access'])->plainTextToken;

            // 3. Respons Sukses Login
            return response()->json([
                'message' => 'Login Super Admin berhasil.',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'super_admin' => [
                    'id' => $superAdmin->id,
                    'name' => $superAdmin->name,
                    'email' => $superAdmin->email,
                ]
            ]);
        }

        // 4. Tangani Kegagalan Kredensial (HTTP 401 Unauthorized)
        return response()->json([
            'message' => 'Gagal login. Kredensial email atau password Super Admin salah.',
        ], 401);
    }
    
    /**
     * Logout Super Admin (Berbasis API/JSON) - Mencabut Token.
     */
    public function logoutApi(Request $request)
    {
        // Memastikan request.user() berhasil mengambil data pengguna yang terotentikasi melalui Sanctum
        if ($request->user()) {
             /** @var \App\Models\SuperAdmin $user */
            $user = $request->user();
            // Hapus token yang sedang digunakan
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logout Super Admin berhasil. Token dicabut.',
        ]);
    }
    
    /**
     * Mengambil data profil Super Admin yang sedang terotentikasi.
     */
    public function showProfileApi(Request $request)
    {
        // Metode user() secara otomatis mengambil pengguna dari guard Sanctum yang terkonfigurasi.
        $superAdmin = $request->user();

        if (!$superAdmin) {
            return response()->json([
                'message' => 'Tidak Terotentikasi. Akses ditolak.',
            ], 401);
        }

        return response()->json([
            'message' => 'Data profil Super Admin berhasil diambil.',
            'super_admin' => [
                'id' => $superAdmin->id,
                'name' => $superAdmin->name,
                'email' => $superAdmin->email,
                'created_at' => $superAdmin->created_at,
            ]
        ]);
    }

    /**
     * Super Admin Dashboard
     */
    public function dashboard(Request $request)
    {
        // Get monthly booking data for the last 6 months
        $monthlyBookings = \App\Models\Booking::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Get monthly revenue data for the last 6 months
        $monthlyRevenue = \App\Models\Booking::selectRaw('MONTH(created_at) as month, SUM(total_price) as revenue')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        // Get booking status counts
        $statusCounts = \App\Models\Booking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get monthly facility bookings (assuming products are facilities)
        $monthlyFacilities = \App\Models\BookProduct::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Prepare data for charts
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $bookingData = [];
        $revenueData = [];
        $facilityData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthNum = now()->subMonths($i)->month;
            $bookingData[] = $monthlyBookings[$monthNum] ?? 0;
            $revenueData[] = $monthlyRevenue[$monthNum] ?? 0;
            $facilityData[] = $monthlyFacilities[$monthNum] ?? 0;
        }

        $statusData = [
            $statusCounts['completed'] ?? 0,
            $statusCounts['pending'] ?? 0,
            $statusCounts['cancelled'] ?? 0,
            $statusCounts['confirmed'] ?? 0
        ];

        return view('super_admin.dashboard', compact(
            'months',
            'bookingData',
            'revenueData',
            'statusData',
            'facilityData'
        ));
    }

    /**
     * Index method for transaction packages
     */
    public function transactionPackages()
    {
        // Fetch transaction packages data from bookings table with relations
        $transactions = \App\Models\Booking::with(['user', 'package', 'addons'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('super_admin.transaction_packages', compact('transactions'));
    }

    /**
     * Index method for transaction products
     */
    public function transactionProducts()
    {
        // Fetch transaction products data from book_products table with relations
        $transactions = \App\Models\BookProduct::with(['user', 'product.vendor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('super_admin.transaction_products', compact('transactions'));
    }

    /**
     * Index method for transaction addons
     */
    public function transactionAddons()
    {
        // Fetch transaction addons data from book_addons table with relations
        $transactions = \App\Models\BookAddon::with(['user', 'addon.vendor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('super_admin.transaction_addons', compact('transactions'));
    }

    /**
     * Index method for customers
     */
    public function customers()
    {
        // Fetch customers data from users table with booking counts
        $customers = \App\Models\User::withCount('bookings')
        ->with(['bookings' => function ($query) {
            $query->latest()->take(1);
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('super_admin.customers', compact('customers'));
    }

    /**
     * Ban a customer
     */
    public function banCustomer(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->update(['status' => 'banned']);

        return redirect()->back()->with('success', 'Customer has been banned successfully.');
    }

    /**
     * Unban a customer
     */
    public function unbanCustomer(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Customer has been unbanned successfully.');
    }

    /**
     * View customer details
     */
    public function viewCustomer($id)
    {
        $customer = \App\Models\User::with(['bookings.package', 'bookings.addons'])->findOrFail($id);

        return view('super_admin.customers.view', compact('customer'));
    }

    /**
     * Index method for rekon
     */
    public function rekon()
    {
        // Fetch reconciliation data - assuming reconciliation is based on bookings
        // For now, we'll simulate reconciliation by comparing system records
        $rekons = \App\Models\Booking::selectRaw('
            id,
            created_at,
            total_price as system_amount,
            CASE WHEN status = "completed" THEN total_price ELSE 0 END as bank_amount,
            CASE WHEN status = "completed" THEN 0 ELSE total_price END as difference,
            status
        ')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        // Calculate summary stats
        $totalRevenue = \App\Models\Booking::where('status', 'completed')->sum('total_price');
        $completedTransactions = \App\Models\Booking::where('status', 'completed')->count();
        $pendingReconciliation = \App\Models\Booking::where('status', '!=', 'completed')->count();
        $discrepancies = \App\Models\Booking::where('status', 'cancelled')->count();

        return view('super_admin.rekon', compact(
            'rekons',
            'totalRevenue',
            'completedTransactions',
            'pendingReconciliation',
            'discrepancies'
        ));
    }
}
