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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->default('Jammu & Kashmir');
            $table->string('state_code')->default('01');
            $table->string('gstin')->nullable();
            $table->string('upi_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->string('bank_ifsc')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('logo')->nullable();
            $table->string('signature')->nullable();
            $table->string('invoice_prefix')->default('INV-');
            $table->unsignedBigInteger('invoice_counter')->default(1);
            $table->text('terms_conditions')->nullable();
            $table->text('declaration')->nullable();
            $table->string('plan')->default('trial');
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
