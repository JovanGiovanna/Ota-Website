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
        Schema::create('bookings', function (Blueprint $table) {
            
            $table->uuid('id')->primary();
            
            $table->foreignUuid('id_user')
                  ->constrained('users')
                  ->onDelete('cascade');
                  
            $table->foreignUuid('id_package')
                  ->constrained('packages')
                  ->onDelete('cascade');

            $table->string('booker_name', 100);    
            $table->string('booker_email', 100);
            $table->string('booker_telp', 20);     
            $table->string('booking_code')->unique(); // Kode unik untuk setiap pesanan
            
            $table->dateTime('checkin_appointment_start'); 
            $table->dateTime('checkout_appointment_end');   
            $table->integer('duration_days')->unsigned()->nullable(); 
        
            $table->integer('amount')->unsigned()->default(1); 
            $table->decimal('total_price', 15, 2); 
            // Status & Timestamps
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'checked_in', 'checked_out', 'maintenance'])
                  ->default('pending');
            $table->string('note')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};