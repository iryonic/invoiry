<?php

namespace App\Console\Commands;

use App\Jobs\SendQueuedEmailJob;
use App\Models\EmailAutomation;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Services\MailService;
use Illuminate\Console\Command;

class ProcessScheduledEmailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:process-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process due scheduled emails and automated invoice reminders';

    /**
     * Execute the console command.
     */
    public function handle(MailService $mailService): int
    {
        $this->info('Checking for due scheduled emails...');

        // 1. Process explicit scheduled emails
        $dueEmails = EmailLog::where('status', EmailLog::STATUS_QUEUED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        $processedCount = 0;
        foreach ($dueEmails as $emailLog) {
            SendQueuedEmailJob::dispatch($emailLog->id);
            $processedCount++;
        }

        $this->info("Dispatched {$processedCount} scheduled email(s).");

        // 2. Process automated payment reminders if automations are enabled
        $automations = EmailAutomation::enabled()
            ->where('event_name', EmailAutomation::EVENT_INVOICE_DUE_REMINDER)
            ->get();

        $automatedCount = 0;
        foreach ($automations as $automation) {
            $days = max(0, (int) $automation->timing_days);
            $cutoffDate = now()->subDays($days)->toDateString();

            $overdueInvoices = Invoice::where('status', 'finalized')
                ->where('balance_due', '>', 0)
                ->whereNotNull('due_date')
                ->where('due_date', '<=', $cutoffDate)
                ->when($automation->tenant_id, fn ($q, $tid) => $q->where('tenant_id', $tid))
                ->with(['customer', 'tenant'])
                ->get();

            $template = $automation->template ?? EmailTemplate::where('code', 'payment_reminder')->first();

            if (! $template) {
                continue;
            }

            foreach ($overdueInvoices as $invoice) {
                if (empty($invoice->customer?->email)) {
                    continue;
                }

                // Prevent duplicate reminders within 7 days
                $recentLog = EmailLog::where('invoice_id', $invoice->id)
                    ->where('category', 'reminders')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->exists();

                if ($recentLog) {
                    continue;
                }

                $placeholders = $mailService->buildPlaceholderData($invoice->tenant, $invoice->customer, $invoice);
                $renderedSubject = $mailService->renderPlaceholders($template->subject, $placeholders);
                $renderedBody = $mailService->renderPlaceholders($template->body_html, $placeholders);

                $mailService->queueEmail([
                    'tenant_id' => $invoice->tenant_id,
                    'user_id' => null,
                    'customer_id' => $invoice->customer_id,
                    'invoice_id' => $invoice->id,
                    'template_id' => $template->id,
                    'category' => 'reminders',
                    'recipient_email' => $invoice->customer->email,
                    'recipient_name' => $invoice->customer->name,
                    'subject' => $renderedSubject,
                    'body_html' => $renderedBody,
                ]);

                $automatedCount++;
            }
        }

        $this->info("Generated {$automatedCount} automated overdue payment reminder(s).");

        return self::SUCCESS;
    }
}
