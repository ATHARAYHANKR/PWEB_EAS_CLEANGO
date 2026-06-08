<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    // Detail per-order: menyimpan item (katalog) yang dipesan, berat/jumlah, harga
    protected $table = 'order_detail';
    protected $primaryKey = 'id_detail';
    public $timestamps = false; // tabel tidak pakai timestamps
    protected $fillable = [
        'id_order',
        'id_katalog',
        'berat',
        'qty',
        'harga_satuan',
        'subtotal',
    ];

    public function order(): BelongsTo
    {
        // Referensi kembali ke Order utama
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }

    public function katalog(): BelongsTo
    {
        // Relasi ke Katalog untuk mengetahui nama/tipe layanan item
        return $this->belongsTo(Katalog::class, 'id_katalog', 'id_katalog');
    }
}
