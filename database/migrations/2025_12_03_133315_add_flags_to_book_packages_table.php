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
        Schema::table('book_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('book_packages', 'stock_applied')) {
                $table->boolean('stock_applied')->default(false)->after('total_price');
            }
            if (!Schema::hasColumn('book_packages', 'revenue_applied')) {
                $table->boolean('revenue_applied')->default(false)->after('stock_applied');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_packages', function (Blueprint $table) {
            if (Schema::hasColumn('book_packages', 'revenue_applied')) {
                $table->dropColumn('revenue_applied');
            }
            if (Schema::hasColumn('book_packages', 'stock_applied')) {
                $table->dropColumn('stock_applied');
            }
        });
    }
};
