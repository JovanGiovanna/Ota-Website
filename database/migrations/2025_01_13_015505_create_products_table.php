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
            $table->text('location'); 
            $table->string('phone')->nullable();

            $table->decimal('basic_price', 10, 2); 
            $table->decimal('nta', 10, 2); 

            $table->decimal('tax_rate', 5, 2)->default(0.00)->comment('Persentase Pajak'); 
            
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable()->comment('Tipe Diskon');
            $table->decimal('discount_value', 10, 2)->nullable()->comment('Nilai Diskon');
            $table->decimal('discount_amount', 10, 2)->nullable()->comment('Jumlah Diskon Terhitung');
            $table->timestamp('discount_expires_at')->nullable()->comment('Kadaluarsa Diskon');
            
            $table->foreignUuid('id_category')
                  ->constrained('categories') 
                  ->onDelete('cascade');
            
            $table->foreignUuid('id_vendor')
                  ->constrained('vendor') 
                  ->onDelete('cascade');
                  
            $table->integer('pax'); 
            $table->integer('jumlah')->unsigned(); 
            
            $table->enum('status', ['available', 'unavailable', 'draft' , 'publish'])->default('available');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};