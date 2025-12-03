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
        Schema::create('city', function (Blueprint $table) { 
            $table->uuid('id')->primary();
            $table->foreignUuid('id_province')
                  ->constrained('province') 
                  ->onDelete('cascade');
                  
            $table->string('name', 100);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menggunakan 'cities'
        Schema::dropIfExists('city');
    }
};