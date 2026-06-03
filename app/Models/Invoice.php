<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $table = 'invoice';
    protected $primaryKey = 'id_invoice';
    protected $fillable = [
        'id_bayar',
        'no_invoice',
        'nomor_wa',
        'tgl_invoice',
    ];

    protected $casts = [
        'tgl_invoice' => 'datetime',
    ];

    public function pembayaran(): BelongsTo
    {
        return $this->belongsTo(Pembayaran::class, 'id_bayar', 'id_bayar');
    }
}
