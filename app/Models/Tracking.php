<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tracking extends Model
{
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
        return $this->belongsTo(Order::class, 'id_order', 'id_order');
    }
}
