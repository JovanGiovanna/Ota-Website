<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('book_products', function (Blueprint $table) {
            // Kolom Primary Key dan UUID
            $table->uuid('id')->primary();
            $table->uuid('id_user')->index();
            $table->uuid('id_product')->index();
            $table->dateTime('checkin_appointment_start_datetime');
            $table->dateTime('checkout_appointment_end_datetime');
            $table->decimal('amount', 10, 2); 
            $table->string('booker_name', 150);
            $table->string('booker_email', 150);
            $table->string('booker_telp', 20)->nullable();
            $table->timestamps();

            // Opsional: Definisi Foreign Key Constraints
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_product')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Kembalikan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_products');
    }
};