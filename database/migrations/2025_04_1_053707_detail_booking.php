<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_booking', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('booking_id'); //booking
            $table->string('booker_name'); //nama tamu
            $table->uuid('product_id'); // kamar yang disewa
            $table->string('special_request')->nullable();
            $table->integer('adults'); // jumlah orang dewasa
            $table->integer('children')->default(0); // jumlah anak (default 0)
            $table->timestamps();

            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_booking');
        
    }
};
