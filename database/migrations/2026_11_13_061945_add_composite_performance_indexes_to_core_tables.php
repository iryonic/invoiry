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
        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['tenant_id', 'invoice_date'], 'invoices_tenant_date_idx');
            $table->index(['tenant_id', 'payment_status'], 'invoices_tenant_status_idx');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->index(['tenant_id', 'status'], 'items_tenant_status_idx');
            $table->index(['tenant_id', 'imei_1'], 'items_tenant_imei1_idx');
            $table->index(['tenant_id', 'imei_2'], 'items_tenant_imei2_idx');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->index(['tenant_id', 'phone'], 'customers_tenant_phone_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_tenant_phone_idx');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex('items_tenant_status_idx');
            $table->dropIndex('items_tenant_imei1_idx');
            $table->dropIndex('items_tenant_imei2_idx');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_tenant_date_idx');
            $table->dropIndex('invoices_tenant_status_idx');
        });
    }
};
