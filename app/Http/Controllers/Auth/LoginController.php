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
        // Jika sudah ada session login, redirect ke dashboard sesuai role
        if (Session::has('user_id')) {
            return redirect()->route(Session::get('role') . '.dashboard');
        }

        // Tampilkan halaman login dengan username hasil cookie remember_username jika tersedia
        return view('auth.login', [
            'rememberUsername' => request()->cookie('remember_username', ''),
        ]);
    }

    // ── LOGIN ────────────────────────────────────────────────
    public function login(Request $request)
    {
        // Validasi input login secara server-side
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // Bendera apakah user ingin menyimpan username di cookie
        $remember = $request->has('remember_me');

        // Cek role Owner terlebih dahulu, kemudian Staff, kemudian Customer
        $user = Owner::where('username', $credentials['username'])->where('is_active', 1)->first();
        if ($user && Hash::check($credentials['password'], $user->sandi_owner)) {
            // Simpan session dan catat login di tabel login_logs
            $this->setSession($user->id_owner, $user->username, $user->nama_owner, 'owner');
            $this->logLogin('owner', $user->id_owner, $request->ip());
            $response = redirect()->route('owner.dashboard');

            // Jika remember diaktifkan, set cookie username selama 120 menit
            if ($remember) {
                $response->cookie('remember_username', $credentials['username'], 120);
            } else {
                $response->withoutCookie('remember_username');
            }
            return $response;
        }

        $user = Staff::where('username', $credentials['username'])->where('is_active', 1)->first();
        if ($user && Hash::check($credentials['password'], $user->sandi)) {
            $this->setSession($user->id_staff, $user->username, $user->nama, 'staff');
            $this->logLogin('staff', $user->id_staff, $request->ip());
            $response = redirect()->route('staff.dashboard');
            if ($remember) {
                $response->cookie('remember_username', $credentials['username'], 120);
            }
            return $response;
        }

        $user = User::where('username', $credentials['username'])->where('is_active', 1)->first();
        if ($user && Hash::check($credentials['password'], $user->sandi_cust)) {
            $this->setSession($user->id_cust, $user->username, $user->nama_cust, 'customer');
            $this->logLogin('customer', $user->id_cust, $request->ip());
            $response = redirect()->route('customer.dashboard');
            if ($remember) {
                $response->cookie('remember_username', $credentials['username'], 120);
            }
            return $response;
        }

        // Jika tidak ditemukan kombinasi username/password, kembalikan error ke form
        return back()->withErrors(['login' => 'Username atau password salah!'])->withInput();
    }

    // ── LOGOUT ─────────────────────────────────────────────
    public function logout(Request $request)
    {
        // Hapus semua session user agar logout bersih
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
        // Validasi input registrasi customer
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'alpha_num', Rule::unique('users', 'username')],
            'notelp' => ['required', 'digits_between:6,20'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'username.alpha_num' => 'Username hanya boleh huruf dan angka.',
        ]);

        // Simpan user baru ke tabel users
        User::create([
            'nama_cust' => $data['nama'],
            'username' => $data['username'],
            'notelp_cust' => $data['notelp'],
            'sandi_cust' => Hash::make($data['password']),
            'alamat_cust' => $data['alamat'] ?? null,
            'is_active' => 1,
        ]);

        // Arahkan user ke halaman login setelah registrasi sukses
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // ── PRIVATE HELPERS ──────────────────────────────────────
    private function setSession(int $id, string $username, string $nama, string $role): void
    {
        // Simpan data dasar login ke session: id, username, nama, dan role
        Session::put('user_id', $id);
        Session::put('user', $username);
        Session::put('nama', $nama);
        Session::put('role', $role);
    }

    private function logLogin(string $role, int $actorId, string $ip): void
    {
        // Catat setiap login ke tabel login_logs untuk audit dan tracking
        \DB::table('login_logs')->insert([
            'role' => $role,
            'actor_id' => $actorId,
            'ip_address' => $ip,
            'login_time' => now(),
        ]);
    }
}
