<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    protected $table = 'layanan';
    protected $primaryKey = 'id_layanan';
    protected $fillable = [
        'nama_layanan',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function katalog(): HasMany
    {
        return $this->hasMany(Katalog::class, 'id_layanan', 'id_layanan');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'id_layanan', 'id_layanan');
    }
}
