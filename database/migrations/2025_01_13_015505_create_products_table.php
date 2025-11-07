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
        Schema::create('products', function (Blueprint $table) {
            // Kolom Primary Key (UUID)
            $table->uuid('id')->primary();

            // Kolom Data Utama
            $table->string('name', 255);
            $table->string('image')->nullable();       // Path/URL gambar, boleh kosong
            $table->text('description');               // Menggunakan 'text' untuk deskripsi panjang

            // Kolom Harga
            $table->decimal('price', 10, 2);           // Menggunakan 'decimal' untuk harga (disarankan daripada 'integer')

            // Kolom Kunci Asing (Foreign Keys) - Menggunakan foreignUuid() untuk sintaks yang lebih rapi
            
            // id_category
            $table->foreignUuid('id_category')
                  ->constrained('categories')        // Asumsi nama tabel 'categories'
                  ->onDelete('cascade');
            
            // id_vendor
            $table->foreignUuid('id_vendor')
                  ->constrained('vendor')           // Menggunakan 'vendors' (plural) untuk mengikuti konvensi Laravel. Sesuaikan jika nama tabel Anda adalah 'vendor' (singular).
                  ->onDelete('cascade');
            $table->integer('pax'); 
            $table->integer('jumlah')->unsigned();     // Jumlah unit/stok yang tersedia
            $table->integer('max_adults')->default(2); // Kapasitas dewasa
            $table->integer('max_children')->default(1); // Kapasitas anak
            
            // Kolom Status
            $table->enum('status', ['available', 'unavailable', 'draft'])->default('available');
            
            // Kolom timestamps dan Soft Deletes (disarankan)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};