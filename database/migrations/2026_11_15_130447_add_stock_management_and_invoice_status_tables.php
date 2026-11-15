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
        // 1. Add quantity and is_device to items
        Schema::table('items', function (Blueprint $table) {
            $table->integer('quantity')->default(1)->after('status');
            $table->boolean('is_device')->default(true)->after('quantity');
        });

        // 2. Add status to invoices (draft, finalized, cancelled)
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('status')->default('finalized')->after('payment_status')->index();
        });

        // 3. Create stock_adjustments table for comprehensive inventory movement tracking
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->string('adjustment_type')->index(); // manual_add, manual_reduce, manual_set, sale, invoice_restored, invoice_edited, damage, return, initial
            $table->integer('quantity_change');
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'is_device']);
        });
    }
};
