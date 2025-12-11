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
            // Add quantity column after checkin_appointment_end
            $table->integer('quantity')->unsigned()->default(1)->after('checkout_appointment_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_packages', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
