<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailAutomation extends Model
{
    use HasFactory;

    public const EVENT_INVOICE_FINALIZED = 'invoice.finalized';

    public const EVENT_PAYMENT_CREATED = 'payment.created';

    public const EVENT_INVOICE_DUE_REMINDER = 'invoice.due_reminder';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'event_name',
        'template_id',
        'is_enabled',
        'timing_days',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'timing_days' => 'integer',
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
     * Email template relationship.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id');
    }

    /**
     * Scope enabled automations.
     */
    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope for a specific tenant or global.
     */
    public function scopeForTenantOrGlobal(Builder $query, ?int $tenantId): Builder
    {
        return $query->where(function (Builder $q) use ($tenantId) {
            if ($tenantId !== null) {
                $q->where('tenant_id', $tenantId)->orWhereNull('tenant_id');
            } else {
                $q->whereNull('tenant_id');
            }
        });
    }

    /**
     * Standard automation events with labels.
     *
     * @return array<string, array{label: string, default_description: string, default_template_code: string}>
     */
    public static function standardEvents(): array
    {
        return [
            self::EVENT_INVOICE_FINALIZED => [
                'label' => 'Auto-Send Invoice on Generation',
                'default_description' => 'Automatically email the generated PDF invoice to the customer when a new bill is finalized.',
                'default_template_code' => 'invoice_generated',
            ],
            self::EVENT_PAYMENT_CREATED => [
                'label' => 'Auto-Send Payment Receipt',
                'default_description' => 'Instantly dispatch a payment confirmation email whenever full or partial settlement is logged.',
                'default_template_code' => 'payment_received',
            ],
            self::EVENT_INVOICE_DUE_REMINDER => [
                'label' => 'Overdue Payment Reminders',
                'default_description' => 'Send automatic payment reminders for unpaid invoices past their payment due date.',
                'default_template_code' => 'payment_reminder',
            ],
        ];
    }
}
