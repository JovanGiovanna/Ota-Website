<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_package_addons', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(Str::uuid());
            $table->uuid('id_book');
            $table->uuid('id_addons');
            $table->timestamps();

            $table->foreign('id_book')
                ->references('id')
                ->on('bookings')
                ->onDelete('cascade');

            $table->foreign('id_addons')
                ->references('id')
                ->on('addons')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_package_addons');
    }
};
