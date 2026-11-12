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
        Schema::table('tenants', function (Blueprint $table) {
            $table->decimal('default_gst_rate', 5, 2)->default(18.00)->after('gstin');
            $table->boolean('is_tax_inclusive')->default(true)->after('default_gst_rate');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 2)->default(18.00)->after('is_gst');
            $table->boolean('is_tax_inclusive')->default(true)->after('tax_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['default_gst_rate', 'is_tax_inclusive']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['tax_rate', 'is_tax_inclusive']);
        });
    }
};
