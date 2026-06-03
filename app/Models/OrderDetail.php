<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    protected $table = 'order_detail';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;
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
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }

    public function katalog(): BelongsTo
    {
        return $this->belongsTo(Katalog::class, 'id_katalog', 'id_katalog');
    }
}
