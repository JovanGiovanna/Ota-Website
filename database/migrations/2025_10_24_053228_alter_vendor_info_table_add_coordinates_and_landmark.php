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
        Schema::table('vendor_info', function (Blueprint $table) {
            $table->decimal('coordinate_latitude', 10, 8)->nullable()->after('desc');
            $table->decimal('coordinate_longitude', 11, 8)->nullable()->after('coordinate_latitude');
            $table->text('landmark_description')->nullable()->after('coordinate_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_info', function (Blueprint $table) {
            $table->dropColumn(['coordinate_latitude', 'coordinate_longitude', 'landmark_description']);
        });
    }
};
