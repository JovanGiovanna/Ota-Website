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
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['id_package']);
            $table->uuid('id_package')->nullable()->change();
            $table->foreign('id_package')->references('id')->on('packages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['id_package']);
            $table->uuid('id_package')->nullable(false)->change();
            $table->foreign('id_package')->references('id')->on('packages')->onDelete('cascade');
        });
    }
};
