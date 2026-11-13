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
        Schema::create('brochure_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        Schema::create('brochure_modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('badge')->nullable();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('brochure_screenshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->nullable()->constrained('brochure_modules')->nullOnDelete();
            $table->string('title');
            $table->text('caption')->nullable();
            $table->string('image_path');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('brochure_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('price');
            $table->string('period')->default('per month');
            $table->string('billing_note')->nullable();
            $table->string('badge')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->text('description')->nullable();
            $table->json('features')->nullable();
            $table->text('limitations')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('brochure_generations', function (Blueprint $table) {
            $table->id();
            $table->string('version')->default('1.0.0');
            $table->string('file_path')->nullable();
            $table->integer('file_size')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brochure_generations');
        Schema::dropIfExists('brochure_plans');
        Schema::dropIfExists('brochure_screenshots');
        Schema::dropIfExists('brochure_modules');
        Schema::dropIfExists('brochure_settings');
    }
};
