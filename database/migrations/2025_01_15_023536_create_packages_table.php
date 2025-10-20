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
        Schema::create('packages', function (Blueprint $table) {
            // Kolom Primary Key (UUID)
            $table->uuid('id')->primary();

            // Kolom Data Utama
            $table->string('name_package', 255);
            $table->string('slug')->unique(); // Untuk URL yang ramah SEO dan unik
            
            // Kolom Pendukung Konten
            $table->text('description')->nullable(); // Deskripsi lengkap package
            $table->string('image')->nullable(); // Path atau URL gambar utama package
            
            // Kolom Harga dan Waktu Publikasi
            $table->decimal('price_publish', 10, 2); 
            $table->dateTime('start_publish');
            $table->dateTime('end_publish')->nullable();

            // Kolom Status
            $table->boolean('is_active')->default(true); // Status paket (aktif/tidak aktif)
            
            // Kolom timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};