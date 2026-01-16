<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/hod.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            '/api/student/validate-matric',
            '/student/register-face',
        ]);
        $middleware->alias([
            'hod.role' => \App\Http\Middleware\EnsureHODRole::class,
            'hod.department' => \App\Http\Middleware\VerifyDepartmentOwnership::class,
            'hod.session' => \App\Http\Middleware\HODSessionTimeout::class,
            'require.2fa' => \App\Http\Middleware\RequireTwoFactorAuth::class,
            'api.key' => \App\Http\Middleware\AuthenticateApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
