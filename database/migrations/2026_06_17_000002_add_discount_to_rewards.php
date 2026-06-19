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
        Schema::table('rewards', function (Blueprint $table) {
            // Percentage discount this reward grants when redeemed (0 = not a discount voucher)
            $table->unsignedTinyInteger('discount_percent')->default(0)->after('poin_cost');
        });

        Schema::table('reward_redemptions', function (Blueprint $table) {
            // Snapshot of the discount at redemption time so it can be applied as a promo code
            $table->unsignedTinyInteger('discount_percent')->default(0)->after('poin_spent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
        });

        Schema::table('reward_redemptions', function (Blueprint $table) {
            $table->dropColumn('discount_percent');
        });
    }
};
