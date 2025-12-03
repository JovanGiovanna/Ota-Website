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
        Schema::table('packages', function (Blueprint $table) {
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable()->after('tax_rate');
            $table->decimal('discount_value', 15, 2)->nullable()->after('discount_type');
            $table->decimal('discount_amount', 15, 2)->nullable()->after('discount_value');
            $table->dateTime('discount_expires_at')->nullable()->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'discount_amount', 'discount_expires_at']);
        });
    }
};
