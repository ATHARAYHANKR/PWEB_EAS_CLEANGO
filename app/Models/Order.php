<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    // Model untuk tabel `orders`. Primary key kustom `id_order`.
    protected $table = 'orders';
    protected $primaryKey = 'id_order';
    // Field yang boleh diisi mass-assignment (digunakan oleh controller saat create/update)
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

    // Casts memudahkan manipulasi tipe data: total_harga sebagai decimal dan tanggal sebagai objek datetime.
    protected $casts = [
        'total_harga' => 'decimal:2',
        'tanggal_pesan' => 'datetime',
        'jadwal_jemput' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        // Relasi ke model User (customer). Kunci lokal `id_cust`.
        return $this->belongsTo(User::class, 'id_cust', 'id_cust');
    }

    public function layanan(): BelongsTo
    {
        // Relasi ke layanan (jenis laundry)
        return $this->belongsTo(Layanan::class, 'id_layanan', 'id_layanan');
    }

    public function staff(): BelongsTo
    {
        // Relasi staf yang mengerjakan order (boleh null jika belum diambil)
        return $this->belongsTo(Staff::class, 'id_staff', 'id_staff');
    }

    public function detail(): HasOne
    {
        // Satu Order punya satu OrderDetail (berat, jumlah pakaian, dsb.)
        return $this->hasOne(OrderDetail::class, 'id_order', 'id_order');
    }

    public function pembayaran(): HasOne
    {
        // Satu order -> satu pembayaran (bukti transfer, status)
        return $this->hasOne(Pembayaran::class, 'id_order', 'id_order');
    }

    public function tracking(): HasMany
    {
        // Riwayat status/order tracking (multiple entries)
        return $this->hasMany(Tracking::class, 'id_order', 'id_order');
    }
}
