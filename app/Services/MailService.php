<?php

namespace App\Services;

use App\Jobs\SendQueuedEmailJob;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\EmailLog;
use App\Models\EmailSetting;
use App\Models\Invoice;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MailService
{
    /**
     * Resolve the active EmailSetting for the tenant, falling back to platform global default.
     */
    public function getActiveSetting(?Tenant $tenant = null): ?EmailSetting
    {
        if ($tenant) {
            $tenantSetting = EmailSetting::where('tenant_id', $tenant->id)
                ->where('is_active', true)
                ->orderByDesc('is_default')
                ->latest()
                ->first();

            if ($tenantSetting) {
                return $tenantSetting;
            }
        }

        // Fallback to global platform SMTP configuration configured by Super Admin
        return EmailSetting::whereNull('tenant_id')
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->latest()
            ->first();
    }

    /**
     * Dynamically register and return a mailer name for the given setting or tenant.
     */
    public function configureDynamicMailer(?EmailSetting $setting = null, ?Tenant $tenant = null): string
    {
        $setting = $setting ?? $this->getActiveSetting($tenant);

        if (! $setting || ! $setting->mail_host) {
            return (string) config('mail.default', 'log');
        }

        $mailerName = 'invoiry_dynamic_'.($setting->id ?? 'global');

        Config::set("mail.mailers.{$mailerName}", [
            'transport' => 'smtp',
            'host' => $setting->mail_host,
            'port' => (int) $setting->mail_port,
            'encryption' => $setting->mail_encryption === 'none' ? null : $setting->mail_encryption,
            'username' => $setting->mail_username,
            'password' => $setting->mail_password,
            'timeout' => 15,
            'local_domain' => parse_url((string) config('app.url', 'http://localhost'), PHP_URL_HOST),
        ]);

        Config::set('mail.from.address', $setting->from_address);
        Config::set('mail.from.name', $setting->from_name);

        // Purge old cached instance in MailManager so updated credentials take effect immediately
        try {
            app('mail.manager')->purge($mailerName);
        } catch (Throwable) {
            // Ignored if mailer was not instantiated yet
        }

        return $mailerName;
    }

    /**
     * Test the raw TCP / SSL socket connection to verify SMTP availability and latency.
     *
     * @return array{success: bool, message: string, latency_ms: float|null}
     */
    public function testConnection(EmailSetting $setting): array
    {
        $host = trim($setting->mail_host);
        $port = (int) $setting->mail_port;
        $encryption = strtolower(trim((string) $setting->mail_encryption));
        $timeout = 8;

        $targetHost = $encryption === 'ssl' ? "ssl://{$host}" : $host;

        $startTime = microtime(true);
        $errno = 0;
        $errstr = '';

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $socket = @stream_socket_client(
            "{$targetHost}:{$port}",
            $errno,
            $errstr,
            $timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (! $socket) {
            $latency = round((microtime(true) - $startTime) * 1000, 2);
            $errorMsg = "Connection failed to {$host}:{$port} ({$errno}: {$errstr})";

            $setting->update([
                'last_tested_at' => now(),
                'last_test_status' => 'failed',
                'last_test_error' => $errorMsg,
            ]);

            return [
                'success' => false,
                'message' => $errorMsg,
                'latency_ms' => $latency,
            ];
        }

        // Read server welcome banner (expected 220)
        stream_set_timeout($socket, $timeout);
        $banner = (string) fgets($socket, 512);
        fclose($socket);

        $latency = round((microtime(true) - $startTime) * 1000, 2);

        if (! str_starts_with(trim($banner), '220')) {
            $errorMsg = "Unexpected SMTP response banner: {$banner}";
            $setting->update([
                'last_tested_at' => now(),
                'last_test_status' => 'failed',
                'last_test_error' => $errorMsg,
            ]);

            return [
                'success' => false,
                'message' => $errorMsg,
                'latency_ms' => $latency,
            ];
        }

        $successMsg = "Connected to {$host}:{$port} successfully in {$latency}ms. Server greeting: ".trim($banner);
        $setting->update([
            'last_tested_at' => now(),
            'last_test_status' => 'success',
            'last_test_error' => null,
        ]);

        return [
            'success' => true,
            'message' => $successMsg,
            'latency_ms' => $latency,
        ];
    }

    /**
     * Send an end-to-end test email using the specified setting.
     *
     * @return array{success: bool, message: string}
     */
    public function sendTestEmail(EmailSetting $setting, string $recipientEmail): array
    {
        try {
            $mailerName = $this->configureDynamicMailer($setting);

            $subject = 'Test Email from '.($setting->from_name ?: config('app.name', 'INVOIRY'));
            $html = '<div style="font-family: -apple-system, sans-serif; max-width: 500px; padding: 24px; border: 1px solid #E6E6E6; border-radius: 12px; background: #FFFFFF;">
                <div style="background: #050505; color: #B7FF00; padding: 12px 18px; border-radius: 8px; font-weight: 800; font-size: 14px; margin-bottom: 16px;">
                    '.e($setting->from_name ?: 'INVOIRY Cloud Counter').'
                </div>
                <h3 style="margin: 0 0 8px; color: #111111; font-size: 16px;">SMTP Test Succeeded!</h3>
                <p style="color: #666666; font-size: 13px; line-height: 20px;">
                    Your mail server connection via <strong>'.e($setting->mail_host).':'.e((string) $setting->mail_port).'</strong> is working properly.
                </p>
                <div style="background: #F7F7F6; padding: 10px 14px; border-radius: 6px; font-size: 11px; font-family: monospace; color: #444444; margin-top: 14px;">
                    Timestamp: '.now()->toRfc2822String().'<br>
                    Sender: '.e($setting->from_address).'<br>
                    Encryption: '.strtoupper(e($setting->mail_encryption)).'
                </div>
            </div>';

            Mail::mailer($mailerName)->send([], [], function (Message $message) use ($setting, $recipientEmail, $subject, $html) {
                $message->to($recipientEmail)
                    ->from($setting->from_address, $setting->from_name)
                    ->subject($subject)
                    ->html($html);

                if (! empty($setting->reply_to_address)) {
                    $message->replyTo($setting->reply_to_address);
                }
            });

            $setting->update([
                'last_tested_at' => now(),
                'last_test_status' => 'success',
                'last_test_error' => null,
            ]);

            return [
                'success' => true,
                'message' => "Test email successfully sent to {$recipientEmail}.",
            ];
        } catch (Throwable $e) {
            $errorMsg = 'Delivery failed: '.$e->getMessage();
            $setting->update([
                'last_tested_at' => now(),
                'last_test_status' => 'failed',
                'last_test_error' => $errorMsg,
            ]);

            Log::error('SMTP Test Email Error: '.$e->getMessage(), [
                'setting_id' => $setting->id,
                'recipient' => $recipientEmail,
            ]);

            return [
                'success' => false,
                'message' => $errorMsg,
            ];
        }
    }

    /**
     * Render string replacing template placeholders with supplied key-value pairs.
     *
     * @param  array<string, mixed>  $data
     */
    public function renderPlaceholders(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            $strVal = is_scalar($value) || $value === null ? (string) $value : json_encode($value);
            $content = str_replace(["{{{$key}}}", "{{ {$key} }}", "{{$key}}"], $strVal, $content);
        }

        return $content;
    }

    /**
     * Build standard dictionary of placeholders from models.
     *
     * @return array<string, string>
     */
    public function buildPlaceholderData(?Tenant $tenant, ?Customer $customer = null, ?Invoice $invoice = null, array $extra = []): array
    {
        $tenant = $tenant ?? TenantContext::get();

        $data = [
            'business_name' => $tenant?->name ?? config('app.name', 'INVOIRY'),
            'business_phone' => $tenant?->phone ?? '',
            'business_email' => $tenant?->email ?? '',
            'business_address' => trim(($tenant?->address ?? '').' '.($tenant?->city ?? '')),
            'business_upi' => $tenant?->upi_id ?? '',
            'customer_name' => $customer?->name ?? 'Valued Customer',
            'customer_email' => $customer?->email ?? '',
            'customer_phone' => $customer?->phone ?? '',
            'invoice_number' => $invoice?->invoice_number ?? '',
            'invoice_date' => $invoice?->invoice_date?->format('d M Y') ?? now()->format('d M Y'),
            'invoice_due_date' => $invoice?->due_date?->format('d M Y') ?? 'Due upon receipt',
            'invoice_total' => '₹'.number_format($invoice?->total_amount ?? 0, 2),
            'invoice_paid' => '₹'.number_format($invoice?->paid_amount ?? 0, 2),
            'invoice_balance_due' => '₹'.number_format($invoice?->balance_due ?? 0, 2),
            'payment_amount' => $extra['payment_amount'] ?? '₹0.00',
            'payment_date' => $extra['payment_date'] ?? now()->format('d M Y, h:i A'),
            'payment_method' => $extra['payment_method'] ?? 'Cash / Counter',
            'login_url' => url('/login'),
            'current_year' => date('Y'),
        ];

        return array_merge($data, $extra);
    }

    /**
     * Queue or schedule an email.
     *
     * @param  array<string, mixed>  $payload
     */
    public function queueEmail(array $payload): EmailLog
    {
        $scheduledAt = ! empty($payload['scheduled_at']) ? Carbon::parse($payload['scheduled_at']) : null;
        $isFuture = $scheduledAt && $scheduledAt->isFuture();

        $log = EmailLog::create([
            'tenant_id' => $payload['tenant_id'] ?? TenantContext::get()?->id,
            'user_id' => $payload['user_id'] ?? auth()->id(),
            'customer_id' => $payload['customer_id'] ?? null,
            'invoice_id' => $payload['invoice_id'] ?? null,
            'template_id' => $payload['template_id'] ?? null,
            'category' => $payload['category'] ?? 'general',
            'recipient_email' => trim($payload['recipient_email']),
            'recipient_name' => $payload['recipient_name'] ?? null,
            'cc' => ! empty($payload['cc']) ? (array) $payload['cc'] : null,
            'bcc' => ! empty($payload['bcc']) ? (array) $payload['bcc'] : null,
            'subject' => $payload['subject'],
            'body_html' => $payload['body_html'],
            'body_text' => $payload['body_text'] ?? strip_tags($payload['body_html']),
            'attachments' => ! empty($payload['attachments']) ? (array) $payload['attachments'] : null,
            'mailer_used' => $payload['mailer_used'] ?? null,
            'status' => $payload['status'] ?? ($isFuture ? EmailLog::STATUS_QUEUED : EmailLog::STATUS_QUEUED),
            'scheduled_at' => $scheduledAt,
        ]);

        if (! $isFuture && $log->status !== EmailLog::STATUS_DRAFT) {
            // Dispatch asynchronously via Laravel Queue
            SendQueuedEmailJob::dispatch($log->id);
        }

        return $log;
    }

    /**
     * Execute the physical email dispatch for a given log record.
     */
    public function sendNow(EmailLog $log): bool
    {
        if ($log->status === EmailLog::STATUS_CANCELLED) {
            return false;
        }

        $log->update([
            'status' => EmailLog::STATUS_PROCESSING,
            'attempts' => $log->attempts + 1,
        ]);

        try {
            $tenant = $log->tenant;
            $setting = $this->getActiveSetting($tenant);
            $mailerName = $this->configureDynamicMailer($setting, $tenant);

            $fromAddress = $setting?->from_address ?: config('mail.from.address', 'noreply@invoiry.com');
            $fromName = $setting?->from_name ?: config('mail.from.name', 'INVOIRY');

            Mail::mailer($mailerName)->send([], [], function (Message $message) use ($log, $fromAddress, $fromName, $setting) {
                $message->to($log->recipient_email, $log->recipient_name)
                    ->from($fromAddress, $fromName)
                    ->subject($log->subject)
                    ->html($log->body_html);

                if (! empty($log->body_text)) {
                    $message->text($log->body_text);
                }

                if ($setting?->reply_to_address) {
                    $message->replyTo($setting->reply_to_address);
                }

                // CC
                if (! empty($log->cc) && is_array($log->cc)) {
                    foreach ($log->cc as $ccEmail) {
                        if (filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                            $message->cc(trim($ccEmail));
                        }
                    }
                }

                // BCC
                if (! empty($log->bcc) && is_array($log->bcc)) {
                    foreach ($log->bcc as $bccEmail) {
                        if (filter_var($bccEmail, FILTER_VALIDATE_EMAIL)) {
                            $message->bcc(trim($bccEmail));
                        }
                    }
                }

                // Attachments
                if (! empty($log->attachments) && is_array($log->attachments)) {
                    foreach ($log->attachments as $att) {
                        if (is_array($att) && ! empty($att['path']) && Storage::disk($att['disk'] ?? 'local')->exists($att['path'])) {
                            $fullPath = Storage::disk($att['disk'] ?? 'local')->path($att['path']);
                            $message->attach($fullPath, [
                                'as' => $att['name'] ?? basename($fullPath),
                                'mime' => $att['mime'] ?? 'application/octet-stream',
                            ]);
                        } elseif (is_string($att) && file_exists($att)) {
                            $message->attach($att);
                        }
                    }
                }
            });

            $log->update([
                'status' => EmailLog::STATUS_SENT,
                'sent_at' => now(),
                'mailer_used' => $setting?->display_name ?: $mailerName,
                'error_message' => null,
            ]);

            AuditLog::create([
                'user_id' => $log->user_id,
                'tenant_id' => $log->tenant_id,
                'event' => 'mail.sent',
                'description' => "Email sent to {$log->recipient_email} ('{$log->subject}')",
                'ip_address' => request()->ip() ?? '127.0.0.1',
            ]);

            return true;
        } catch (Throwable $e) {
            $log->update([
                'status' => EmailLog::STATUS_FAILED,
                'error_message' => $e->getMessage(),
            ]);

            Log::error("Email delivery failed for Log ID {$log->id}: ".$e->getMessage(), [
                'recipient' => $log->recipient_email,
                'subject' => $log->subject,
            ]);

            throw $e;
        }
    }

    /**
     * Generate an invoice PDF file on local disk and return attachment array.
     *
     * @return array{path: string, name: string, mime: string, disk: string}
     */
    public function generateInvoiceAttachment(Invoice $invoice): array
    {
        $tenant = $invoice->tenant ?? TenantContext::get();
        $pdf = Pdf::loadView('invoices.print', compact('invoice', 'tenant'));
        $pdf->setPaper('a4', 'portrait');

        $fileName = 'Invoice_'.preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $invoice->invoice_number).'.pdf';
        $storagePath = "email_attachments/invoices/{$invoice->id}/{$fileName}";

        Storage::disk('local')->put($storagePath, $pdf->output());

        return [
            'path' => $storagePath,
            'name' => $fileName,
            'mime' => 'application/pdf',
            'disk' => 'local',
        ];
    }
}
