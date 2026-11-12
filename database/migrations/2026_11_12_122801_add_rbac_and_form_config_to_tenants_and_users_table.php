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
            $table->json('permissions')->nullable()->after('is_active');
            $table->json('form_fields')->nullable()->after('permissions');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['permissions', 'form_fields']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['permissions']);
        });
    }
};
