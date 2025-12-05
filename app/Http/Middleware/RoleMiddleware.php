<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        // cek admin guard
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
        }
        // cek super admin guard
        else if (Auth::guard('super_admin')->check()) {
            $user = Auth::guard('super_admin')->user();
        }
        else {
            // tidak login di mana pun
            return redirect()->route('login');
        }

        // kalau user dari guard admin
        if ($user instanceof \App\Models\Admin) {
            if (!$user->hasRole($role)) {
                abort(403);
            }
        } else {
            // user adalah super_admin
            if ($role !== 'super_admin') {
                // super admin hanya punya akses ke role super_admin
                // atau bisa kamu izinkan kalau perlu
            }
        }

        return $next($request);
    }
}
