<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
    
    return redirect()->route('admin.profil')->with('success', 'Profil berhasil diperbarui!');
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
        return redirect()->route('admin.login')->with('success', 'Admin registered successfully. You can now login.');
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

            return redirect()->intended(route('super_admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
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
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        }

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'admin',
        ]);

        $token = $admin->createToken('admin_token')->plainTextToken;

        return response()->json([
            'message' => 'Admin registered successfully',
            'admin' => $admin,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    // --- API Login Admin ---

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $admin->createToken('admin_token')->plainTextToken;

        return response()->json([
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
        // Hapus token yang sedang digunakan
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $request->user()->currentAccessToken();
        $token->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    // --- Show Profile Admin (API/JSON) ---

    public function showProfile(Request $request)
    {
        // Asumsikan endpoint ini dilindungi oleh middleware 'auth:sanctum'
        $admin = $request->user('admin');

        if (!$admin) {
            return response()->json([
                'message' => 'Tidak Terotentikasi. Akses ditolak.',
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
