<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SuperAdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // Allow access if authenticated as super_admin
        if (Auth::guard('super_admin')->check()) {
            return $next($request);
        }

        // Otherwise, check the specific guard
        if ($guard && Auth::guard($guard)->check()) {
            return $next($request);
        }

        // If no guard specified, check default web guard
        if (!$guard && Auth::check()) {
            return $next($request);
        }

        // Redirect to appropriate login if not authenticated
        if ($guard === 'admin') {
            return redirect()->route('admin.login');
        } elseif ($guard === 'vendor') {
            return redirect()->route('vendor.login');
        } else {
            return redirect()->route('login');
        }
    }
}
