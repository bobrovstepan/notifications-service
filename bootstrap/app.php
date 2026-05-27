<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                $model = class_basename($e->getModel());

                return response()->json([
                    'message' => __('errors.not_found', ['model' => $model]),
                ], 404);
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') && ! $e instanceof HttpException && ! $e instanceof ValidationException) {
                return response()->json(['message' => __('errors.server_error')], 500);
            }
        });
    })->create();
