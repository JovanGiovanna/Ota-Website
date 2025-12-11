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
        // Update status enum for book_packages to include book, paid, payment_return
        DB::statement("ALTER TABLE `book_packages` MODIFY `status` ENUM('book','pending','paid','payment_return','completed','cancelled','confirmed') NOT NULL DEFAULT 'pending';");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `book_packages` MODIFY `status` ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending';");
    }
};
