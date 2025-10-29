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
        Schema::create('addons', function (Blueprint $table) {
            // Kolom Primary Key (UUID)
            $table->uuid('id')->primary();

            // Kolom Foreign Key (UUID) - DISIMPLIFIKASI
            // Menggunakan constrained() untuk sintaks yang lebih rapi
            $table->foreignUuid('id_vendor')
                  ->nullable()                  // <<< HARUS NULLABLE untuk ON DELETE SET NULL
                  ->constrained('vendor')      // MENGGANTIKAN references('id')->on('vendor')
                  ->onDelete('set null');       // Aturan penghapusan

            // Kolom Data
            $table->string('addons', 255);      // Nama addon
            $table->string('desc', 500)->nullable(); // Deskripsi
            $table->string('status', 50)->default('available'); // Status
            $table->decimal('price', 10, 2);    // Harga
            $table->boolean('publish')->default(false); // Status publikasi
            
            // Kolom tambahan
            $table->string('image')->nullable(); 

            // Kolom timestamps
            $table->timestamps();

            // Soft Deletes
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addons');
    }
};