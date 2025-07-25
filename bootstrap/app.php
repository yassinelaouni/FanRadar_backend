<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        //web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(HandleCors::class); // gérer CORS

    })
    ->withExceptions(function (Exceptions $exceptions): void {
         $exceptions->render(function (Throwable $e, Illuminate\Http\Request $request) {
        if ($request->expectsJson() || $request->is('api/*')) {
            if ($e instanceof HttpException) {
                $status = $e->getStatusCode();
            } else {
                $status = 500;
            }
            return response()->json([
                'message' => $e->getMessage(),
                'exception' => class_basename(get_class($e)),
                'status' => $status,
            ], $status);
        }
    });
    })->create();
