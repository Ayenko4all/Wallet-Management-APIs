<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage() ?: 'Unauthenticated'
                ], 401);
            }



            if ($e instanceof ValidationException) {
                return response()->json([
                    'status' => false,
                    'message' => $e->validator->errors()->all()[0]
                ], 422);
            }



            $code = validHttpStatusCode($e->getCode()) ? $e->getCode() : Response::HTTP_BAD_REQUEST;
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], $code);
        });


    })->create();

function validHttpStatusCode(int $code): bool
{
    return array_key_exists($code, Response::$statusTexts);
}
