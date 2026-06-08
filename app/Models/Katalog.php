<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Katalog extends Model
{
    // Item katalog: jenis layanan/varian yang dapat dipesan pada form booking.
    // Masing-masing entry bisa memiliki foto, harga, satuan, dan status aktif/nonaktif.
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
        // Hubungan ke tabel layanan (mis. reguler, express)
        return $this->belongsTo(Layanan::class, 'id_layanan', 'id_layanan');
    }
}
