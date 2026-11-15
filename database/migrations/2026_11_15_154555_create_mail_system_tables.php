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
        Schema::create('email_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('provider_name')->default('custom'); // custom, gmail, google_workspace, hostinger, outlook
            $table->string('display_name')->nullable();
            $table->string('mail_mailer')->default('smtp');
            $table->string('mail_host');
            $table->unsignedInteger('mail_port')->default(587);
            $table->string('mail_username')->nullable();
            $table->text('mail_password')->nullable(); // Encrypted
            $table->string('mail_encryption')->default('tls'); // tls, ssl, none
            $table->string('from_address');
            $table->string('from_name');
            $table->string('reply_to_address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamp('last_tested_at')->nullable();
            $table->string('last_test_status', 30)->nullable(); // success, failed
            $table->text('last_test_error')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'is_default']);
        });

        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('name');
            $table->string('code')->index(); // invoice_generated, payment_received, etc.
            $table->string('category', 50)->default('general')->index(); // invoices, payments, reminders, customers, system, general
            $table->string('subject');
            $table->longText('body_html');
            $table->text('body_text')->nullable();
            $table->json('available_placeholders')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['tenant_id', 'category', 'is_active']);
        });

        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('email_templates')->nullOnDelete();
            $table->string('category', 50)->default('general')->index();
            $table->string('recipient_email')->index();
            $table->string('recipient_name')->nullable();
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->string('subject');
            $table->longText('body_html');
            $table->text('body_text')->nullable();
            $table->json('attachments')->nullable();
            $table->string('mailer_used')->nullable();
            $table->string('status', 30)->default('queued')->index(); // draft, queued, processing, sent, delivered, failed, cancelled
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamps();

            $table->index(['tenant_id', 'status', 'created_at']);
        });

        Schema::create('email_automations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('event_name')->index(); // invoice.finalized, payment.created, invoice.due_reminder
            $table->foreignId('template_id')->nullable()->constrained('email_templates')->nullOnDelete();
            $table->boolean('is_enabled')->default(false);
            $table->integer('timing_days')->default(0); // e.g. +3 days overdue
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'event_name', 'is_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_automations');
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('email_settings');
    }
};
