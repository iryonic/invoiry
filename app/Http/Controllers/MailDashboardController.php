<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use App\Services\MailService;
use App\Services\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MailDashboardController extends Controller
{
    /**
     * Display the email management dashboard with delivery metrics & service health.
     */
    public function index(Request $request, MailService $mailService): View
    {
        $tenant = TenantContext::get();
        $isSuperAdmin = auth()->user()?->isSuperAdmin() && ! session()->has('active_tenant_id');

        $tenantId = $isSuperAdmin ? null : $tenant?->id;

        $baseQuery = EmailLog::query()->when($tenantId, fn ($q, $tid) => $q->where('tenant_id', $tid));

        $totalSent = (clone $baseQuery)->whereIn('status', [EmailLog::STATUS_SENT, EmailLog::STATUS_DELIVERED])->count();
        $deliveredCount = (clone $baseQuery)->where('status', EmailLog::STATUS_DELIVERED)->count();
        $failedCount = (clone $baseQuery)->where('status', EmailLog::STATUS_FAILED)->count();
        $queuedCount = (clone $baseQuery)->whereIn('status', [EmailLog::STATUS_QUEUED, EmailLog::STATUS_PROCESSING])->count();
        $scheduledCount = (clone $baseQuery)->where('status', EmailLog::STATUS_QUEUED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>', now())
            ->count();
        $sentToday = (clone $baseQuery)->whereIn('status', [EmailLog::STATUS_SENT, EmailLog::STATUS_DELIVERED])
            ->whereDate('sent_at', today())
            ->count();

        // Safe rate calculations
        $totalAttempted = $totalSent + $failedCount;
        $successRate = $totalAttempted > 0 ? round(($totalSent / $totalAttempted) * 100, 1) : 100.0;

        // Active SMTP Provider
        $activeSetting = $mailService->getActiveSetting($tenant);

        // Queue Health
        $jobsInQueue = 0;
        $failedQueueJobs = 0;
        try {
            $jobsInQueue = DB::table('jobs')->count();
            $failedQueueJobs = DB::table('failed_jobs')->count();
        } catch (\Throwable) {
            // Queue table queries fallback
        }

        // Recent 10 email activities
        $recentEmails = (clone $baseQuery)->with(['customer', 'invoice', 'user'])
            ->latest()
            ->limit(10)
            ->get();

        // Category breakdown
        $categoriesBreakdown = (clone $baseQuery)->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        return view('mail.dashboard', compact(
            'totalSent',
            'deliveredCount',
            'failedCount',
            'queuedCount',
            'scheduledCount',
            'sentToday',
            'successRate',
            'activeSetting',
            'jobsInQueue',
            'failedQueueJobs',
            'recentEmails',
            'categoriesBreakdown',
            'tenant',
            'isSuperAdmin'
        ));
    }
}
