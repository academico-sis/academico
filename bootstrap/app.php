<?php

use App\Http\Middleware\ForceUpdate;
use App\Http\Middleware\NeedsTenant;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;
use Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Without tenant there is no database for the session: refuse the request first.
        $middleware->web(prepend: [
            NeedsTenant::class,
        ], append: [
            EnsureValidTenantSession::class,
            SetLocale::class,
        ]);

        $middleware->alias([
            'force_update' => ForceUpdate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        Integration::handles($exceptions);
    })->create();
