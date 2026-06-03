<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    public $timestamps = false;

    protected $table = 'staff';
    protected $primaryKey = 'id_staff';
    protected $fillable = [
        'nama',
        'username',
        'notelp',
        'sandi',
        'alamat',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'sandi',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'id_staff', 'id_staff');
    }
}
