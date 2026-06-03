<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tracking', function (Blueprint $table) {
            $table->id('id_tracking');
            $table->unsignedBigInteger('id_order');
            $table->enum('status', ['Menunggu Konfirmasi','Dijemput','Dicuci','Disetrika','Dikirim','Selesai','Dibatalkan']);
            $table->text('keterangan')->nullable();
            $table->timestamp('waktu_update')->useCurrent();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('id_order')->references('id_order')->on('orders')->cascadeOnDelete();
            $table->foreign('updated_by')->references('id_staff')->on('staff')->nullOnDelete();
        });
    }
    public function down(): void { Schema::dropIfExists('tracking'); }
};
