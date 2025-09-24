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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'rate.limit.friends' => \App\Http\Middleware\RateLimitFriendRequests::class,
            'security' => \App\Http\Middleware\SecurityMiddleware::class,
        ]);

        // 웹 그룹에 보안 미들웨어 적용
        $middleware->web(append: [
            \App\Http\Middleware\SecurityMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
