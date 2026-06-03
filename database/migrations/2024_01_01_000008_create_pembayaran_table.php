<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_bayar');
            $table->unsignedBigInteger('id_order')->unique();
            $table->enum('metode', ['QRIS'])->default('QRIS');
            $table->decimal('jumlah', 10, 2);
            $table->enum('status_bayar', ['Pending','Menunggu Konfirmasi','Lunas','Gagal'])->default('Pending');
            $table->string('bukti_transfer', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('waktu_bayar')->nullable();
            $table->unsignedBigInteger('dikonfirmasi_oleh')->nullable();
            $table->timestamps();
            $table->foreign('id_order')->references('id_order')->on('orders');
            $table->foreign('dikonfirmasi_oleh')->references('id_staff')->on('staff')->nullOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('pembayaran'); }
};
