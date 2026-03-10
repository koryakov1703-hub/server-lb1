<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response|JsonResponse
    {
        $user = $request->user();

        if ($user === null) {
            return response()->json([
                'error' => 'Unauthenticated',
            ], 401);
        }

        if (!method_exists($user, 'hasPermission') || !$user->hasPermission($permission)) {
            return response()->json([
                'error' => 'Access denied. Required permission: ' . $permission,
            ], 403);
        }

        return $next($request);
    }
}
