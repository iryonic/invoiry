<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\BusinessTypeManager;
use App\Services\TenantContext;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'avatar',
        'password',
        'role',
        'is_active',
        'permissions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'permissions' => 'array',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/'.$this->avatar);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=B7FF00&background=050505&bold=true';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isTenantAdmin(): bool
    {
        return $this->role === 'tenant_admin' || $this->role === 'super_admin';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isTechnician(): bool
    {
        return $this->role === 'technician';
    }

    public function assignedRepairJobs(): HasMany
    {
        return $this->hasMany(RepairJob::class, 'assigned_to');
    }

    public function hasPermission(string $permission): bool
    {
        $tenant = $this->tenant ?? TenantContext::get();

        // Business-type restriction: independent of individual user permissions
        if ($tenant) {
            $module = explode('.', $permission)[0];
            if (! BusinessTypeManager::isModuleAllowed($tenant->business_type ?? BusinessTypeManager::TYPE_SELLING, $module)) {
                return false;
            }
        }

        // Super Admin bypasses role checks (when not restricted by tenant business-type above)
        if ($this->isSuperAdmin()) {
            return true;
        }

        // 1. Check user-specific permissions override if explicitly configured
        if (is_array($this->permissions) && ! empty($this->permissions)) {
            if (array_key_exists($permission, $this->permissions)) {
                return (bool) $this->permissions[$permission];
            }
            $parts = explode('.', $permission);
            if (count($parts) === 2 && isset($this->permissions[$parts[0]]) && is_array($this->permissions[$parts[0]]) && array_key_exists($parts[1], $this->permissions[$parts[0]])) {
                return (bool) $this->permissions[$parts[0]][$parts[1]];
            }
            if (count($parts) === 1 && isset($this->permissions[$parts[0]])) {
                if (is_array($this->permissions[$parts[0]])) {
                    return (bool) ($this->permissions[$parts[0]]['view'] ?? true);
                }

                return (bool) $this->permissions[$parts[0]];
            }
        }

        // 2. Role-level baseline for cashiers without custom overrides
        if ($this->role === 'cashier') {
            $allowedCashierActions = [
                'dashboard.view', 'dashboard',
                'invoices.view', 'invoices.create', 'invoices.print', 'invoices.record_payments', 'invoices.email',
                'repairs.view', 'repairs.create', 'repairs.payments', 'repairs.print',
                'items.view', 'spare_parts.view', 'customers.view', 'customers.create',
            ];
            $module = explode('.', $permission)[0];
            if (! in_array($permission, $allowedCashierActions, true) && ! in_array($module, ['dashboard', 'invoices', 'repairs', 'items', 'spare_parts', 'customers'], true)) {
                return false;
            }
        }

        // 3. Role-level baseline for technicians without custom overrides
        if ($this->role === 'technician') {
            $allowedTechnicianActions = [
                'dashboard.view', 'dashboard',
                'repairs.view', 'repairs.edit', 'repairs.status_update', 'repairs.billing', 'repairs.print',
                'spare_parts.view', 'customers.view',
            ];
            $module = explode('.', $permission)[0];
            if (! in_array($permission, $allowedTechnicianActions, true) && ! in_array($module, ['dashboard', 'repairs', 'spare_parts', 'customers'], true)) {
                return false;
            }
        }

        // 4. Check tenant-level permissions
        if ($tenant && is_array($tenant->permissions)) {
            if (array_key_exists($permission, $tenant->permissions)) {
                return (bool) $tenant->permissions[$permission];
            }
            $parts = explode('.', $permission);
            if (count($parts) === 2 && isset($tenant->permissions[$parts[0]]) && is_array($tenant->permissions[$parts[0]]) && array_key_exists($parts[1], $tenant->permissions[$parts[0]])) {
                return (bool) $tenant->permissions[$parts[0]][$parts[1]];
            }
            if (count($parts) === 1 && isset($tenant->permissions[$parts[0]])) {
                if (is_array($tenant->permissions[$parts[0]])) {
                    return (bool) ($tenant->permissions[$parts[0]]['view'] ?? true);
                }

                return (bool) $tenant->permissions[$parts[0]];
            }
        }

        // Default: enabled by default
        return true;
    }

    public function canAccess(string $moduleOrPermission): bool
    {
        return $this->hasPermission($moduleOrPermission);
    }

    public function canManageStaff(): bool
    {
        return ($this->isSuperAdmin() || $this->role === 'tenant_admin') && $this->hasPermission('staff.view');
    }

    public function canAccessSettings(): bool
    {
        return ($this->isSuperAdmin() || $this->role === 'tenant_admin') && $this->hasPermission('settings.view');
    }

    public function canAccessMail(): bool
    {
        return ($this->isSuperAdmin() || $this->role === 'tenant_admin') && $this->hasPermission('mail.view');
    }

    public function canInwardStock(): bool
    {
        return ($this->isSuperAdmin() || $this->role === 'tenant_admin') && $this->hasPermission('items.create');
    }

    public function canDeleteInvoices(): bool
    {
        return ($this->isSuperAdmin() || $this->role === 'tenant_admin') && $this->hasPermission('invoices.delete');
    }
}
