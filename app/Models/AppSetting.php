<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    // Simple key/value settings table.
    // Digunakan oleh OwnerController untuk menyimpan konfigurasi aplikasi yang
    // tampil pada halaman booking customer dan halaman owner (misal teks dan foto antar jemput).
    protected $table = 'app_settings';
    protected $fillable = [
        'key',
        'value',
    ];
}
