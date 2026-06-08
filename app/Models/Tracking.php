<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tracking extends Model
{
    // Model untuk riwayat status order.
    // Tabel tracking menyimpan status, catatan, siapa yang mengubah, dan waktu update.
    // Timestamps default Eloquent dimatikan karena kolom custom `waktu_update` dipakai.
    public $timestamps = false;

    protected $table = 'tracking';
    protected $primaryKey = 'id_tracking';
    protected $fillable = [
        'id_order',
        'status',
        'keterangan',
        'updated_by',
        'waktu_update',
    ];

    protected $casts = [
        'waktu_update' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        // Relasi kembali ke order
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }
}
