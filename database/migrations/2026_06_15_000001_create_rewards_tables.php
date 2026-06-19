<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('kategori')->default('Produk'); // Produk, Voucher, Merchandise
            $table->unsignedInteger('poin_cost');
            $table->integer('stok')->nullable(); // null = unlimited
            $table->string('icon')->default('🎁');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('reward_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reward_id')->constrained()->onDelete('cascade');
            $table->string('reward_nama'); // snapshot of reward name at redemption time
            $table->unsignedInteger('poin_spent');
            $table->string('kode_voucher')->unique();
            $table->string('status')->default('Completed'); // Completed, Used
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_redemptions');
        Schema::dropIfExists('rewards');
    }
};
