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
            $table->string('status', 50)->default('available');
            $table->integer('pax'); 
            $table->decimal('price', 10, 2); 
            $table->boolean('publish')->default(false); 
            
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