<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('name', 255);
            $table->json('images')->nullable(); 
            $table->text('description'); 

            $table->decimal('price', 10, 2); 

            $table->foreignUuid('id_category')
                  ->constrained('categories') 
                  ->onDelete('cascade');
            
            $table->foreignUuid('id_vendor')
                  ->constrained('vendor') 
                  ->onDelete('cascade');
                  
            $table->integer('pax'); 
            $table->integer('jumlah')->unsigned(); 
            $table->integer('max_adults')->default(2); 
            $table->integer('max_children')->default(1); 
            
            $table->enum('status', ['available', 'unavailable', 'draft'])->default('available');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};