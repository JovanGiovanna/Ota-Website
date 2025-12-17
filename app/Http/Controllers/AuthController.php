<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SweetAlert;
use App\Helpers\ErrorHandler;
use App\Helpers\SweetAlertHelper;

class AuthController extends Controller
{
public function index(Request $request)
{
    $query = User::query();

    // Pencarian berdasarkan nama
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Ambil data users
    $customers = $query->paginate(10);

    // Hitung jumlah user
    $userCount = User::count();

    // Kirim ke view
    return view('super_admin.customers', compact('customers', 'userCount'));
}

    public function register(Request $request)
    {
        $validator = Validator::make($request->all() ,[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function registerWeb(Request $request)
    {
        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser) {
            // If user exists and has no password, allow setting password
            if (is_null($existingUser->password)) {
                $validator = Validator::make($request->all(), [
                    'email' => 'required|string|email|max:255',
                    'password' => 'required|string|min:6',
                ]);

                if ($validator->fails()) {
                    if (function_exists('alert')) {
                        alert()->error('Validation Failed', 'Mohon periksa kembali form Anda');
                    }
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                try {
                    $existingUser->update([
                        'password' => Hash::make($request->password),
                    ]);

                    Auth::login($existingUser);

                    return SweetAlert::success('Password berhasil diset, Anda telah login!', route('user.search'));
                } catch (\Exception $e) {
                    if (function_exists('alert')) {
                        alert()->error('Error', 'Gagal menyimpan password. Silakan coba lagi.');
                    }
                    return redirect()->back()
                        ->withInput();
                }
            } else {
                // If user exists and has password, redirect to login
                if (function_exists('alert')) {
                    alert()->error('Email sudah terdaftar', 'Silakan login dengan email dan password Anda.');
                }
                return redirect()->route('login')->withInput(['email' => $request->email]);
            }
        } else {
            // New user registration
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                if (function_exists('alert')) {
                    alert()->error('Validation Failed', 'Mohon periksa kembali form Anda');
                }
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            try {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                Auth::login($user);

                return SweetAlert::created('Akun', route('user.search'));
            } catch (\Exception $e) {
                if (function_exists('alert')) {
                    alert()->error('Error', 'Registrasi gagal. Silakan coba lagi.');
                }
                return redirect()->back()
                    ->withInput();
            }
        }
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

       public function showLoginForm()
    {
        return view('auth.login');
    }

       public function showRegistrationForm()
    {
        return view('auth.register');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return SweetAlert::success('Anda telah logout', route('login'));
    }

    // --- Logout User (API/JSON) ---

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


public function loginWeb(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if ($validator->fails()) {
        if (function_exists('alert')) {
            alert()->error('Validasi Gagal', ErrorHandler::formatValidationErrors($validator, ', '));
        }
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    try {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::guard('super_admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            if (function_exists('alert')) {
                alert()->success('Login Berhasil', 'Selamat datang kembali!');
            }
            return redirect()->intended(route('super_admin.dashboard'));
        }

        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            if (function_exists('alert')) {
                alert()->success('Login Berhasil', 'Selamat datang kembali!');
            }
            return redirect()->intended(route('admin.dashboard'));
        }

        if (Auth::guard('vendor')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            if (function_exists('alert')) {
                alert()->success('Login Berhasil', 'Selamat datang kembali!');
            }
            return redirect()->intended(route('vendor.dashboard'));
        }

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            if (function_exists('alert')) {
                alert()->success('Login Berhasil', 'Selamat datang kembali!');
            }
            return redirect()->intended(route('user.search'));
        }

        // Jika semua guard gagal, tampilkan error
        return ErrorHandler::invalidCredentials();
    } catch (\Exception $e) {
        return ErrorHandler::genericError('Terjadi kesalahan saat login. Silakan coba lagi.');
    }
}

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Validation Error',
                    'message' => SweetAlertHelper::getValidationErrors($validator),
                    'icon' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = Auth::user();
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'dob' => $request->dob,
                'address' => $request->address,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'title' => 'Success!',
                    'message' => 'Profile updated successfully.',
                    'icon' => 'success'
                ]);
            }

            alert()->success('Success', 'Profile updated successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Update Failed',
                    'message' => 'An error occurred while updating profile.',
                    'icon' => 'error'
                ], 500);
            }
            alert()->error('Error', 'An error occurred while updating profile.');
            return redirect()->back();
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Validation Error',
                    'message' => SweetAlertHelper::getValidationErrors($validator),
                    'icon' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'title' => 'Invalid Password',
                        'message' => 'Current password is incorrect.',
                        'icon' => 'error'
                    ], 422);
                }
                return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'title' => 'Success!',
                    'message' => 'Password changed successfully.',
                    'icon' => 'success'
                ]);
            }

            alert()->success('Success', 'Password changed successfully.');
            return redirect()->back();
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'title' => 'Error',
                    'message' => 'An error occurred while changing password.',
                    'icon' => 'error'
                ], 500);
            }
            alert()->error('Error', 'An error occurred while changing password.');
            return redirect()->back();
        }
    }


}

