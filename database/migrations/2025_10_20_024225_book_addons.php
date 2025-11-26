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
        Schema::create('book_addons', function (Blueprint $table) {
            // Kolom Primary Key (UUID)
            $table->uuid('id')->primary();

            // Kolom Foreign Keys (UUID)
            // Asumsi: tabel 'users' dan 'addons' menggunakan UUID untuk ID mereka.
            $table->foreignUuid('id_book')   // Ini penting, relasi ke Booking
          ->references('id')->on('bookings')
          ->onDelete('cascade');

            $table->foreignUuid('id_user')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
                  
            $table->foreignUuid('id_addon')
                  ->references('id')->on('addons')
                  ->onDelete('cascade');
            
            // Kolom Waktu Reservasi
            $table->dateTime('checkin_appointment_start'); 
            $table->dateTime('checkout_appointment_end')->nullable(); 

            // Kolom Kuantitas dan Harga (Memperbaiki 'amount' dari datetime ke integer/decimal)
            $table->integer('amount')->unsigned(); // Jumlah/kuantitas addon yang dipesan
            $table->decimal('total_price', 10, 2); // Kolom tambahan untuk total harga

            // Kolom Detail Pemesan
            $table->string('booker_name', 100);
            $table->string('booker_email')->nullable();
            $table->string('booker_telp', 20)->nullable();
            // Kolom Pendukung Tambahan yang Umum
            $table->string('booking_code')->unique(); // Kode unik untuk setiap pesanan
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])
                  ->default('pending'); // Status pesanan
            $table->text('notes')->nullable(); // Catatan tambahan dari pemesan
            
            // Kolom timestamps
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_addons');
    }
};