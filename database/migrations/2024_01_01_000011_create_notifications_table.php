<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['customer','staff','owner']);
            $table->unsignedBigInteger('actor_id');
            $table->string('title', 150);
            $table->text('message');
            $table->string('link', 255)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->index(['role','actor_id','is_read','created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('notifications'); }
};
