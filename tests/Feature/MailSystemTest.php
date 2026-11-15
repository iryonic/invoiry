<?php

namespace Tests\Feature;

use App\Console\Commands\ProcessScheduledEmailsCommand;
use App\Jobs\SendQueuedEmailJob;
use App\Models\Customer;
use App\Models\EmailAutomation;
use App\Models\EmailLog;
use App\Models\EmailSetting;
use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MailSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $tenantAdmin;

    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->tenant = Tenant::first() ?? Tenant::factory()->create();
        $this->tenantAdmin = User::where('role', 'tenant_admin')->where('tenant_id', $this->tenant->id)->first()
            ?? User::factory()->create(['role' => 'tenant_admin', 'tenant_id' => $this->tenant->id]);
    }

    public function test_mail_dashboard_loads_for_authorized_admin(): void
    {
        $this->actingAs($this->tenantAdmin);

        $response = $this->get(route('mail.dashboard'));
        $response->assertOk();
        $response->assertSee('Total Sent');
        $response->assertSee('SMTP Config');

        $composeResponse = $this->get(route('mail.compose'));
        $composeResponse->assertOk();
        $composeResponse->assertSee('Compose Email');

        $composeResponse = $this->get(route('mail.compose'));
        $composeResponse->assertOk();
        $composeResponse->assertSee('Compose Email');

        $settingsResponse = $this->get(route('mail.settings.index'));
        $settingsResponse->assertOk();
        $settingsResponse->assertSee('Mail Server');
        $settingsResponse->assertSee('SMTP Configuration');

        $templatesResponse = $this->get(route('mail.templates.index'));
        $templatesResponse->assertOk();

        $logsResponse = $this->get(route('mail.logs.index'));
        $logsResponse->assertOk();

        $scheduledResponse = $this->get(route('mail.scheduled.index'));
        $scheduledResponse->assertOk();

        $automationsResponse = $this->get(route('mail.automations.index'));
        $automationsResponse->assertOk();

        $templatesResponse = $this->get(route('mail.templates.index'));
        $templatesResponse->assertOk();

        $logsResponse = $this->get(route('mail.logs.index'));
        $logsResponse->assertOk();

        $scheduledResponse = $this->get(route('mail.scheduled.index'));
        $scheduledResponse->assertOk();

        $automationsResponse = $this->get(route('mail.automations.index'));
        $automationsResponse->assertOk();
    }

    public function test_smtp_setting_creation_encrypts_password_and_masks_display(): void
    {
        $this->actingAs($this->tenantAdmin);

        $response = $this->post(route('mail.settings.store'), [
            'provider_name' => 'hostinger',
            'display_name' => 'Production Hostinger Relay',
            'mail_host' => 'smtp.hostinger.com',
            'mail_port' => 465,
            'mail_username' => 'billing@business.com',
            'mail_password' => 'super_secret_smtp_pass_123',
            'mail_encryption' => 'ssl',
            'from_address' => 'billing@business.com',
            'from_name' => 'Apex Business',
            'is_active' => 1,
            'is_default' => 1,
        ]);

        $response->assertRedirect();

        $setting = EmailSetting::where('display_name', 'Production Hostinger Relay')->first();
        $this->assertNotNull($setting);
        $this->assertEquals('super_secret_smtp_pass_123', $setting->mail_password);
        $this->assertStringContainsString('••••', $setting->getMaskedPassword());
        $this->assertTrue($setting->is_default);

        // Verify password is never serialized in toArray
        $array = $setting->toArray();
        $this->assertArrayNotHasKey('mail_password', $array);
    }

    public function test_template_crud_and_placeholder_rendering(): void
    {
        $this->actingAs($this->tenantAdmin);

        // 1. Create Template
        $response = $this->post(route('mail.templates.store'), [
            'name' => 'VIP Welcome Notice',
            'code' => 'vip_welcome_notice',
            'category' => 'customers',
            'subject' => 'Welcome to {{business_name}}, {{customer_name}}!',
            'body_html' => '<p>Hello {{customer_name}}, thank you for shopping at {{business_name}}.</p>',
            'body_text' => 'Hello {{customer_name}}, thank you for shopping at {{business_name}}.',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('mail.templates.index'));

        $template = EmailTemplate::where('code', 'vip_welcome_notice')->first();
        $this->assertNotNull($template);
        $this->assertEquals('VIP Welcome Notice', $template->name);

        // 2. Duplicate Template
        $dupResponse = $this->post(route('mail.templates.duplicate', $template));
        $dupResponse->assertRedirect(route('mail.templates.index'));

        $this->assertDatabaseHas('email_templates', [
            'name' => 'VIP Welcome Notice (Copy)',
        ]);

        // 3. Test Preview endpoint
        $previewResponse = $this->get(route('mail.templates.preview', $template));
        $previewResponse->assertOk();
        $previewData = $previewResponse->json();
        $this->assertStringContainsString('Welcome to', $previewData['subject']);
        $this->assertStringContainsString('John Doe', $previewData['subject']);
        $this->assertStringContainsString('John Doe', $previewData['body_html']);
    }

    public function test_compose_and_dispatch_immediate_email_queues_job(): void
    {
        Queue::fake();
        $this->actingAs($this->tenantAdmin);

        $response = $this->post(route('mail.send'), [
            'recipient_email' => 'client@customer.com',
            'recipient_name' => 'Client Customer',
            'subject' => 'Monthly Account Statement',
            'body_html' => '<h3>Statement</h3><p>Here is your account statement.</p>',
            'category' => 'general',
            'send_action' => 'send_now',
        ]);

        $response->assertRedirect(route('mail.logs.index'));

        $log = EmailLog::where('recipient_email', 'client@customer.com')->first();
        $this->assertNotNull($log);
        $this->assertEquals(EmailLog::STATUS_QUEUED, $log->status);

        Queue::assertPushed(SendQueuedEmailJob::class, function ($job) use ($log) {
            return $job->emailLogId === $log->id;
        });
    }

    public function test_scheduled_email_is_picked_up_by_scheduled_command(): void
    {
        Queue::fake();

        // Create a past-due scheduled email log
        $scheduledLog = EmailLog::factory()->create([
            'tenant_id' => $this->tenant->id,
            'status' => EmailLog::STATUS_QUEUED,
            'scheduled_at' => now()->subMinutes(5),
            'recipient_email' => 'scheduled@target.com',
            'subject' => 'Timely reminder',
        ]);

        $this->artisan(ProcessScheduledEmailsCommand::class)
            ->expectsOutputToContain('Dispatched 1 scheduled email(s).')
            ->assertExitCode(0);

        Queue::assertPushed(SendQueuedEmailJob::class, function ($job) use ($scheduledLog) {
            return $job->emailLogId === $scheduledLog->id;
        });
    }

    public function test_invoice_email_preview_and_sending_with_pdf(): void
    {
        Queue::fake();
        $this->actingAs($this->tenantAdmin);

        $customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Alice Walker',
            'phone' => '9876543210',
            'email' => 'alice@walker.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $customer->id,
            'user_id' => $this->tenantAdmin->id,
            'invoice_number' => 'INV-TEST-001',
            'invoice_date' => now()->toDateString(),
            'payment_method' => 'cash',
            'subtotal' => 12500,
            'discount' => 0,
            'tax_amount' => 0,
            'grand_total' => 12500,
            'amount_paid' => 12500,
            'payment_status' => 'paid',
            'status' => 'finalized',
        ]);

        // Test preview endpoint
        $previewResponse = $this->get(route('invoices.mail.preview', $invoice));
        $previewResponse->assertOk();
        $this->assertEquals('alice@walker.com', $previewResponse->json('recipient_email'));

        // Test send invoice email
        $sendResponse = $this->post(route('invoices.mail.send', $invoice), [
            'recipient_email' => 'alice@walker.com',
            'recipient_name' => 'Alice Walker',
            'subject' => 'Official Tax Invoice #INV-TEST-001',
            'body_html' => '<p>Please find your tax invoice attached.</p>',
            'attach_pdf' => 1,
        ]);

        $sendResponse->assertRedirect();

        $log = EmailLog::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($log);
        $this->assertEquals('alice@walker.com', $log->recipient_email);
        $this->assertNotEmpty($log->attachments);
        $this->assertStringContainsString('Invoice_INV-TEST-001.pdf', $log->attachments[0]['name']);

        Queue::assertPushed(SendQueuedEmailJob::class);
    }

    public function test_payment_recording_auto_triggers_receipt_when_automation_enabled(): void
    {
        Queue::fake();
        $this->actingAs($this->tenantAdmin);

        // Enable payment.created automation
        EmailAutomation::updateOrCreate(
            ['tenant_id' => $this->tenant->id, 'event_name' => EmailAutomation::EVENT_PAYMENT_CREATED],
            ['is_enabled' => true]
        );

        $customer = Customer::factory()->create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Paying Customer',
            'phone' => '9123456789',
            'email' => 'paying_customer@test.com',
        ]);

        $invoice = Invoice::create([
            'tenant_id' => $this->tenant->id,
            'customer_id' => $customer->id,
            'user_id' => $this->tenantAdmin->id,
            'invoice_number' => 'INV-TEST-002',
            'invoice_date' => now()->toDateString(),
            'payment_method' => 'cash',
            'subtotal' => 5000,
            'discount' => 0,
            'tax_amount' => 0,
            'grand_total' => 5000,
            'amount_paid' => 0,
            'payment_status' => 'unpaid',
            'status' => 'finalized',
        ]);

        // Record payment
        $response = $this->post(route('invoices.payments.store', $invoice), [
            'amount' => 2500,
            'payment_method' => 'upi',
            'payment_date' => now()->toDateString(),
            'reference_id' => 'UPI99887766',
        ]);

        $response->assertRedirect();

        $log = EmailLog::where('invoice_id', $invoice->id)
            ->where('category', 'payments')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals('paying_customer@test.com', $log->recipient_email);
        $this->assertStringContainsString('Payment Receipt', $log->subject);

        Queue::assertPushed(SendQueuedEmailJob::class);
    }
}
