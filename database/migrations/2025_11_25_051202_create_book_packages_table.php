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
        Schema::create('book_packages', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Primary key UUID

            // Foreign key

            $table->foreignUuid('id_book')
                  ->references('id')->on('bookings')
                  ->onDelete('cascade');

            $table->foreignUuid('id_user')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreignUuid('id_package')
                  ->references('id')->on('packages')
                  ->onDelete('cascade');

            // Waktu booking
            $table->dateTime('checkin_appointment_start');
            $table->dateTime('checkout_appointment_end')->nullable();

            // Info pemesan
            $table->string('booker_name', 100);
            $table->string('booker_email')->nullable();
            $table->string('booker_telp', 20)->nullable();

            // Detail tambahan
            $table->string('booking_code')->unique();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])
                  ->default('pending');

            $table->decimal('total_price', 10, 2);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_packages');
    }
};
