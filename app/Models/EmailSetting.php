<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'provider_name',
        'display_name',
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'from_address',
        'from_name',
        'reply_to_address',
        'is_active',
        'is_default',
        'last_tested_at',
        'last_test_status',
        'last_test_error',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'mail_password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mail_password' => 'encrypted',
            'mail_port' => 'integer',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'last_tested_at' => 'datetime',
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
     * Scope active providers.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope default provider.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope settings for a specific tenant or global fallback.
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
     * Masked password placeholder for UI rendering.
     */
    public function getMaskedPassword(): string
    {
        return ! empty($this->attributes['mail_password']) ? '••••••••••••' : '';
    }
}
