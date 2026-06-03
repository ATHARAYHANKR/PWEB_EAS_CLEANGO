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

    protected $hidden = [
        'sandi_cust',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_cust', 'id_cust');
    }
}
