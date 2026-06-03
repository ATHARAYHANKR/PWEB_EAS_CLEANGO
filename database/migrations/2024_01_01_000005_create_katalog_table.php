<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('katalog', function (Blueprint $table) {
            $table->id('id_katalog');
            $table->unsignedBigInteger('id_layanan');
            $table->string('jenis_layanan', 100)->default('');
            $table->enum('varian', ['Regular','Express','Hemat'])->default('Regular');
            $table->decimal('harga', 10, 2);
            $table->enum('satuan', ['kg','pcs'])->default('kg');
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['Aktif','Nonaktif'])->default('Aktif');
            $table->timestamps();
            $table->foreign('id_layanan')->references('id_layanan')->on('layanan');
        });
    }
    public function down(): void { Schema::dropIfExists('katalog'); }
};
