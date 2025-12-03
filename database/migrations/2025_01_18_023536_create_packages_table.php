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
            $table->uuid('id')->primary();
            $table->string('name_package', 255);
            $table->string('slug')->unique(); 
            $table->text('description')->nullable();
            $table->json('images')->nullable();
            $table->text('location');
            $table->string('phone');
            $table->decimal('nta', 15, 2); 
            $table->json('products_data')->nullable(); 
            $table->json('addons_data')->nullable();
            $table->decimal('pax_paid', 15, 2);
            $table->dateTime('start_publish');  
            $table->dateTime('end_publish')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
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