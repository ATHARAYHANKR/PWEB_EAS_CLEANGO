<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration {
    /**
     * Hash semua password plaintext yang sudah ada di database.
     * Jalankan SEKALI saja setelah deploy.
     */
    public function up(): void
    {
        // Hash password owner
        DB::table('owner')->get()->each(function ($row) {
            if (!str_starts_with($row->sandi_owner, '$2y$') && !str_starts_with($row->sandi_owner, '$argon')) {
                DB::table('owner')->where('id_owner', $row->id_owner)
                    ->update(['sandi_owner' => Hash::make($row->sandi_owner)]);
            }
        });

        // Hash password staff
        DB::table('staff')->get()->each(function ($row) {
            if (!str_starts_with($row->sandi, '$2y$') && !str_starts_with($row->sandi, '$argon')) {
                DB::table('staff')->where('id_staff', $row->id_staff)
                    ->update(['sandi' => Hash::make($row->sandi)]);
            }
        });

        // Hash password customer
        DB::table('users')->get()->each(function ($row) {
            if (!str_starts_with($row->sandi_cust, '$2y$') && !str_starts_with($row->sandi_cust, '$argon')) {
                DB::table('users')->where('id_cust', $row->id_cust)
                    ->update(['sandi_cust' => Hash::make($row->sandi_cust)]);
            }
        });
    }

    public function down(): void
    {
        // Tidak bisa di-rollback karena hash tidak bisa dikembalikan ke plaintext
    }
};
