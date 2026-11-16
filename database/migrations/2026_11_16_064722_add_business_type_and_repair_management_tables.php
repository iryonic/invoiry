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
        // 1. Add business_type and repair prefix/counter to tenants
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('business_type')->default('selling')->after('plan')->index();
            $table->string('repair_job_prefix')->default('REP-')->after('invoice_counter');
            $table->integer('repair_job_counter')->default(1)->after('repair_job_prefix');
        });

        // 2. Create spare_parts table
        Schema::create('spare_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('name');
            $table->string('category')->default('Display / Screen');
            $table->string('brand')->nullable();
            $table->string('compatible_models')->nullable();
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->text('specifications')->nullable();
            $table->decimal('purchase_price', 12, 2)->default(0.00);
            $table->decimal('selling_price', 12, 2)->default(0.00);
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(5);
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'category']);
            $table->index(['tenant_id', 'sku']);
            $table->index(['tenant_id', 'barcode']);
        });

        // 3. Create repair_jobs table
        Schema::create('repair_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('job_number');
            $table->string('device_brand');
            $table->string('device_model');
            $table->string('imei')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('device_color')->nullable();
            $table->string('device_password')->nullable();
            $table->text('device_condition')->nullable();
            $table->text('accessories_received')->nullable();
            $table->text('reported_problem');
            $table->text('diagnosis_notes')->nullable();
            $table->text('technician_notes')->nullable();
            $table->string('status')->default('received');
            $table->string('priority')->default('normal');
            $table->decimal('estimated_cost', 12, 2)->default(0.00);
            $table->decimal('labour_charge', 12, 2)->default(0.00);
            $table->decimal('parts_total', 12, 2)->default(0.00);
            $table->decimal('additional_charges', 12, 2)->default(0.00);
            $table->decimal('discount', 12, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->decimal('amount_paid', 12, 2)->default(0.00);
            $table->date('expected_completion_date')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->integer('warranty_days')->default(0);
            $table->timestamps();

            $table->index(['tenant_id', 'job_number']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'customer_id']);
        });

        // 4. Create repair_job_items table
        Schema::create('repair_job_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_job_id')->constrained('repair_jobs')->cascadeOnDelete();
            $table->foreignId('spare_part_id')->nullable()->constrained('spare_parts')->nullOnDelete();
            $table->string('item_type')->default('part');
            $table->string('item_name');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_cost', 12, 2)->default(0.00);
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('total_price', 12, 2)->default(0.00);
            $table->boolean('is_stock_deducted')->default(false);
            $table->timestamps();
        });

        // 5. Create repair_job_activities table
        Schema::create('repair_job_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repair_job_id')->constrained('repair_jobs')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('activity_type');
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Create spare_part_stock_movements table
        Schema::create('spare_part_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('spare_part_id')->constrained('spare_parts')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('repair_job_id')->nullable()->constrained('repair_jobs')->nullOnDelete();
            $table->string('type');
            $table->integer('quantity_change');
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'created_at']);
        });

        // 7. Add invoice_type and repair_job_id to invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('invoice_type')->default('sale')->after('status')->index();
            $table->foreignId('repair_job_id')->nullable()->after('customer_id')->constrained('repair_jobs')->nullOnDelete();
        });

        // 8. Add repair_job_id to payments
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('repair_job_id')->nullable()->after('invoice_id')->constrained('repair_jobs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['repair_job_id']);
            $table->dropColumn('repair_job_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['repair_job_id']);
            $table->dropColumn(['invoice_type', 'repair_job_id']);
        });

        Schema::dropIfExists('spare_part_stock_movements');
        Schema::dropIfExists('repair_job_activities');
        Schema::dropIfExists('repair_job_items');
        Schema::dropIfExists('repair_jobs');
        Schema::dropIfExists('spare_parts');

        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['business_type', 'repair_job_prefix', 'repair_job_counter']);
        });
    }
};
