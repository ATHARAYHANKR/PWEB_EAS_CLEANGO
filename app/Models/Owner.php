<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    // Model untuk pemilik/administrator aplikasi.
    // Owner biasanya mengelola setting, laporan, katalog, dan staff.
    protected $table = 'owner';
    protected $primaryKey = 'id_owner';
    protected $fillable = [
        'nama_owner',
        'username',
        'notelp_owner',
        'sandi_owner',
        'alamat_owner',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'sandi_owner',
    ];
}
