<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    // Invoice yang dibuat ketika pembayaran telah diverifikasi.
    // Tabel invoice menyimpan nomor faktur, nomor WA pelanggan, dan tanggal cetak.
    protected $table = 'invoice';
    protected $primaryKey = 'id_invoice';
    protected $fillable = [
        'id_bayar',
        'no_invoice',
        'nomor_wa',
        'tgl_invoice',
    ];

    // Cast tanggal invoice ke objek datetime untuk memudahkan format di view.
    protected $casts = [
        'tgl_invoice' => 'datetime',
    ];

    public function pembayaran(): BelongsTo
    {
        // Relasi ke record pembayaran
        return $this->belongsTo(Pembayaran::class, 'id_bayar', 'id_bayar');
    }
}
