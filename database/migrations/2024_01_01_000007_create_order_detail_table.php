<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('order_detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_order');
            $table->unsignedBigInteger('id_katalog');
            $table->decimal('berat', 8, 2)->nullable();
            $table->integer('qty')->nullable();
            $table->decimal('harga_satuan', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->foreign('id_order')->references('id_order')->on('orders')->cascadeOnDelete();
            $table->foreign('id_katalog')->references('id_katalog')->on('katalog');
        });
    }
    public function down(): void { Schema::dropIfExists('order_detail'); }
};
