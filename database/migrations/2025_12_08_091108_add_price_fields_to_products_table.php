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
        Schema::table('products', function (Blueprint $table) {
            // Price fields: NTA + Upsell, then after discount
            $table->decimal('total_price_before_discount', 15, 2)->nullable()->default(0)->after('discount_amount')->comment('NTA + Upsell');
            $table->decimal('final_price', 15, 2)->nullable()->default(0)->after('total_price_before_discount')->comment('Final price after discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['total_price_before_discount', 'final_price']);
        });
    }
};
