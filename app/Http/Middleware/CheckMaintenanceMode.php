<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request and enforce platform maintenance mode.
     * Super Admins and essential superadmin/auth routes are permitted.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (SystemSetting::isEnabled('maintenance_mode', false)) {
            // 1. Allow Super Admin users full access
            if (auth()->check() && auth()->user()->isSuperAdmin()) {
                return $next($request);
            }

            // 2. Allow login/logout and superadmin control routes so Super Admin can access platform controls
            if ($request->routeIs('login') || $request->routeIs('logout') || $request->is('superadmin*')) {
                return $next($request);
            }

            // 3. For JSON/API requests return 503 JSON
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => SystemSetting::get('maintenance_message', 'The platform is currently under scheduled maintenance.'),
                    'status' => 503,
                ], 503);
            }

            // 4. Render dedicated 503 maintenance page
            return response()->view('errors.503', [], 503);
        }

        return $next($request);
    }
}
