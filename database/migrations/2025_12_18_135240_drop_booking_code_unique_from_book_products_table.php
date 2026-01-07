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
        Schema::table('book_products', function (Blueprint $table) {
            $table->dropUnique('book_products_booking_code_unique');
            $table->dropColumn('booking_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_products', function (Blueprint $table) {
            $table->string('booking_code')->unique()->nullable();
        });
    }
};
