<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Katalog extends Model
{
    protected $table = 'katalog';
    protected $primaryKey = 'id_katalog';
    protected $fillable = [
        'id_layanan',
        'jenis_layanan',
        'varian',
        'harga',
        'satuan',
        'deskripsi',
        'foto',
        'status',
    ];

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'id_layanan', 'id_layanan');
    }
}
