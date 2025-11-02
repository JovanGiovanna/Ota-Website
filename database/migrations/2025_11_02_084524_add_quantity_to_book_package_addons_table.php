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
        Schema::table('book_package_addons', function (Blueprint $table) {
            $table->integer('quantity')->default(1)->after('id_addons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_package_addons', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
