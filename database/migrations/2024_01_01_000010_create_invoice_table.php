<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('invoice', function (Blueprint $table) {
            $table->id('id_invoice');
            $table->unsignedBigInteger('id_bayar')->unique();
            $table->string('no_invoice', 50)->unique();
            $table->timestamp('tgl_invoice')->useCurrent();
            $table->string('nomor_wa', 20)->default('');
            $table->enum('status_kirim', ['Belum Dikirim','Terkirim','Gagal Kirim'])->default('Belum Dikirim');
            $table->timestamp('waktu_kirim')->nullable();
            $table->timestamps();
            $table->foreign('id_bayar')->references('id_bayar')->on('pembayaran');
        });
    }
    public function down(): void { Schema::dropIfExists('invoice'); }
};
