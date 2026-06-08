<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    // Tipe layanan (mis: reguler, express). Memiliki banyak katalog dan orders.
    protected $table = 'layanan';
    protected $primaryKey = 'id_layanan';
    protected $fillable = [
        'nama_layanan',
        'deskripsi',
        'is_active',
    ];

    // Cast is_active ke boolean untuk memudahkan logika aktif/nonaktif.
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function katalog(): HasMany
    {
        // Semua katalog yang termasuk di layanan ini
        return $this->hasMany(Katalog::class, 'id_layanan', 'id_layanan');
    }

    public function orders(): HasMany
    {
        // Orders yang memilih layanan ini
        return $this->hasMany(Order::class, 'id_layanan', 'id_layanan');
    }
}
