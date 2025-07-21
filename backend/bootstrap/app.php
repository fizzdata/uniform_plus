<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Request;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
    $exceptions->reportable(function (Throwable $e) {
          Log::channel('slack')->error($e->getMessage(),[
               'File' => $e->getFile(),
                'Line' => $e->getLine(),
                'Method' => Request::method(),
                'URL' => (string) Request::fullUrl(),
                'IP' => Request::ip(),
                'Time' => now()->toDateTimeString(),
          ]);
      });
    })->create();
