<?php

namespace App\Http\Controllers;

use App\Jobs\SendQueuedEmailJob;
use App\Models\AuditLog;
use App\Models\EmailLog;
use App\Services\TenantContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MailLogController extends Controller
{
    /**
     * Display a listing of email delivery logs with filters.
     */
    public function index(Request $request): View
    {
        $tenant = TenantContext::get();
        $isSuperAdmin = auth()->user()?->isSuperAdmin() && ! session()->has('active_tenant_id');
        $tenantId = $isSuperAdmin ? null : $tenant?->id;

        $query = EmailLog::query()->when($tenantId, fn ($q, $tid) => $q->where('tenant_id', $tid))
            ->with(['customer', 'invoice', 'user', 'template']);

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('recipient_email', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Category
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Related Invoice
        if ($request->filled('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        $logs = $query->latest()->paginate(15)->withQueryString();

        // Quick counts
        $baseCounts = EmailLog::query()->when($tenantId, fn ($q, $tid) => $q->where('tenant_id', $tid));
        $counts = [
            'all' => (clone $baseCounts)->count(),
            'sent' => (clone $baseCounts)->whereIn('status', [EmailLog::STATUS_SENT, EmailLog::STATUS_DELIVERED])->count(),
            'queued' => (clone $baseCounts)->whereIn('status', [EmailLog::STATUS_QUEUED, EmailLog::STATUS_PROCESSING])->count(),
            'failed' => (clone $baseCounts)->where('status', EmailLog::STATUS_FAILED)->count(),
        ];

        return view('mail.logs.index', compact('logs', 'counts', 'tenant', 'isSuperAdmin'));
    }

    /**
     * Show log details (modal/json endpoint).
     */
    public function show(EmailLog $log): JsonResponse
    {
        $tenant = TenantContext::get();
        if ($tenant && $log->tenant_id && $log->tenant_id !== $tenant->id) {
            abort(403);
        }

        return response()->json([
            'id' => $log->id,
            'recipient_email' => $log->recipient_email,
            'recipient_name' => $log->recipient_name,
            'cc' => $log->cc,
            'bcc' => $log->bcc,
            'subject' => $log->subject,
            'body_html' => $log->body_html,
            'body_text' => $log->body_text,
            'category' => $log->category,
            'status' => $log->status,
            'status_badge' => $log->getStatusBadgeClass(),
            'mailer_used' => $log->mailer_used,
            'attempts' => $log->attempts,
            'error_message' => $log->error_message,
            'scheduled_at' => $log->scheduled_at?->format('d M Y, h:i A'),
            'sent_at' => $log->sent_at?->format('d M Y, h:i A'),
            'created_at' => $log->created_at->format('d M Y, h:i A'),
            'attachments' => $log->attachments,
            'invoice_number' => $log->invoice?->invoice_number,
            'customer_name' => $log->customer?->name,
        ]);
    }

    /**
     * Retry sending a failed email.
     */
    public function retry(EmailLog $log): RedirectResponse
    {
        $tenant = TenantContext::get();
        if ($tenant && $log->tenant_id && $log->tenant_id !== $tenant->id) {
            abort(403);
        }

        $log->update([
            'status' => EmailLog::STATUS_QUEUED,
            'error_message' => null,
        ]);

        SendQueuedEmailJob::dispatch($log->id);

        AuditLog::create([
            'user_id' => auth()->id(),
            'tenant_id' => $log->tenant_id,
            'event' => 'mail.retried',
            'description' => "Retried delivery for Log #{$log->id} to {$log->recipient_email}",
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);

        return back()->with('success', "Email #{$log->id} re-queued for delivery.");
    }

    /**
     * Delete an email log record.
     */
    public function destroy(EmailLog $log): RedirectResponse
    {
        $tenant = TenantContext::get();
        if ($tenant && $log->tenant_id && $log->tenant_id !== $tenant->id) {
            abort(403);
        }

        $log->delete();

        return back()->with('success', 'Email record deleted successfully.');
    }
}
