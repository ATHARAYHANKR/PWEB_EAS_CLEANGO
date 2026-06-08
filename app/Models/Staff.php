<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    public $timestamps = false;
    // Staf yang mengelola orders. Tidak menggunakan timestamps pada tabel.
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

    // Cast is_active ke boolean agar status staf bisa langsung dievaluasi.
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Sandi staf disembunyikan saat serialisasi model.
    protected $hidden = [
        'sandi',
    ];

    public function orders(): HasMany
    {
        // Orders yang ditangani staf ini
        return $this->hasMany(Order::class, 'id_staff', 'id_staff');
    }
}
