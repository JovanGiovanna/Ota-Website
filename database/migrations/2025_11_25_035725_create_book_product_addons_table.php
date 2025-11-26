<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_product_addons', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Relasi ke tabel booking produknya
            $table->uuid('id_book'); // relasi ke book_products.id
            $table->uuid('id_product')->nullable(); // relasi ke products.id
            $table->uuid('id_addons')->nullable(); // relasi ke addons.id

            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('id_book')
                ->references('id')
                ->on('book_products')
                ->onDelete('cascade');

            $table->foreign('id_product')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->foreign('id_addons')
                ->references('id')
                ->on('addons')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_product_addons');
    }
};
