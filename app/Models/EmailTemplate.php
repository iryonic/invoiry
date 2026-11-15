<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'category',
        'subject',
        'body_html',
        'body_text',
        'available_placeholders',
        'is_active',
        'is_default',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'available_placeholders' => 'array',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
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
     * Logs sent using this template.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(EmailLog::class, 'template_id');
    }

    /**
     * Scope active templates.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope category.
     */
    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope templates accessible to tenant (including global templates).
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
     * Standard placeholders across the system.
     *
     * @return array<string, string>
     */
    public static function standardPlaceholders(): array
    {
        return [
            'customer_name' => 'Full name of the customer',
            'customer_email' => 'Customer email address',
            'customer_phone' => 'Customer phone number',
            'invoice_number' => 'Invoice sequential number (e.g. INV-2026-0001)',
            'invoice_date' => 'Date of invoice creation (DD Mon YYYY)',
            'invoice_due_date' => 'Invoice payment due date',
            'invoice_total' => 'Grand total payable with currency symbol',
            'invoice_paid' => 'Amount paid by customer',
            'invoice_balance_due' => 'Remaining balance due / Khata credit',
            'payment_amount' => 'Recent payment transaction amount',
            'payment_date' => 'Payment transaction date & time',
            'payment_method' => 'Payment channel (Cash, UPI, Card, Bank)',
            'business_name' => 'Store or company trade name',
            'business_phone' => 'Store support phone number',
            'business_email' => 'Store contact email address',
            'business_address' => 'Store physical location / city',
            'business_upi' => 'Store UPI VPA for instant counter payment',
            'login_url' => 'Secure portal or app URL',
            'current_year' => 'Current Gregorian calendar year',
        ];
    }
}
