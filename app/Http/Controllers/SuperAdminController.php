<?php

namespace App\Http\Controllers; // <-- THIS LINE IS CRUCIAL

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuperAdmin; // Menggunakan model SuperAdmin
use App\Models\Rekon;
use App\Models\BookPackage; // Pastikan model ini di-import
use App\Models\BookAddon; // Pastikan model ini di-import
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;
use App\Models\Permission;

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

        return redirect()->route('login');
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
        // Show all bookings, even those without packages (id_package can be null)
        $transactions = \App\Models\Booking::with(['user', 'packages', 'addons'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('super_admin.transaction_packages', compact('transactions'));
    }

    /**
     * Index method for transaction products
     */
public function transactionProducts()
{
    // Fetch transaction products data from detail_booking table with relations
    $transactions = \App\Models\Detail_Booking::with(['booking.user', 'product.vendor.vendorInfo'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    // Add calculated fields to each transaction
    $transactions->getCollection()->transform(function ($transaction) {
        $product = $transaction->product;

        $basicPrice = $product->basic_price ?? 0;
        $taxRate = $product->tax_rate ?? 0;
        $discountType = $product->discount_type;
        $discountValue = $product->discount_value ?? 0;

        // Calculate tax amount
        $taxAmount = $basicPrice * ($taxRate / 100);

        // Calculate discount amount
        $totalPriceBeforeDiscount = $basicPrice + $taxAmount;
        $discountAmount = 0;
        if ($discountType === 'percentage' && $discountValue > 0) {
            $discountAmount = $totalPriceBeforeDiscount * ($discountValue / 100);
        } elseif ($discountType === 'fixed' && $discountValue > 0) {
            $discountAmount = $discountValue;
        }

        // Calculate nta
        $nta = $basicPrice + $taxAmount - $discountAmount;

        // Calculate pax paid (sum of adults and children)
        $paxPaid = ($transaction->adults ?? 0) + ($transaction->children ?? 0);

        // Calculate profit = pax paid * (final price - nta)
        // finalPrice includes discount, to get from product accessor if exists, otherwise calculate
        $finalPrice = $product->finalPrice ?? ($totalPriceBeforeDiscount - $discountAmount);
        $profit = $paxPaid * ($finalPrice - $nta);

        // Attach to transaction object for view access
        $transaction->basic_price = $basicPrice;
        $transaction->tax_amount = $taxAmount;
        $transaction->discount_amount = $discountAmount;
        $transaction->nta = $nta;
        $transaction->pax_paid = $paxPaid;
        $transaction->profit = $profit;

        return $transaction;
    });

    return view('super_admin.transaction_products', compact('transactions'));
}

    /**
     * Index method for transaction addons
     */
    public function transactionAddons()
    {
        // Fetch transaction addons data from book_addons table with relations
        $transactions = \App\Models\BookAddon::with(['user', 'addon.vendor.vendorInfo'])
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
     * Index method for rekon - Filter by completed status, date range, and vendor
     * Display profit-only chart, ordered by newest first
     */
    public function rekon()
    {
        // Get filter parameters
        $search = request('search');
        $vendorId = request('vendor_id');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        // Get all vendors for filter dropdown
        $vendors = \App\Models\Vendor::pluck('name', 'id')->toArray();

        // Prepare an array to collect all booking records (products, addons only - no packages for now)
        $rekonDetails = [];

        // Fetch bookProducts with booking and product relations (completed status only)
        $bookProducts = \App\Models\BookProduct::with(['booking', 'product', 'product.vendor'])
            ->whereHas('booking', function ($q) {
                $q->where('status', 'completed');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($bookProducts as $bookProduct) {
            $booking = $bookProduct->booking;
            $product = $bookProduct->product;

            if (!$product || !$booking) {
                continue;
            }

            $basicPrice = $product->basic_price ?? 0;
            $taxRate = $product->tax_rate ?? 0;
            $discountType = $product->discount_type;
            $discountValue = $product->discount_value ?? 0;

            // Calculate tax amount
            $taxAmount = $basicPrice * ($taxRate / 100);

            // Calculate discount amount
            $totalPriceBeforeDiscount = $basicPrice + $taxAmount;
            $discountAmount = 0;
            if ($discountType === 'percentage' && $discountValue > 0) {
                $discountAmount = $totalPriceBeforeDiscount * ($discountValue / 100);
            } elseif ($discountType === 'fixed' && $discountValue > 0) {
                $discountAmount = $discountValue;
            }

            // NTA = BasicPrice - Discount
            $ntaPerUnit = $basicPrice - $discountAmount;

            // Pax paid
            $paxCount = $bookProduct->amount ?? 1;

            // TotalPaid = BasicPrice + Tax - Discount
            $totalPaid = $paxCount * ($basicPrice + $taxAmount - $discountAmount);

            // Total NTA
            $totalNta = $paxCount * $ntaPerUnit;

            // Profit = TotalPaid - NTA, ensure not negative
            $profit = max(0, $totalPaid - $totalNta);

            $rekonDetails[] = (object) [
                'transaction_id' => $booking->booking_code ?? '#' . strtoupper(substr($booking->id, 0, 8)),
                'date' => $booking->created_at,
                'product_name' => $product->name . ' (Product)',
                'vendor_id' => $product->id_vendor,
                'vendor_name' => $product->vendor->name ?? 'Unknown',
                'basic_price' => $basicPrice,
                'tax' => $taxAmount,
                'discount' => $discountAmount,
                'nta' => $totalNta,
                'pax_paid' => $totalPaid,
                'profit' => $profit,
                'status' => $booking->status,
                'type' => 'product'
            ];
        }

        // Fetch bookPackages with booking and package relations
        $bookPackages = \App\Models\BookPackage::with(['booking', 'package', 'package.vendor']) // <-- Tambahkan 'package.vendor'
        ->whereHas('booking', function ($q) { // <-- Tambahkan filter completed
            $q->where('status', 'completed');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($bookPackages as $bookPackage) {
            $booking = $bookPackage->booking;
            $package = $bookPackage->package;

            if (!$package || !$booking || $booking->status !== 'completed') {
                continue; // Skip if no package or booking linked or status is not completed
            }

            // Get pax count from booking
            $paxCount = ($booking->adults ?? 0) + ($booking->children ?? 0) ?: 1;

            // For packages, basic price is pax_paid (selling price per pax)
            $basicPrice = $package->pax_paid ?? 0;

            // Calculate tax amount based on package's tax_rate (per pax)
            $taxRate = $package->tax_rate ?? 0;
            $taxAmount = $basicPrice * ($taxRate / 100);

            // Packages have no discount in model
            $discountAmount = 0;

            // NTA = BasicPrice - Discount
            $ntaPerUnit = $basicPrice - $discountAmount;

            // TotalPaid = BasicPrice + Tax - Discount
            $totalPaid = $paxCount * ($basicPrice + $taxAmount - $discountAmount);

            // Total NTA
            $totalNta = $paxCount * $ntaPerUnit;

            // Profit = TotalPaid - NTA, ensure not negative
            $profit = max(0, $totalPaid - $totalNta);

            $rekonDetails[] = (object) [
                'transaction_id' => $booking->booking_code ?? '#' . strtoupper(substr($booking->id, 0, 8)),
                'date' => $booking->created_at,
                'product_name' => $package->name_package . ' (Package)', // Menggunakan nama package, bukan product
                'vendor_id' => $package->vendorInfo->id_vendor ?? null,
                'vendor_name' => $package->vendorInfo->name_corporate ?? 'Unknown',
                'basic_price' => $basicPrice,
                'tax' => $taxAmount,
                'discount' => $discountAmount,
                'nta' => $totalNta,
                'pax_paid' => $totalPaid,
                'profit' => $profit,
                'status' => $booking->status,
                'type' => 'package' // Tipe 'package'
            ];
        }

        // Fetch bookAddons with booking and addon relations (completed status only)
        $bookAddons = \App\Models\BookAddon::with(['booking', 'addon', 'addon.vendor'])
            ->whereHas('booking', function ($q) {
                $q->where('status', 'completed');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($bookAddons as $bookAddon) {
            $booking = $bookAddon->booking;
            $addon = $bookAddon->addon;

            if (!$addon || !$booking) {
                continue;
            }

            $basicPrice = $addon->basic_price ?? 0;
            $taxRate = $addon->tax_rate ?? 0;
            $discountType = $addon->discount_type;
            $discountValue = $addon->discount_value ?? 0;

            // Calculate tax amount
            $taxAmount = $basicPrice * ($taxRate / 100);

            // Calculate discount amount
            $totalPriceBeforeDiscount = $basicPrice + $taxAmount;
            $discountAmount = 0;
            if ($discountType === 'percentage' && $discountValue > 0) {
                $discountAmount = $totalPriceBeforeDiscount * ($discountValue / 100);
            } elseif ($discountType === 'fixed' && $discountValue > 0) {
                $discountAmount = $discountValue;
            }

            // NTA = BasicPrice - Discount
            $ntaPerUnit = $basicPrice - $discountAmount;

            // Use amount from bookAddon as pax count
            $paxCount = $bookAddon->amount ?? 1;

            // TotalPaid = BasicPrice + Tax - Discount
            $totalPaid = $paxCount * ($basicPrice + $taxAmount - $discountAmount);

            // Total NTA
            $totalNta = $paxCount * $ntaPerUnit;

            // Profit = TotalPaid - NTA
            $profit = $totalPaid - $totalNta;

            $rekonDetails[] = (object) [
                'transaction_id' => $booking->booking_code ?? '#' . strtoupper(substr($booking->id, 0, 8)),
                'date' => $booking->created_at,
                'product_name' => $addon->addons . ' (Addon)',
                'vendor_id' => $addon->id_vendor,
                'vendor_name' => $addon->vendor->name ?? 'Unknown',
                'basic_price' => $basicPrice,
                'tax' => $taxAmount,
                'discount' => $discountAmount,
                'nta' => $totalNta,
                'pax_paid' => $totalPaid,
                'profit' => $profit,
                'status' => $booking->status,
                'type' => 'addon'
            ];
        }

        // Apply filters
        $rekonDetailsCollection = collect($rekonDetails);

        // Search filter (transaction ID or product name)
        if ($search) {
            $rekonDetailsCollection = $rekonDetailsCollection->filter(function ($detail) use ($search) {
                return str_contains(strtolower($detail->product_name), strtolower($search)) ||
                        str_contains(strtolower($detail->transaction_id), strtolower($search));
            });
        }

        // Vendor filter
        if ($vendorId) {
            $rekonDetailsCollection = $rekonDetailsCollection->filter(function ($detail) use ($vendorId) {
                return $detail->vendor_id === $vendorId;
            });
        }

        // Date range filter
        if ($dateFrom && $dateTo) {
            $startDate = \Carbon\Carbon::parse($dateFrom)->startOfDay();
            $endDate = \Carbon\Carbon::parse($dateTo)->endOfDay();
            $rekonDetailsCollection = $rekonDetailsCollection->filter(function ($detail) use ($startDate, $endDate) {
                $detailDate = \Carbon\Carbon::parse($detail->date);
                return $detailDate->between($startDate, $endDate);
            });
        }

        // Sort all records by date descending (newest first)
        $rekonDetails = $rekonDetailsCollection->sortByDesc('date')->values()->all();

        // Paginate the combined rekonDetails array
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage('page');
        $rekonDetailsCollection = collect($rekonDetails);
        $total = $rekonDetailsCollection->count();
        $offset = ($currentPage - 1) * $perPage;
        $items = $rekonDetailsCollection->slice($offset, $perPage);
        $rekonDetailsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        // Calculate profit chart data for last 6 months (completed only)
        $monthlyProfit = [];
        $labels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M');
            $labels[] = $month;

            $profit = collect($rekonDetails)->filter(function ($detail) use ($date) {
                $detailDate = \Carbon\Carbon::parse($detail->date);
                return $detailDate->month === $date->month && $detailDate->year === $date->year;
            })->sum('profit');

            $monthlyProfit[] = (int)$profit;
        }

        // Calculate summary stats (completed status only)
        $totalProfit = collect($rekonDetails)->sum('profit');
        $completedTransactions = \App\Models\Booking::where('status', 'completed')->count();
        $totalRevenue = \App\Models\Booking::where('status', 'completed')->sum('total_price');
        $avgProfit = $completedTransactions > 0 ? $totalProfit / $completedTransactions : 0;

        return view('super_admin.rekon', compact(
            'rekonDetailsPaginated',
            'vendors',
            'totalProfit',
            'completedTransactions',
            'totalRevenue',
            'avgProfit',
            'monthlyProfit',
            'labels'
        ));
    }

    // ---------------------------
    // Admin management for Super Admin
    // ---------------------------
    public function adminsIndex(Request $request)
    {
        $query = Admin::query();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $admins = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('super_admin.admins.index', compact('admins'));
    }

    public function createAdminForm()
    {
        $permissions = Permission::orderBy('name')->get();
        $roles = \App\Models\Role::orderBy('name')->get();
        return view('super_admin.admins.form', ['admin' => null, 'permissions' => $permissions, 'roles' => $roles]);
    }

    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'password' => 'nullable|string|min:6|confirmed',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // determine legacy role column value: prefer first selected role key, otherwise default to 'admin'
        $legacyRole = 'admin';
        if (!empty($validated['roles'])) {
            $firstRole = \App\Models\Role::find($validated['roles'][0]);
            if ($firstRole) $legacyRole = $firstRole->key;
        }

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $legacyRole,
            'password' => isset($validated['password']) ? bcrypt($validated['password']) : null,
        ]);

        if (!empty($validated['permissions'])) {
            $admin->permissions()->sync($validated['permissions']);
        }

        // Assign roles if provided
        if (!empty($validated['roles'])) {
            // Enforce super_admin limit: max 2
            $superRole = \App\Models\Role::where('key', 'super_admin')->first();
            if ($superRole && in_array($superRole->id, $validated['roles'])) {
                $countSuper = \DB::table('admin_role')->where('role_id', $superRole->id)->count();
                if ($countSuper >= 2) {
                    // rollback created admin
                    $admin->permissions()->detach();
                    $admin->delete();
                    return back()->withInput()->with('error', 'Batas maksimal Super Admin (2 orang) telah tercapai.');
                }
            }

            $admin->roles()->sync($validated['roles']);
        }

        return redirect()->route('super_admin.admins')->with('success', 'Admin berhasil dibuat.');
    }

    public function editAdminForm(Admin $admin)
    {
        $permissions = Permission::orderBy('name')->get();
        $roles = \App\Models\Role::orderBy('name')->get();
        $admin->load('permissions', 'roles');
        return view('super_admin.admins.form', compact('admin', 'permissions', 'roles'));
    }

    public function updateAdmin(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'password' => 'nullable|string|min:6|confirmed',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Update legacy role column to first selected role key if roles provided
        $legacyRole = $admin->role;
        if (array_key_exists('roles', $validated) && !empty($validated['roles'])) {
            $firstRole = \App\Models\Role::find($validated['roles'][0]);
            if ($firstRole) $legacyRole = $firstRole->key;
        }

        $admin->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $legacyRole,
        ]);

        if (!empty($validated['password'])) {
            $admin->update(['password' => bcrypt($validated['password'])]);
        }

        $admin->permissions()->sync($validated['permissions'] ?? []);

        // Sync roles if provided
        if (array_key_exists('roles', $validated)) {
            // If roles include super_admin, enforce max 2
            $superRole = \App\Models\Role::where('key', 'super_admin')->first();
            if ($superRole && in_array($superRole->id, $validated['roles'] ?? [])) {
                $countSuper = \DB::table('admin_role')->where('role_id', $superRole->id)->count();
                // allow if current admin already has super role (editing), otherwise enforce max 2
                $hasAlready = $admin->roles()->where('key', 'super_admin')->exists();
                if (!$hasAlready && $countSuper >= 2) {
                    return back()->withInput()->with('error', 'Batas maksimal Super Admin (2 orang) telah tercapai.');
                }
            }

            $admin->roles()->sync($validated['roles'] ?? []);
        }

        return redirect()->route('super_admin.admins')->with('success', 'Admin berhasil diperbarui.');
    }

    public function destroyAdmin(Admin $admin)
    {
        if ($admin->hasRole('super_admin') || ($admin->role ?? null) === 'super_admin') {
            return back()->with('error', 'Tidak dapat menghapus akun Super Admin.');
        }

        $admin->permissions()->detach();
        $admin->roles()->detach();
        $admin->delete();

        return back()->with('success', 'Admin berhasil dihapus.');
    }
}