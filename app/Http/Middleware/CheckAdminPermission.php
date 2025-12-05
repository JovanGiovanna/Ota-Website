<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminPermission
{
    /**
     * Handle an incoming request.
     * Example usage:
     *    ->middleware('admin.permission:packages.view')
     */
    public function handle(Request $request, Closure $next, $permissionKey = null)
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

        // If no specific permission → allow (admin is authenticated)
        if (!$permissionKey) {
            return $next($request);
        }

        // Check permission
        if (method_exists($admin, 'hasPermission') && $admin->hasPermission($permissionKey)) {
            return $next($request);
        }

        // No permission
        abort(403, 'You do not have permission to access this resource.');
    }
}
