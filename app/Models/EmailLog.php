<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_QUEUED = 'queued';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_SENT = 'sent';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'user_id',
        'customer_id',
        'invoice_id',
        'template_id',
        'category',
        'recipient_email',
        'recipient_name',
        'cc',
        'bcc',
        'subject',
        'body_html',
        'body_text',
        'attachments',
        'mailer_used',
        'status',
        'attempts',
        'error_message',
        'scheduled_at',
        'sent_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cc' => 'array',
            'bcc' => 'array',
            'attachments' => 'array',
            'attempts' => 'integer',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    /**
     * Tenant relationship.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * User (author/sender) relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Customer relationship.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Invoice relationship.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Template relationship.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id');
    }

    /**
     * Scope for a specific tenant or global admin view.
     */
    public function scopeForTenant(Builder $query, ?int $tenantId): Builder
    {
        if ($tenantId === null) {
            return $query;
        }

        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope sent logs.
     */
    public function scopeSent(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_SENT, self::STATUS_DELIVERED]);
    }

    /**
     * Scope failed logs.
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope queued logs.
     */
    public function scopeQueued(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_QUEUED, self::STATUS_PROCESSING]);
    }

    /**
     * Scope pending future scheduled emails.
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_QUEUED)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>', now());
    }

    /**
     * Badge CSS class helper based on design.md status colors.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_SENT, self::STATUS_DELIVERED => 'bg-[#ECFDF3] text-[#027A48] border-[#A6F4C5]',
            self::STATUS_QUEUED, self::STATUS_PROCESSING => 'bg-[#EFF8FF] text-[#175CD3] border-[#B2DDFF]',
            self::STATUS_FAILED => 'bg-[#FEF2F2] text-[#B91C1C] border-[#FECACA]',
            self::STATUS_DRAFT => 'bg-[#F2F4F7] text-[#344054] border-[#EAECF0]',
            self::STATUS_CANCELLED => 'bg-[#F7F7F6] text-[#666666] border-[#E6E6E6]',
            default => 'bg-[#F2F4F7] text-[#344054] border-[#EAECF0]',
        };
    }
}
