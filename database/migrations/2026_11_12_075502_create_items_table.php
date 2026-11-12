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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('item_name');
            $table->string('brand')->nullable();
            $table->string('category')->default('Mobile');
            $table->string('storage')->nullable();
            $table->string('color')->nullable();
            $table->text('specs')->nullable();
            $table->string('imei_1')->nullable()->index();
            $table->string('imei_2')->nullable();
            $table->string('serial_number')->nullable()->index();
            $table->string('condition')->default('used'); // new, used, refurbished
            $table->string('status')->default('in_stock'); // in_stock, sold, returned
            $table->decimal('purchase_price', 10, 2)->default(0.00);
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->string('warranty_status')->default('no_warranty'); // no_warranty, store_warranty, company_warranty
            $table->date('warranty_expiry_date')->nullable();
            $table->date('purchase_date');
            $table->string('payment_method')->default('cash'); // cash, upi, bank
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
