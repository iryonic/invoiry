<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Session;

class TenantContext
{
    protected static ?Tenant $overrideTenant = null;

    public static function set(?Tenant $tenant): void
    {
        static::$overrideTenant = $tenant;
    }

    public static function get(): ?Tenant
    {
        if (static::$overrideTenant !== null) {
            return static::$overrideTenant;
        }

        // If super admin has switched tenant in session
        if (Session::has('active_tenant_id')) {
            return Tenant::find(Session::get('active_tenant_id'));
        }

        $user = auth()->user();
        if ($user && $user->tenant_id) {
            return $user->tenant;
        }

        // Super Admin fallback: default to the first active business store so context is never null
        if ($user && $user->isSuperAdmin()) {
            return Tenant::where('is_active', true)->first() ?? Tenant::first();
        }

        return null;
    }

    public static function id(): ?int
    {
        return static::get()?->id;
    }
}
