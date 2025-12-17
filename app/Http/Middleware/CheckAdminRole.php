<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     * Example usage:
     *    ->middleware('admin.role:super_admin,manager')
     */
    public function handle(Request $request, Closure $next, ...$roleKeys)
    {
        // Super admin bypass all
        if (Auth::guard('super_admin')->check()) {
            return $next($request);
        }

        // Check logged in admin
        $admin = Auth::guard('admin')->user();
        if (!$admin) {
            // Not logged in as admin
            return redirect()->route('admin.login');
        }

        // If no specific roles required → allow (admin is authenticated)
        if (empty($roleKeys)) {
            return $next($request);
        }

        // Check if admin has any of the required roles
        $hasRole = false;
        foreach ($roleKeys as $roleKey) {
            if ($admin->roles()->where('key', $roleKey)->exists()) {
                $hasRole = true;
                break;
            }
        }

        if ($hasRole) {
            return $next($request);
        }

        // No required role
        abort(403, 'You do not have the required role to access this resource.');
    }
}
