<?php

namespace App\Http\Controllers;

use App\Jobs\SendQueuedEmailJob;
use App\Models\AuditLog;
use App\Models\EmailLog;
use App\Services\TenantContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MailScheduledController extends Controller
{
    /**
     * Display a listing of upcoming scheduled emails.
     */
    public function index(Request $request): View
    {
        $tenant = TenantContext::get();
        $isSuperAdmin = auth()->user()?->isSuperAdmin() && ! session()->has('active_tenant_id');
        $tenantId = $isSuperAdmin ? null : $tenant?->id;

        $scheduledEmails = EmailLog::query()
            ->when($tenantId, fn ($q, $tid) => $q->where('tenant_id', $tid))
            ->where('status', EmailLog::STATUS_QUEUED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>', now())
            ->with(['customer', 'invoice', 'user'])
            ->orderBy('scheduled_at')
            ->paginate(15);

        return view('mail.scheduled.index', compact('scheduledEmails', 'tenant', 'isSuperAdmin'));
    }

    /**
     * Cancel an upcoming scheduled email.
     */
    public function cancel(EmailLog $log): RedirectResponse
    {
        $tenant = TenantContext::get();
        if ($tenant && $log->tenant_id && $log->tenant_id !== $tenant->id) {
            abort(403);
        }

        $log->update(['status' => EmailLog::STATUS_CANCELLED]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'tenant_id' => $log->tenant_id,
            'event' => 'mail.scheduled.cancelled',
            'description' => "Cancelled scheduled email #{$log->id} to {$log->recipient_email}",
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);

        return back()->with('success', 'Scheduled email was cancelled.');
    }

    /**
     * Dispatch a scheduled email immediately without waiting.
     */
    public function dispatchNow(EmailLog $log): RedirectResponse
    {
        $tenant = TenantContext::get();
        if ($tenant && $log->tenant_id && $log->tenant_id !== $tenant->id) {
            abort(403);
        }

        $log->update(['scheduled_at' => now()]);
        SendQueuedEmailJob::dispatch($log->id);

        return back()->with('success', 'Email queued for immediate delivery.');
    }
}
