<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('token') ?? $request->query('token');

        if (!$token || $token !== config('wallets.auth_token')   ) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid or missing token.'
            ], 401);
        }

        return $next($request);
    }
}
