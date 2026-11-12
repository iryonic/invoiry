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
            $table->string('invoice_title')->nullable()->after('invoice_prefix');
            $table->string('invoice_color')->nullable()->default('#111111')->after('invoice_title');
            $table->text('invoice_footer_note')->nullable()->after('declaration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['invoice_title', 'invoice_color', 'invoice_footer_note']);
        });
    }
};
