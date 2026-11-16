<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairJob extends Model
{
    use HasFactory;

    public const STATUS_RECEIVED = 'received';

    public const STATUS_UNDER_DIAGNOSIS = 'under_diagnosis';

    public const STATUS_AWAITING_APPROVAL = 'awaiting_approval';

    public const STATUS_WAITING_FOR_PARTS = 'waiting_for_parts';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'assigned_to',
        'created_by',
        'job_number',
        'device_brand',
        'device_model',
        'imei',
        'serial_number',
        'device_color',
        'device_password',
        'device_condition',
        'accessories_received',
        'reported_problem',
        'diagnosis_notes',
        'technician_notes',
        'status',
        'priority',
        'estimated_cost',
        'labour_charge',
        'parts_total',
        'additional_charges',
        'discount',
        'tax_amount',
        'total_amount',
        'amount_paid',
        'expected_completion_date',
        'completed_at',
        'delivered_at',
        'warranty_days',
    ];

    protected function casts(): array
    {
        return [
            'estimated_cost' => 'decimal:2',
            'labour_charge' => 'decimal:2',
            'parts_total' => 'decimal:2',
            'additional_charges' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'expected_completion_date' => 'date',
            'completed_at' => 'datetime',
            'delivered_at' => 'datetime',
            'warranty_days' => 'integer',
        ];
    }

    /**
     * Get all available status definitions with readable labels and styling.
     *
     * @return array<string, array{label: string, badge_class: string, step: int, description: string}>
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_RECEIVED => [
                'label' => 'Received',
                'badge_class' => 'bg-[#EFF8FF] text-[#175CD3] border-[#B2DDFF]',
                'step' => 1,
                'description' => 'Device checked in at counter and registered',
            ],
            self::STATUS_UNDER_DIAGNOSIS => [
                'label' => 'Under Diagnosis',
                'badge_class' => 'bg-[#F4F3FF] text-[#5925DC] border-[#D9D6FE]',
                'step' => 2,
                'description' => 'Technician inspecting device and identifying fault',
            ],
            self::STATUS_AWAITING_APPROVAL => [
                'label' => 'Awaiting Customer Approval',
                'badge_class' => 'bg-[#FEF0C7] text-[#B54708] border-[#FEDF89]',
                'step' => 3,
                'description' => 'Quote shared; waiting for customer approval',
            ],
            self::STATUS_WAITING_FOR_PARTS => [
                'label' => 'Waiting for Parts',
                'badge_class' => 'bg-[#FFFAEB] text-[#B54708] border-[#FEDF89]',
                'step' => 4,
                'description' => 'Components on order or awaiting inward delivery',
            ],
            self::STATUS_IN_PROGRESS => [
                'label' => 'In Progress',
                'badge_class' => 'bg-[#F0FDF9] text-[#0E7090] border-[#A5F3FC]',
                'step' => 5,
                'description' => 'Repair actively being performed by technician',
            ],
            self::STATUS_COMPLETED => [
                'label' => 'Completed',
                'badge_class' => 'bg-[#ECFDF3] text-[#027A48] border-[#A6F4C5]',
                'step' => 6,
                'description' => 'Repair finished and quality tested; ready for pickup',
            ],
            self::STATUS_DELIVERED => [
                'label' => 'Delivered',
                'badge_class' => 'bg-[#050505] text-[#B7FF00] border-[#222]',
                'step' => 7,
                'description' => 'Delivered to customer with payment completed',
            ],
            self::STATUS_CANCELLED => [
                'label' => 'Cancelled',
                'badge_class' => 'bg-[#FEF2F2] text-[#B91C1C] border-[#FECACA]',
                'step' => 0,
                'description' => 'Repair cancelled or returned unrepaired',
            ],
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RepairJobItem::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(RepairJobActivity::class)->latest();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status]['label'] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return self::getStatuses()[$this->status]['badge_class'] ?? 'bg-[#F7F7F6] text-[#666666] border-[#E6E6E6]';
    }

    public function getDueAmountAttribute(): float
    {
        return max(0.00, round((float) $this->total_amount - (float) $this->amount_paid, 2));
    }

    public function getDueBalanceAttribute(): float
    {
        return $this->getDueAmountAttribute();
    }

    public function getStatusInfoAttribute(): array
    {
        $st = self::getStatuses()[$this->status] ?? [
            'label' => ucfirst($this->status),
            'badge_class' => 'bg-gray-100 text-gray-800',
            'step' => 1,
            'description' => '',
        ];
        $st['color'] = $st['badge_class'];

        return $st;
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_DELIVERED], true);
    }

    public function isDelivered(): bool
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Recalculate parts total, net total, and balance.
     */
    public function recalculateTotals(): void
    {
        $partsTotal = (float) $this->items()->where('item_type', 'part')->sum('total_price');
        $this->parts_total = $partsTotal;

        $subtotal = $partsTotal + (float) $this->labour_charge + (float) $this->additional_charges;
        $discounted = max(0.00, $subtotal - (float) $this->discount);
        $total = $discounted + (float) $this->tax_amount;

        $this->total_amount = round($total, 2);
        $this->save();
    }

    /**
     * Record an activity event on this job.
     */
    public function addActivity(string $type, ?string $notes = null, ?string $fromStatus = null, ?string $toStatus = null, ?int $userId = null): RepairJobActivity
    {
        return $this->activities()->create([
            'user_id' => $userId ?? auth()->id(),
            'activity_type' => $type,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'notes' => $notes,
        ]);
    }
}
