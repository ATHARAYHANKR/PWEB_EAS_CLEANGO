<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';
    protected $primaryKey = 'id_bayar';
    protected $fillable = [
        'id_order',
        'metode',
        'jumlah',
        'status_bayar',
        'catatan',
        'waktu_bayar',
        'dikonfirmasi_oleh',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'waktu_bayar' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }
}
