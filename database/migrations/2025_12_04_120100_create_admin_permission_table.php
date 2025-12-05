<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_permission', function (Blueprint $table) {
            $table->id();
            $table->uuid('admin_id');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->unique(['admin_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_permission');
    }
};
