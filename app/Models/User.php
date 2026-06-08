<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Model untuk customers (users). Data user disimpan di tabel users.
    protected $table = 'users';
    protected $primaryKey = 'id_cust';

    protected $fillable = [
        'nama_cust',
        'username',
        'notelp_cust',
        'sandi_cust',
        'alamat_cust',
        'is_active',
    ];

    // Sandi (password) disembunyikan saat model diubah ke array atau JSON.
    protected $hidden = [
        'sandi_cust',
    ];

    // Cast is_active ke boolean agar pengecekan status user lebih mudah.
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relasi user -> orders
    public function orders()
    {
        return $this->hasMany(Order::class, 'id_cust', 'id_cust');
    }
}
