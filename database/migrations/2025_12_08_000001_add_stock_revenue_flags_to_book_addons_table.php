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
        Schema::table('book_addons', function (Blueprint $table) {
            $table->boolean('stock_applied')->default(false)->after('notes');
            $table->boolean('revenue_applied')->default(false)->after('stock_applied');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_addons', function (Blueprint $table) {
            $table->dropColumn(['stock_applied', 'revenue_applied']);
        });
    }
};
