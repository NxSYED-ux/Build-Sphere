<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.jwt'  => \App\Http\Middleware\AuthMiddleware::class,
            'check.permission' => \App\Http\Middleware\ValidatePermission::class,
            'plan' => \App\Http\Middleware\PlanMiddleware::class,
        ]);
        $middleware->appendToGroup('web', \App\Http\Middleware\FixBackRedirect::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
