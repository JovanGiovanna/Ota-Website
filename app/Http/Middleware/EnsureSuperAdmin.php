<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     * Only allow super admin or users with super_admin role
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if logged in as super_admin
        if (Auth::guard('super_admin')->check()) {
            return $next($request);
        }

        // Check if admin has super_admin role
        $admin = Auth::guard('admin')->user();
        if ($admin && $admin->roles()->where('key', 'super_admin')->exists()) {
            return $next($request);
        }

        // No super admin access
        abort(403, 'Anda tidak memiliki akses ke halaman ini. Hanya Super Admin yang dapat mengakses.');
    }
}
