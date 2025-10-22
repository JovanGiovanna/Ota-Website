<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
public function index(Request $request)
{
    $query = User::query();

    // Pencarian berdasarkan nama
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Filter berdasarkan role
    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    // Ambil data users
    $users = $query->get(); // Jika banyak data, kamu bisa menggunakan paginate()

    // Hitung jumlah user dan admin
    $userCount = User::whereRaw('LOWER(role) = ?', ['user'])->count();
    $adminCount = User::whereRaw('LOWER(role) = ?', ['admin'])->count();


    $users = $query->paginate(10);

        // Hitung jumlah user dan admin
        $userCount = User::whereRaw('LOWER(role) = ?', ['user'])->count();
        $adminCount = User::whereRaw('LOWER(role) = ?', ['admin'])->count();


        // Kirim ke view
        return view('Pages.ListUsers', compact('users', 'userCount', 'adminCount'));
}

    public function register(Request $request)
    {
        $validator = Validator::make($request->all() ,[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:' . implode(',', UserRole::getValues()),
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
            'role' => UserRole::from($request->role),
        ]);

        $token = $user->createToken('auth_token', [$user->role->value])->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function registerWeb(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed', // pastikan form ada password_confirmation
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'user', // fix: default user aja
    ]);

    Auth::login($user);

    return redirect()->route('landing')->with('success', 'Registrasi berhasil, silakan login.');
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

        $token = $user->createToken('auth_token', [$user->role->value])->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
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
    Auth::logout(); // keluarin user dari session

    $request->session()->invalidate(); // invalidate session
    $request->session()->regenerateToken(); // regenerate CSRF token

    return redirect()->route('login'); // langsung balik ke login
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
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        $user = Auth::user();

        if (strtolower($user->role->value) === 'admin') {
        return redirect()->route('admin.admin.dashboard');
}
        // default untuk user
        return redirect()->route('landing');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ])->withInput();
}


}

