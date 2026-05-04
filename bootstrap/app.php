<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        \App\Providers\ImageServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'staff' => \App\Http\Middleware\StaffMiddleware::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'public' => \App\Http\Middleware\RedirectIfAuthenticatedToAdmin::class,
        ]);

        // Auto-complete expired bookings on every authenticated admin/staff request (max once per minute)
        $middleware->appendToGroup('web', \App\Http\Middleware\AutoCompleteBookings::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
