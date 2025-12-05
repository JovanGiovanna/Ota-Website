<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize existing values that may have used different naming
        DB::table('bookings')->where('status', 'payment return')->update(['status' => 'payment_return']);

        // Ensure we replace any empty/invalid status with default 'book'
        DB::table('bookings')->whereNull('status')->orWhere('status', '')->update(['status' => 'book']);

        // Alter the enum to include all statuses used in the app
        // Note: uses raw statement because Blueprint has limited enum change support
        DB::statement("ALTER TABLE `bookings` MODIFY `status` ENUM('book','pending','paid','payment_return','completed','cancelled','confirmed','checked_in','checked_out','maintenance') NOT NULL DEFAULT 'book';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to a simpler set (closest to original). This may fail if rows contain values not in the list.
        DB::statement("ALTER TABLE `bookings` MODIFY `status` ENUM('book','paid','completed','cancelled','payment return','maintenance') NOT NULL DEFAULT 'book';");
    }
};
