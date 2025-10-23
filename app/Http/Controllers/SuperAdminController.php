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
     * Registrasi Super Admin (Berbasis Web/Form).
     */
    public function registerWeb(Request $request)
    {
        // 1. Validasi Input Registrasi
        $request->validate([
            'name' => 'required|string|max:255',
            // Pastikan email unik di tabel 'super_admin'
            'email' => 'required|string|email|max:255|unique:super_admin,email', 
            'password' => 'required|string|min:8|confirmed', 
        ]);

        try {
            $superAdmin = SuperAdmin::create([
                // ID UUID akan dibuat otomatis oleh Model
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Otomatis loginkan Super Admin setelah registrasi
            Auth::guard('super_admin')->login($superAdmin);

            return redirect()->route('super_admin.dashboard')->with('success', 'Registrasi Super Admin berhasil. Selamat datang!');
        } catch (\Exception $e) {
            Log::error('Super Admin Web Registration Failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['gagal' => 'Gagal menyelesaikan registrasi karena masalah sistem. Silakan coba lagi.']);
        }
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

        $credentials = $request->only('email', 'password');
        
        // Coba otentikasi menggunakan guard 'super_admin'
        if (Auth::guard('super_admin')->attempt($credentials)) {
            
            /** @var \App\Models\SuperAdmin $superAdmin */
            $superAdmin = Auth::guard('super_admin')->user();
            
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
        // You can add dashboard logic here, similar to DashboardController
        // For now, return the view
        return view('super_admin.dashboard');
    }

    /**
     * Index method for transaction packages
     */
    public function transactionPackages()
    {
        // Add logic to fetch transaction packages data
        return view('super_admin.transaction_packages');
    }

    /**
     * Index method for transaction products
     */
    public function transactionProducts()
    {
        // Add logic to fetch transaction products data
        return view('super_admin.transaction_products');
    }

    /**
     * Index method for transaction addons
     */
    public function transactionAddons()
    {
        // Add logic to fetch transaction addons data
        return view('super_admin.transaction_addons');
    }

    /**
     * Index method for rekon
     */
    public function rekon()
    {
        // Add logic to fetch rekon data
        return view('super_admin.rekon');
    }
}
