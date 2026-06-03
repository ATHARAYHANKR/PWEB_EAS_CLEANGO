<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CleanGoSeeder extends Seeder
{
    public function run(): void
    {
        // Layanan
        DB::table('layanan')->insert([
            ['nama_layanan' => 'Cuci Kering',    'deskripsi' => 'Layanan cuci dan pengeringan standar', 'is_active' => 1, 'created_at' => now()],
            ['nama_layanan' => 'Cuci Setrika',   'deskripsi' => 'Layanan cuci lengkap dengan setrika',  'is_active' => 1, 'created_at' => now()],
            ['nama_layanan' => 'Setrika Saja',   'deskripsi' => 'Khusus setrika pakaian',               'is_active' => 1, 'created_at' => now()],
            ['nama_layanan' => 'Laundry Sepatu', 'deskripsi' => 'Pembersihan khusus sepatu dan tas',    'is_active' => 1, 'created_at' => now()],
        ]);

        // Katalog
        DB::table('katalog')->insert([
            ['id_layanan'=>1,'jenis_layanan'=>'','varian'=>'Regular','harga'=>7000, 'satuan'=>'kg','status'=>'Aktif','created_at'=>now()],
            ['id_layanan'=>1,'jenis_layanan'=>'','varian'=>'Express','harga'=>12000,'satuan'=>'kg','status'=>'Aktif','created_at'=>now()],
            ['id_layanan'=>2,'jenis_layanan'=>'','varian'=>'Regular','harga'=>10000,'satuan'=>'kg','status'=>'Aktif','created_at'=>now()],
            ['id_layanan'=>2,'jenis_layanan'=>'','varian'=>'Express','harga'=>15000,'satuan'=>'kg','status'=>'Aktif','created_at'=>now()],
            ['id_layanan'=>3,'jenis_layanan'=>'','varian'=>'Regular','harga'=>6000, 'satuan'=>'kg','status'=>'Aktif','created_at'=>now()],
            ['id_layanan'=>4,'jenis_layanan'=>'','varian'=>'Regular','harga'=>20000,'satuan'=>'pcs','status'=>'Aktif','created_at'=>now()],
            ['id_layanan'=>4,'jenis_layanan'=>'','varian'=>'Express','harga'=>30000,'satuan'=>'pcs','status'=>'Aktif','created_at'=>now()],
        ]);

        // Owner
        DB::table('owner')->insert([
            'nama_owner'   => 'Asa Owner',
            'username'     => 'owner',
            'notelp_owner' => '081234567890',
            'sandi_owner'  => Hash::make('owner123'),
            'is_active'    => 1,
            'created_at'   => now(),
        ]);

        // Staff
        DB::table('staff')->insert([
            'nama'       => 'Karimah Staff',
            'username'   => 'staff',
            'notelp'     => '081111111111',
            'sandi'      => Hash::make('staff123'),
            'alamat'     => 'Jl. Melati No. 3',
            'is_active'  => 1,
            'created_at' => now(),
        ]);

        // Customer
        DB::table('users')->insert([
            'nama_cust'   => 'Dhira Cust',
            'username'    => 'dhira',
            'notelp_cust' => '084444444444',
            'sandi_cust'  => Hash::make('dhira123'),
            'alamat_cust' => 'Jl. Mawar No. 10',
            'is_active'   => 1,
            'created_at'  => now(),
        ]);
    }
}
