<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_cust');
            $table->string('nama_cust', 100);
            $table->string('username', 50)->unique();
            $table->string('notelp_cust', 20);
            $table->string('sandi_cust', 255);
            $table->text('alamat_cust')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('users'); }
};
