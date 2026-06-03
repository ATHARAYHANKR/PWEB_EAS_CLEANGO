<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class LoginController extends Controller
{
    // ── SHOW LOGIN ──────────────────────────────────────────
    public function showLogin()
    {
        if (Session::has('user_id')) {
            return redirect()->route(Session::get('role') . '.dashboard');
        }
        return view('auth.login', [
            'rememberUsername' => request()->cookie('remember_username', ''),
        ]);
    }

    // ── LOGIN ────────────────────────────────────────────────
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $remember = $request->has('remember_me');

        $user = Owner::where('username', $credentials['username'])->where('is_active', 1)->first();
        if ($user && Hash::check($credentials['password'], $user->sandi_owner)) {
            $this->setSession($user->id_owner, $user->username, $user->nama_owner, 'owner');
            $this->logLogin('owner', $user->id_owner, $request->ip());
            $response = redirect()->route('owner.dashboard');
            if ($remember) $response->cookie('remember_username', $credentials['username'], 120);
            else $response->withoutCookie('remember_username');
            return $response;
        }

        $user = Staff::where('username', $credentials['username'])->where('is_active', 1)->first();
        if ($user && Hash::check($credentials['password'], $user->sandi)) {
            $this->setSession($user->id_staff, $user->username, $user->nama, 'staff');
            $this->logLogin('staff', $user->id_staff, $request->ip());
            $response = redirect()->route('staff.dashboard');
            if ($remember) $response->cookie('remember_username', $credentials['username'], 120);
            return $response;
        }

        $user = User::where('username', $credentials['username'])->where('is_active', 1)->first();
        if ($user && Hash::check($credentials['password'], $user->sandi_cust)) {
            $this->setSession($user->id_cust, $user->username, $user->nama_cust, 'customer');
            $this->logLogin('customer', $user->id_cust, $request->ip());
            $response = redirect()->route('customer.dashboard');
            if ($remember) $response->cookie('remember_username', $credentials['username'], 120);
            return $response;
        }

        return back()->withErrors(['login' => 'Username atau password salah!'])->withInput();
    }

    // ── LOGOUT ─────────────────────────────────────────────
    public function logout(Request $request)
    {
        Session::flush();
        return redirect()->route('login')->withoutCookie('remember_username');
    }

    // ── SHOW REGISTER ───────────────────────────────────────
    public function showRegister()
    {
        if (Session::has('user_id')) {
            return redirect()->route(Session::get('role') . '.dashboard');
        }
        return view('auth.register');
    }

    // ── REGISTER ─────────────────────────────────────────────
    public function register(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_num', Rule::unique('users', 'username')],
            'notelp' => ['required', 'digits_between:6,20'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'username.alpha_num' => 'Username hanya boleh huruf dan angka.',
        ]);

        User::create([
            'nama_cust' => $data['nama'],
            'username' => $data['username'],
            'notelp_cust' => $data['notelp'],
            'sandi_cust' => Hash::make($data['password']),
            'alamat_cust' => $data['alamat'] ?? null,
            'is_active' => 1,
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // ── PRIVATE HELPERS ──────────────────────────────────────
    private function setSession(int $id, string $username, string $nama, string $role): void
    {
        Session::put('user_id', $id);
        Session::put('user', $username);
        Session::put('nama', $nama);
        Session::put('role', $role);
    }

    private function logLogin(string $role, int $actorId, string $ip): void
    {
        \DB::table('login_logs')->insert([
            'role' => $role,
            'actor_id' => $actorId,
            'ip_address' => $ip,
            'login_time' => now(),
        ]);
    }
}
