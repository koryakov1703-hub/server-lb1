<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\TokenService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenMiddleware
{
    public function __construct(
        private readonly TokenService $tokenService
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'message' => 'Token required',
            ], 401);
        }

        $token = substr($authHeader, 7);
        $user = $this->tokenService->resolveUserByAccessToken($token);

        if ($user === null) {
            return response()->json([
                'message' => 'Invalid or expired token',
            ], 401);
        }

        $request->attributes->set('auth_user', $user);

        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
