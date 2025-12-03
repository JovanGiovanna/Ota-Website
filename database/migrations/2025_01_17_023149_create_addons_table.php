<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('id_vendor')
                ->nullable() 
                ->constrained('vendor') 
                ->onDelete('set null'); 

            $table->string('addons', 255); 
            $table->string('desc', 500)->nullable();
            $table->enum('status', ['available', 'unavailable', 'draft' , 'publish'])->default('available');
            $table->integer('pax');
            $table->text('location'); 
            $table->string('phone');
            $table->decimal('basic_price', 10, 2); 
            $table->decimal('nta', 10, 2)->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0.00); 
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('discount_value', 8, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable()->comment('Jumlah Diskon Terhitung');
            $table->timestamp('discount_expires_at')->nullable();            
            $table->json('images')->nullable(); 

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addons');
    }
};