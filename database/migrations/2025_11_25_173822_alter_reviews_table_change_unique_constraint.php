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
        Schema::table('reviews', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['user_id']);
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['package_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['addon_id']);

            // Drop the existing unique constraint
            $table->dropUnique(['user_id', 'booking_id']);

            // Add a new composite unique constraint that includes item IDs
            $table->unique(['user_id', 'booking_id', 'package_id', 'product_id', 'addon_id'], 'reviews_user_booking_item_unique');

            // Add back the foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('addon_id')->references('id')->on('addons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['user_id']);
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['package_id']);
            $table->dropForeign(['product_id']);
            $table->dropForeign(['addon_id']);

            // Drop the new unique constraint
            $table->dropUnique('reviews_user_booking_item_unique');

            // Restore the original unique constraint
            $table->unique(['user_id', 'booking_id']);

            // Add back the foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('addon_id')->references('id')->on('addons')->onDelete('cascade');
        });
    }
};
