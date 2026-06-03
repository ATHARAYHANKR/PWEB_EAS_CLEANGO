<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tambah kolom foto ke tabel katalog
        Schema::table('katalog', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->after('deskripsi');
        });

        // Buat tabel app_settings untuk menyimpan foto antar jemput & setting lain
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('app_settings')->insert([
            ['key' => 'antar_jemput_foto',  'value' => null, 'created_at' => now()],
            ['key' => 'antar_jemput_judul', 'value' => 'Antar Jemput', 'created_at' => now()],
            ['key' => 'antar_jemput_desc',  'value' => 'Kami siap menjemput & mengantar pakaian Anda kapanpun. Gratis Antar Jemput untuk radius maksimal 4 Km dari outlet terdekat, dengan minimal transaksi Rp 75.000', 'created_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
        Schema::table('katalog', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};
