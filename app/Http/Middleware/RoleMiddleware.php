<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): mixed
    {
        // Pastikan ada session user_id (user sudah login). Jika tidak, arahkan ke halaman login.
        if (!Session::has('user_id')) {
            return redirect()->route('login');
        }

        // Ambil role dari session (diset saat login, misal 'customer', 'staff', 'owner')
        $userRole = Session::get('role');

        // Middleware menerima parameter seperti role:customer,staff,owner
        // Pecah roles yang dikirim oleh route group menjadi array flat.
        $allowedRoles = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $role) {
                $allowedRoles[] = trim($role);
            }
        }

        // Jika role session tidak ada dalam daftar yang diizinkan,
        // redirect kembali ke dashboard role yang sedang aktif.
        if (!in_array($userRole, $allowedRoles)) {
            return redirect()->route($userRole . '.dashboard');
        }

        // Jika validasi role berhasil, lanjutkan request ke controller.
        return $next($request);
    }
}
