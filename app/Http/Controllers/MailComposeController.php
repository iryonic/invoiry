<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Services\MailService;
use App\Services\TenantContext;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MailComposeController extends Controller
{
    /**
     * Show the mail composer interface.
     */
    public function create(Request $request, MailService $mailService): View
    {
        $tenant = TenantContext::get();
        $isSuperAdmin = auth()->user()?->isSuperAdmin() && ! session()->has('active_tenant_id');
        $tenantId = $isSuperAdmin ? null : $tenant?->id;

        $templates = EmailTemplate::active()
            ->forTenantOrGlobal($tenantId)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $customers = Customer::query()
            ->when($tenantId, fn ($q, $tid) => $q->where('tenant_id', $tid))
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        $invoices = Invoice::query()
            ->when($tenantId, fn ($q, $tid) => $q->where('tenant_id', $tid))
            ->latest()
            ->limit(30)
            ->get(['id', 'invoice_number', 'total_amount', 'balance_due', 'customer_id']);

        $preselectedTemplate = null;
        if ($request->filled('template_id')) {
            $preselectedTemplate = $templates->firstWhere('id', (int) $request->template_id);
        }

        $standardPlaceholders = EmailTemplate::standardPlaceholders();

        return view('mail.compose', compact(
            'templates',
            'customers',
            'invoices',
            'preselectedTemplate',
            'standardPlaceholders',
            'tenant'
        ));
    }

    /**
     * Handle email dispatch, queueing, or scheduling.
     */
    public function send(Request $request, MailService $mailService): RedirectResponse
    {
        $tenant = TenantContext::get();

        $validated = $request->validate([
            'recipient_email' => ['required', 'string', 'max:500'],
            'recipient_name' => ['nullable', 'string', 'max:150'],
            'cc' => ['nullable', 'string', 'max:500'],
            'bcc' => ['nullable', 'string', 'max:500'],
            'subject' => ['required', 'string', 'max:255'],
            'body_html' => ['required', 'string'],
            'body_text' => ['nullable', 'string'],
            'template_id' => ['nullable', 'exists:email_templates,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'invoice_id' => ['nullable', 'exists:invoices,id'],
            'category' => ['nullable', 'string', 'max:50'],
            'send_action' => ['required', 'in:send_now,schedule,draft'],
            'scheduled_at' => ['nullable', 'date'],
            'attachments.*' => ['nullable', 'file', 'max:10240', 'mimes:pdf,png,jpg,jpeg,webp,docx,xlsx,txt,csv'],
        ]);

        // Parse recipients (support multiple comma or semicolon separated addresses)
        $rawRecipients = preg_split('/[,;]+/', $validated['recipient_email']);
        $recipients = array_values(array_filter(array_map('trim', $rawRecipients), fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));

        if (empty($recipients)) {
            return back()->withInput()->with('error', 'Please provide at least one valid recipient email address.');
        }

        // Parse CC & BCC
        $ccList = [];
        if (! empty($validated['cc'])) {
            $rawCc = preg_split('/[,;]+/', $validated['cc']);
            $ccList = array_values(array_filter(array_map('trim', $rawCc), fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
        }

        $bccList = [];
        if (! empty($validated['bcc'])) {
            $rawBcc = preg_split('/[,;]+/', $validated['bcc']);
            $bccList = array_values(array_filter(array_map('trim', $rawBcc), fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
        }

        // Process file attachments securely
        $attachments = [];
        if ($request->hasFile('attachments')) {
            $batchFolder = 'email_attachments/'.Str::uuid();
            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $storedPath = $file->storeAs($batchFolder, $originalName, 'local');
                    $attachments[] = [
                        'name' => $originalName,
                        'path' => $storedPath,
                        'mime' => $file->getClientMimeType(),
                        'disk' => 'local',
                    ];
                }
            }
        }

        // Context models for placeholder replacement
        $customer = ! empty($validated['customer_id']) ? Customer::find($validated['customer_id']) : null;
        $invoice = ! empty($validated['invoice_id']) ? Invoice::find($validated['invoice_id']) : null;
        $placeholders = $mailService->buildPlaceholderData($tenant, $customer, $invoice);

        $renderedSubject = $mailService->renderPlaceholders($validated['subject'], $placeholders);
        $renderedHtml = $mailService->renderPlaceholders($validated['body_html'], $placeholders);
        $renderedText = ! empty($validated['body_text'])
            ? $mailService->renderPlaceholders($validated['body_text'], $placeholders)
            : strip_tags($renderedHtml);

        $status = match ($validated['send_action']) {
            'draft' => EmailLog::STATUS_DRAFT,
            default => EmailLog::STATUS_QUEUED,
        };

        $scheduledAt = null;
        if ($validated['send_action'] === 'schedule' && ! empty($validated['scheduled_at'])) {
            $scheduledAt = Carbon::parse($validated['scheduled_at']);
        }

        // Dispatch or record email for each recipient
        foreach ($recipients as $recipientEmail) {
            $mailService->queueEmail([
                'tenant_id' => $tenant?->id,
                'user_id' => auth()->id(),
                'customer_id' => $customer?->id,
                'invoice_id' => $invoice?->id,
                'template_id' => $validated['template_id'] ?? null,
                'category' => $validated['category'] ?? 'general',
                'recipient_email' => $recipientEmail,
                'recipient_name' => $validated['recipient_name'] ?? null,
                'cc' => $ccList,
                'bcc' => $bccList,
                'subject' => $renderedSubject,
                'body_html' => $renderedHtml,
                'body_text' => $renderedText,
                'attachments' => $attachments,
                'status' => $status,
                'scheduled_at' => $scheduledAt,
            ]);
        }

        $message = match ($validated['send_action']) {
            'draft' => 'Email saved as draft.',
            'schedule' => 'Email successfully scheduled for '.$scheduledAt?->format('d M Y, h:i A').'.',
            default => 'Email queued for delivery successfully.',
        };

        return redirect()->route('mail.logs.index')->with('success', $message);
    }

    /**
     * Dynamic AJAX preview with placeholder resolution.
     */
    public function preview(Request $request, MailService $mailService): JsonResponse
    {
        $tenant = TenantContext::get();
        $customer = $request->filled('customer_id') ? Customer::find($request->customer_id) : null;
        $invoice = $request->filled('invoice_id') ? Invoice::find($request->invoice_id) : null;

        $placeholders = $mailService->buildPlaceholderData($tenant, $customer, $invoice);

        $subject = $mailService->renderPlaceholders($request->get('subject', ''), $placeholders);
        $bodyHtml = $mailService->renderPlaceholders($request->get('body_html', ''), $placeholders);

        return response()->json([
            'subject' => $subject,
            'body_html' => $bodyHtml,
        ]);
    }
}
