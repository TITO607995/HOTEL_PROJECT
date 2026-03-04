<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\BlockIpMiddleware; // Pastikan ini bener

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ALIAS DEFAULT
        $middleware->alias([
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ]);

        // 🔥 PASANG DI WEB JUGA BIAR TOTAL!
        $middleware->appendToGroup('web', BlockIpMiddleware::class);
        $middleware->appendToGroup('api', BlockIpMiddleware::class);
        
        // ATAU kalau mau lebih galak lagi (Global), ganti dua baris di atas jadi:
        // $middleware->append(BlockIpMiddleware::class); 
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();