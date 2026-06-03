<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_order');
            $table->string('kode_order', 20)->unique();
            $table->unsignedBigInteger('id_cust');
            $table->unsignedBigInteger('id_layanan');
            $table->unsignedBigInteger('id_staff')->nullable();
            $table->dateTime('tanggal_pesan')->useCurrent();
            $table->text('alamat_penjemputan');
            $table->dateTime('jadwal_jemput')->nullable();
            $table->text('catatan')->nullable();
            $table->decimal('total_harga', 10, 2)->default(0);
            $table->enum('status_order', ['Menunggu Konfirmasi','Dijemput','Dicuci','Disetrika','Dikirim','Selesai','Dibatalkan'])->default('Menunggu Konfirmasi');
            $table->timestamps();
            $table->foreign('id_cust')->references('id_cust')->on('users');
            $table->foreign('id_layanan')->references('id_layanan')->on('layanan');
            $table->foreign('id_staff')->references('id_staff')->on('staff')->nullOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('orders'); }
};
