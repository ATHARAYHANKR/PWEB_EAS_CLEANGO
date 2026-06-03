<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): mixed
    {
        if (!Session::has('user_id')) {
            return redirect()->route('login');
        }

        $userRole = Session::get('role');

        // Pecah roles yang mungkin comma-separated (misal: 'customer,staff,owner')
        $allowedRoles = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $role) {
                $allowedRoles[] = trim($role);
            }
        }

        if (!in_array($userRole, $allowedRoles)) {
            return redirect()->route($userRole . '.dashboard');
        }

        return $next($request);
    }
}
