<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = $user->role instanceof \App\Enums\UserRole ? $user->role->value : $user->role;

        if (strtolower($userRole) !== strtolower($role)) {
            abort(403);
        }

        return $next($request);
    }
}
