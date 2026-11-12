<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTerminalNotLocked
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && session('terminal_locked') === true) {
            if (! $request->routeIs('lock') && ! $request->routeIs('unlock') && ! $request->routeIs('logout')) {
                return redirect()->route('lock');
            }
        }

        return $next($request);
    }
}
