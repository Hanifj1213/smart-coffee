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
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_hp')->nullable();
            $table->integer('total_poin')->default(0);
            $table->decimal('total_pengeluaran', 10, 2)->default(0.00);
            $table->string('tier_status')->default('Bronze'); // Bronze, Silver, Gold
            $table->string('role')->default('member'); // admin, kasir, member
            $table->string('behavior_label')->nullable(); // For KNN training dataset labeling
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_hp', 'total_poin', 'total_pengeluaran', 'tier_status', 'role', 'behavior_label']);
        });
    }
};
