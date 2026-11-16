<?php

namespace App\Http\Middleware;

use App\Services\BusinessTypeManager;
use App\Services\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBusinessType
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$allowedTypes): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Super Admin in platform console bypasses checks
        if ($user->isSuperAdmin() && ! session()->has('active_tenant_id') && $request->is('superadmin*')) {
            return $next($request);
        }

        $tenant = TenantContext::get();
        if (! $tenant) {
            return $next($request);
        }

        $businessType = $tenant->business_type ?? BusinessTypeManager::TYPE_SELLING;

        // If 'both' is allowed or matches
        if (in_array($businessType, $allowedTypes, true) || $businessType === BusinessTypeManager::TYPE_BOTH) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => "This functionality is restricted for '{$tenant->business_type_name}' businesses.",
            ], 403);
        }

        // Friendly redirect based on business type
        if ($businessType === BusinessTypeManager::TYPE_REPAIR) {
            return redirect()->route('repairs.index')->with('error', 'Device sales catalogue is restricted for Mobile Repair businesses. Access your Repair Jobs or Spare Parts instead.');
        }

        return redirect()->route('items.index')->with('error', 'Mobile Repair modules are restricted for Mobile Selling & Reselling businesses.');
    }
}
