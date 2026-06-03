<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id_order';
    protected $fillable = [
        'kode_order',
        'id_cust',
        'id_layanan',
        'id_staff',
        'tanggal_pesan',
        'alamat_penjemputan',
        'jadwal_jemput',
        'catatan',
        'total_harga',
        'status_order',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
        'tanggal_pesan' => 'datetime',
        'jadwal_jemput' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_cust', 'id_cust');
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class, 'id_layanan', 'id_layanan');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'id_staff', 'id_staff');
    }

    public function detail(): HasOne
    {
        return $this->hasOne(OrderDetail::class, 'id_order', 'id_order');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'id_order', 'id_order');
    }

    public function tracking(): HasMany
    {
        return $this->hasMany(Tracking::class, 'id_order', 'id_order');
    }
}
