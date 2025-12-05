<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SuperAdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage di route:
     * ->middleware('super_admin_access')
     * atau
     * ->middleware('super_admin_access:admin')
     * ->middleware('super_admin_access:vendor')
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // 1. Super admin selalu diizinkan
        if (Auth::guard('super_admin')->check()) {
            return $next($request);
        }

        // 2. Jika route mensyaratkan guard tertentu (admin, vendor, user)
        if ($guard !== null) {
            if (Auth::guard($guard)->check()) {
                return $next($request);
            }

            // Redirect ke login guard yang benar
            return $this->redirectToLogin($guard);
        }

        // 3. Jika tidak sebut guard sama sekali → wajib login super_admin
        return redirect()->route('login');
    }

    /**
     * Redirect helper
     */
    private function redirectToLogin($guard)
    {
        return match ($guard) {
            'admin'   => redirect()->route('admin.login'),
            'vendor'  => redirect()->route('vendor.login'),
            'user'    => redirect()->route('user.login'),
            default   => redirect()->route('login'),
        };
    }
}
