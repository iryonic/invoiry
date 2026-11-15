<?php

use App\Http\Middleware\CheckMaintenanceMode;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\EnsureTerminalNotLocked;
use App\Http\Middleware\EnsureUserRole;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            CheckMaintenanceMode::class,
            EnsureTerminalNotLocked::class,
        ]);

        $middleware->alias([
            'role' => EnsureUserRole::class,
            'permission' => CheckPermission::class,
            'terminal.lock' => EnsureTerminalNotLocked::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if (! $request->expectsJson()) {
                return redirect()->back()->withInput()->with('error', 'Your security session has expired due to inactivity. Please submit again.');
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if (! $request->expectsJson()) {
                return response()->view('errors.403', ['exception' => $e], 403);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if (! $request->expectsJson()) {
                return response()->view('errors.404', ['exception' => $e], 404);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if (! $request->expectsJson()) {
                return response()->view('errors.404', ['exception' => $e], 404);
            }
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if (! $request->expectsJson() && view()->exists("errors.{$e->getStatusCode()}")) {
                return response()->view("errors.{$e->getStatusCode()}", ['exception' => $e], $e->getStatusCode());
            }
        });
    })->create();
