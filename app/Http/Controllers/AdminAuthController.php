<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Helpers\ErrorHandler;

class AdminAuthController extends Controller
{
    public function showProfilePage()
{
    return view('admin.profile');
}

public function editProfile()
{
    $admin = Auth::guard('admin')->user();
    return view('admin.profile-edit', compact('admin'));
}

public function updateProfile(Request $request)
{
    $admin = Auth::guard('admin')->user();
    
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:admins,email,' . $admin->id,
    ]);
    
    $admin->update([
        'name' => $request->name,
        'email' => $request->email,
    ]);

    if (function_exists('alert')) {
        alert()->success('Berhasil', 'Profil berhasil diperbarui!');
    }
    return redirect()->route('admin.profil');
}
    // --- Tampilan Form ---

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegistrationForm()
    {
        // Only allow super_admin to view the admin register form
        if (!\Illuminate\Support\Facades\Auth::guard('super_admin')->check()) {
            abort(403, 'Only super admin can create admin accounts.');
        }

        return view('admin_auth.register');
    }

    // Web registration handler for admin (form)
    public function registerWeb(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6',
            'role' => 'nullable|in:' . implode(',', \App\Models\Admin::ROLES),
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'admin',
        ]);

        // Redirect to login page after registration
        if (function_exists('alert')) {
            alert()->success('Berhasil', 'Admin berhasil terdaftar. Silakan login dengan email dan password Anda.');
        }
        return redirect()->route('admin.login');
    }

    // --- Login Admin (Web/Form) ---

    public function loginWeb(Request $request)
    {
        // 1. Validasi Input Login
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Coba otentikasi menggunakan guard 'admin'
        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            if (function_exists('alert')) {
                alert()->success('Login Berhasil', 'Selamat datang kembali!');
            }
            return redirect()->intended(route('super_admin.dashboard'));
        }

        // Tangani kegagalan autentikasi dengan SweetAlert
        return ErrorHandler::invalidCredentials();
    }

    // --- Logout Admin (Web/Session) ---

    public function logoutWeb(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if (function_exists('alert')) {
            alert()->success('Logout Berhasil', 'Anda telah keluar dari sistem.');
        }
        return redirect()->route('login');
    }

    // --- API Registrasi Admin ---

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:6',
            'role' => 'nullable|in:' . implode(',', \App\Models\Admin::ROLES),
        ]);

        if ($validator->fails()) {
            return ErrorHandler::validationErrorJson($validator);
        }

        try {
            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'admin',
            ]);

            $token = $admin->createToken('admin_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'title' => 'Registrasi Berhasil',
                'message' => 'Admin berhasil terdaftar',
                'icon' => 'success',
                'admin' => $admin,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        } catch (\Exception $e) {
            Log::error('Admin Registration Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'title' => 'Server Error',
                'message' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi nanti.',
                'icon' => 'error'
            ], 500);
        }
    }

    // --- API Login Admin ---

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return ErrorHandler::validationErrorJson($validator);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'title' => 'Akun Tidak Ditemukan',
                'message' => 'Tidak ada akun terdaftar dengan email ini.',
                'icon' => 'error'
            ], 404);
        }

        if (!Hash::check($request->password, $admin->password)) {
            return response()->json([
                'status' => 'error',
                'title' => 'Login Gagal',
                'message' => 'Email atau password salah. Silakan periksa dan coba lagi.',
                'icon' => 'error'
            ], 401);
        }

        $admin->tokens()->delete();
        $token = $admin->createToken('admin_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'title' => 'Login Berhasil',
            'message' => 'Selamat datang kembali!',
            'icon' => 'success',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ]
        ]);
    }

    // --- Logout Admin (API/JSON) ---

    public function logoutApi(Request $request)
    {
        try {
            if ($request->user()) {
                /** @var \Laravel\Sanctum\PersonalAccessToken $token */
                $token = $request->user()->currentAccessToken();
                $token->delete();
            }

            return response()->json([
                'status' => 'success',
                'title' => 'Logout Berhasil',
                'message' => 'Anda telah keluar dari sistem.',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            Log::error('Admin Logout Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'title' => 'Logout Gagal',
                'message' => 'Terjadi kesalahan saat logout.',
                'icon' => 'error'
            ], 500);
        }
    }

    // --- Show Profile Admin (API/JSON) ---

    public function showProfile(Request $request)
    {
        // Asumsikan endpoint ini dilindungi oleh middleware 'auth:sanctum'
        $admin = $request->user('admin');

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'title' => 'Tidak Terotentikasi',
                'message' => 'Akses ditolak. Silakan login terlebih dahulu.',
                'icon' => 'error'
            ], 401);
        }

        return response()->json([
            'message' => 'Data profil Admin berhasil diambil.',
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'created_at' => $admin->created_at,
            ]
        ]);
    }

    // --- Logout Admin (Web) ---

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
