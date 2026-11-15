<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\MailService;
use App\Services\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InvoiceMailController extends Controller
{
    /**
     * Preview invoice email with generated PDF attachment details.
     */
    public function preview(Invoice $invoice, MailService $mailService): JsonResponse
    {
        $tenant = $invoice->tenant ?? TenantContext::get();
        $customer = $invoice->customer;

        $template = EmailTemplate::active()
            ->forTenantOrGlobal($tenant?->id)
            ->where('code', 'invoice_generated')
            ->first();

        if (! $template) {
            $template = EmailTemplate::where('code', 'invoice_generated')->first();
        }

        $placeholders = $mailService->buildPlaceholderData($tenant, $customer, $invoice);

        $subject = $template ? $mailService->renderPlaceholders($template->subject, $placeholders) : "Tax Invoice #{$invoice->invoice_number}";
        $bodyHtml = $template ? $mailService->renderPlaceholders($template->body_html, $placeholders) : "<p>Invoice #{$invoice->invoice_number} is attached.</p>";

        return response()->json([
            'recipient_email' => $customer?->email ?? '',
            'recipient_name' => $customer?->name ?? '',
            'subject' => $subject,
            'body_html' => $bodyHtml,
            'template_id' => $template?->id,
            'pdf_attachment' => "Invoice_{$invoice->invoice_number}.pdf",
        ]);
    }

    /**
     * Send invoice PDF directly to customer.
     */
    public function send(Request $request, Invoice $invoice, MailService $mailService): RedirectResponse
    {
        $tenant = $invoice->tenant ?? TenantContext::get();

        $validated = $request->validate([
            'recipient_email' => ['required', 'email'],
            'recipient_name' => ['nullable', 'string', 'max:150'],
            'subject' => ['required', 'string', 'max:255'],
            'body_html' => ['required', 'string'],
            'template_id' => ['nullable', 'exists:email_templates,id'],
            'attach_pdf' => ['nullable', 'boolean'],
        ]);

        $attachments = [];
        if ($request->boolean('attach_pdf', true)) {
            $attachments[] = $mailService->generateInvoiceAttachment($invoice);
        }

        $log = $mailService->queueEmail([
            'tenant_id' => $tenant?->id,
            'user_id' => auth()->id(),
            'customer_id' => $invoice->customer_id,
            'invoice_id' => $invoice->id,
            'template_id' => $validated['template_id'] ?? null,
            'category' => 'invoices',
            'recipient_email' => trim($validated['recipient_email']),
            'recipient_name' => $validated['recipient_name'] ?? $invoice->customer?->name,
            'subject' => $validated['subject'],
            'body_html' => $validated['body_html'],
            'attachments' => $attachments,
            'status' => EmailLog::STATUS_QUEUED,
        ]);

        return back()->with('success', "Invoice #{$invoice->invoice_number} has been queued for delivery to {$log->recipient_email}.");
    }

    /**
     * Send payment confirmation receipt email.
     */
    public function sendReceipt(Request $request, Invoice $invoice, Payment $payment, MailService $mailService): RedirectResponse
    {
        $tenant = $invoice->tenant ?? TenantContext::get();
        $customer = $invoice->customer;

        $recipientEmail = $request->input('recipient_email', $customer?->email);

        if (! filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            return back()->with('error', 'Customer does not have a valid email address configured.');
        }

        $template = EmailTemplate::active()
            ->forTenantOrGlobal($tenant?->id)
            ->where('code', 'payment_received')
            ->first() ?? EmailTemplate::where('code', 'payment_received')->first();

        $extra = [
            'payment_amount' => '₹'.number_format($payment->amount, 2),
            'payment_date' => $payment->payment_date?->format('d M Y, h:i A') ?? now()->format('d M Y, h:i A'),
            'payment_method' => ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'Counter')),
        ];

        $placeholders = $mailService->buildPlaceholderData($tenant, $customer, $invoice, $extra);

        $subject = $template ? $mailService->renderPlaceholders($template->subject, $placeholders) : "Payment Confirmation for Invoice #{$invoice->invoice_number}";
        $bodyHtml = $template ? $mailService->renderPlaceholders($template->body_html, $placeholders) : "<p>Payment of {$extra['payment_amount']} recorded.</p>";

        $mailService->queueEmail([
            'tenant_id' => $tenant?->id,
            'user_id' => auth()->id(),
            'customer_id' => $customer?->id,
            'invoice_id' => $invoice->id,
            'template_id' => $template?->id,
            'category' => 'payments',
            'recipient_email' => $recipientEmail,
            'recipient_name' => $customer?->name,
            'subject' => $subject,
            'body_html' => $bodyHtml,
            'status' => EmailLog::STATUS_QUEUED,
        ]);

        return back()->with('success', "Payment receipt queued for delivery to {$recipientEmail}.");
    }

    /**
     * Send payment reminder for outstanding balance.
     */
    public function sendReminder(Request $request, Invoice $invoice, MailService $mailService): RedirectResponse
    {
        $tenant = $invoice->tenant ?? TenantContext::get();
        $customer = $invoice->customer;

        $recipientEmail = $request->input('recipient_email', $customer?->email);

        if (! filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            return back()->with('error', 'Customer does not have a valid email address.');
        }

        $template = EmailTemplate::active()
            ->forTenantOrGlobal($tenant?->id)
            ->where('code', 'payment_reminder')
            ->first() ?? EmailTemplate::where('code', 'payment_reminder')->first();

        $placeholders = $mailService->buildPlaceholderData($tenant, $customer, $invoice);

        $subject = $template ? $mailService->renderPlaceholders($template->subject, $placeholders) : "Payment Reminder: Invoice #{$invoice->invoice_number}";
        $bodyHtml = $template ? $mailService->renderPlaceholders($template->body_html, $placeholders) : "<p>Reminder for Invoice #{$invoice->invoice_number}.</p>";

        $mailService->queueEmail([
            'tenant_id' => $tenant?->id,
            'user_id' => auth()->id(),
            'customer_id' => $customer?->id,
            'invoice_id' => $invoice->id,
            'template_id' => $template?->id,
            'category' => 'reminders',
            'recipient_email' => $recipientEmail,
            'recipient_name' => $customer?->name,
            'subject' => $subject,
            'body_html' => $bodyHtml,
            'status' => EmailLog::STATUS_QUEUED,
        ]);

        return back()->with('success', "Payment reminder queued for delivery to {$recipientEmail}.");
    }
}
