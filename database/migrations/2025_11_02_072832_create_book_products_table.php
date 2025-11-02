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
            // Drop old columns
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_product']);
            $table->dropColumn(['id_user', 'checkin_appointment_start_datetime', 'checkout_appointment_end_datetime', 'booker_name', 'booker_email', 'booker_telp']);

            // Change amount to integer
            $table->integer('amount')->unsigned()->change();

            // Add new columns
            $table->uuid('id_book')->after('id');
            $table->decimal('total_price', 15, 2)->after('amount');

            // Add new foreign keys
            $table->foreign('id_book')
                ->references('id')
                ->on('bookings')
                ->onDelete('cascade');

            $table->foreign('id_product')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_products', function (Blueprint $table) {
            // Drop new foreign keys and columns
            $table->dropForeign(['id_book']);
            $table->dropForeign(['id_product']);
            $table->dropColumn(['id_book', 'total_price']);

            // Revert amount back to decimal
            $table->decimal('amount', 10, 2)->change();

            // Add back old columns
            $table->uuid('id_user')->after('id');
            $table->dateTime('checkin_appointment_start_datetime')->after('id_product');
            $table->dateTime('checkout_appointment_end_datetime')->after('checkin_appointment_start_datetime');
            $table->string('booker_name', 150)->after('amount');
            $table->string('booker_email', 150)->after('booker_name');
            $table->string('booker_telp', 20)->nullable()->after('booker_email');

            // Add back old foreign keys
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_product')->references('id')->on('products')->onDelete('cascade');
        });
    }
};
