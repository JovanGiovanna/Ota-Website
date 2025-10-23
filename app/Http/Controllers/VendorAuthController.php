<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\VendorInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log; // Import Log untuk error handling

class VendorAuthController extends Controller
{
    // --- Tampilan Form ---

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegistrationForm()
    {
        return view('auth.registervendor');
    }

    // --- Registrasi Vendor (Web/Form) ---

    public function registerWeb(Request $request)
    {
        // 1. Validasi Input Registrasi
        $request->validate([
            'name' => 'required|string|max:255',
            // Pastikan email unik di tabel 'vendor'
            'email' => 'required|string|email|max:255|unique:vendor,email', 
            'password' => 'required|string|min:6|confirmed', // 'confirmed' memastikan ada field password_confirmation
        ]);

        try {
            $vendor = Vendor::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Otomatis loginkan vendor setelah registrasi
            Auth::guard('vendor')->login($vendor);

            return redirect()->intended(route('vendor.dashboard'))->with('success', 'Registrasi Vendor berhasil. Selamat datang!');
        } catch (\Exception $e) {
            // Tangani kegagalan sistem saat penyimpanan data (misalnya error database)
            Log::error('Vendor Web Registration Failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['gagal' => 'Gagal menyelesaikan registrasi karena masalah sistem. Silakan coba lagi.']);
        }
    }

    // --- Login Vendor (Web/Form) ---

    public function loginWeb(Request $request)
    {
        // 1. Validasi Input Login
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        
        $credentials = $request->only('email', 'password');
        
        // Coba otentikasi menggunakan guard 'vendor'
        if (Auth::guard('vendor')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('vendor.dashboard')); 
        }

        // 2. Tangani Kegagalan Otentikasi (Credentials Salah)
        return back()->withErrors([
            // Berikan pesan umum untuk keamanan (tidak menyebutkan apakah email/password yang salah)
            'email' => 'Kredensial yang Anda masukkan tidak cocok dengan catatan kami. Mohon periksa email dan password.',
        ])->withInput();
    }
    
    // --- Logout Vendor (TETAP) ---

    public function logout(Request $request)
    {
        Auth::guard('vendor')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('vendor.login');
    }

    // --- Logout Vendor (API/JSON) ---

    public function logoutApi(Request $request)
    {
        // Hapus token yang sedang digunakan
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $request->user()->currentAccessToken();
        $token->delete();


        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }


    // ------------------------------------
    // --- API/JSON ENDPOINTS ---
    // ------------------------------------
    
    // --- Registrasi Vendor (API/JSON) ---
    
    public function register(Request $request)
    {
        // 1. Validasi Input API
        $validator = Validator::make($request->all() ,[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:vendor,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            // 2. Respons JSON jika validasi gagal (HTTP 422 Unprocessable Entity)
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $vendor = Vendor::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Asumsi menggunakan Sanctum untuk API Token
            $token = $vendor->createToken('vendor_token')->plainTextToken;

            // 3. Respons Sukses Registrasi (HTTP 201 Created)
            return response()->json([
                'message' => 'Vendor berhasil didaftarkan',
                'vendor' => [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'email' => $vendor->email,
                ],
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        } catch (\Exception $e) {
            // 4. Tangani Kegagalan Server (HTTP 500 Internal Server Error)
            Log::error('Vendor API Registration Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses registrasi.',
            ], 500);
        }
    }

    // --- Login Vendor (API/JSON) ---
    
    public function login(Request $request)
    {
        // 1. Validasi Input API
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]); 

        if ($validator->fails()) {
            // 2. Respons JSON jika validasi gagal (HTTP 422 Unprocessable Entity)
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');
        
        // Gunakan Auth::guard('vendor')->attempt untuk otentikasi
        if (Auth::guard('vendor')->attempt($credentials)) {
            $vendor = Auth::guard('vendor')->user();
            
            // Hapus token lama dan buat token baru (jika menggunakan Sanctum)
            $vendor->tokens()->delete();
            $token = $vendor->createToken('vendor_token')->plainTextToken;

            // 3. Respons Sukses Login
            return response()->json([
                'message' => 'Login berhasil.',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'vendor' => [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'email' => $vendor->email,
                ]
            ]);
        }

        // 4. Tangani Kegagalan Kredensial (HTTP 401 Unauthorized)
        // Throwing ValidationException is good, but returning a controlled JSON response is often better for APIs
        return response()->json([
            'message' => 'Gagal login. Kredensial email atau password Vendor salah.',
        ], 401);
    }
    
    // --- Tampilkan Profil Vendor (API/JSON - Membutuhkan Middleware Auth:sanctum) ---

    public function showProfile(Request $request)
    {
        // Asumsikan endpoint ini dilindungi oleh middleware 'auth:sanctum'
        $vendor = $request->user('vendor'); 

        if (!$vendor) {
            // 1. Penanganan Tidak Terotentikasi (HTTP 401 Unauthorized)
            // Ini akan menangani kasus di mana middleware gagal atau token tidak valid/hilang.
            return response()->json([
                'message' => 'Tidak Terotentikasi. Akses ditolak. Mohon login ulang.',
            ], 401);
        }

        // 2. Respons Sukses Profil (HTTP 200 OK)
        return response()->json([
            'message' => 'Data profil Vendor berhasil diambil.',
            'vendor' => [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'email' => $vendor->email,
                'created_at' => $vendor->created_at,
            ]
        ]);
    }
}
