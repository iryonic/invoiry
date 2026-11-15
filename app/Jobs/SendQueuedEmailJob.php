<?php

namespace App\Jobs;

use App\Models\EmailLog;
use App\Services\MailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendQueuedEmailJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job with exponential backoff.
     *
     * @var array<int, int>
     */
    public array $backoff = [10, 60, 300];

    /**
     * Create a new job instance.
     */
    public function __construct(public int $emailLogId) {}

    /**
     * Execute the job.
     */
    public function handle(MailService $mailService): void
    {
        $log = EmailLog::find($this->emailLogId);

        if (! $log || $log->status === EmailLog::STATUS_CANCELLED || $log->status === EmailLog::STATUS_SENT) {
            return;
        }

        $mailService->sendNow($log);
    }

    /**
     * Handle a job failure after all retries are exhausted.
     */
    public function failed(?Throwable $exception): void
    {
        $log = EmailLog::find($this->emailLogId);

        if ($log) {
            $log->update([
                'status' => EmailLog::STATUS_FAILED,
                'error_message' => 'Job failed after max attempts: '.($exception?->getMessage() ?? 'Unknown error'),
            ]);
        }

        Log::error("SendQueuedEmailJob failed permanently for Log ID {$this->emailLogId}: ".($exception?->getMessage() ?? ''));
    }
}
