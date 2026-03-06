<?php

declare(strict_types=1);

namespace App\Http\Controllers;
use App\DTO\TokenDTO;
use Illuminate\Support\Facades\Auth;
use App\DTO\AuthSuccessDTO;
use App\DTO\UserDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly TokenService $tokenService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $dto = $request->toDTO();

        $user = User::query()
            ->where('name', $dto->username)
            ->first();

        if (!$user || !Hash::check($dto->password, $user->password)) {
            return response()->json([
                'message' => 'Неверный логин или пароль'
            ], 401);
        }

        $tokens = $this->tokenService->generateTokenPair(
            $user,
            $request->ip(),
            $request->userAgent()
        );

        $response = new AuthSuccessDTO(
            accessToken: $tokens['access_token'],
            refreshToken: $tokens['refresh_token'],
            user: new UserDTO(
                id: $user->id,
                username: $user->name,
                email: $user->email,
                birthday: (string) $user->birthday
            )
        );

        return response()->json($response);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = $request->toDTO();

        $user = User::query()->create([
            'name' => $dto->username,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'birthday' => $dto->birthday,
        ]);

        $userDto = new UserDTO(
            id: $user->id,
            username: $user->name,
            email: $user->email,
            birthday: (string) $user->birthday,
        );

        return response()->json($userDto->toArray(), 201);
    }

    public function me(Request $request): JsonResponse
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'message' => 'Token required'
            ], 401);
        }

        $token = substr($authHeader, 7);

        $user = $this->tokenService->resolveUserByAccessToken($token);

        if (!$user) {
            return response()->json([
                'message' => 'Invalid or expired token'
            ], 401);
        }

        $dto = new UserDTO(
            id: $user->id,
            username: $user->name,
            email: $user->email,
            birthday: (string) $user->birthday
        );

        return response()->json($dto->toArray());
    }

    public function out(Request $request): JsonResponse
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'message' => 'Token required'
            ], 401);
        }

        $token = substr($authHeader, 7);

        if (!$this->tokenService->revokeAccessToken($token)) {
            return response()->json([
                'message' => 'Invalid or expired token'
            ], 401);
        }

        return response()->json([
            'message' => 'Logged out'
        ]);
    }

    public function tokens(Request $request): JsonResponse
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'message' => 'Token required'
            ], 401);
        }

        $token = substr($authHeader, 7);

        $user = $this->tokenService->resolveUserByAccessToken($token);

        if (!$user) {
            return response()->json([
                'message' => 'Invalid or expired token'
            ], 401);
        }

        $tokens = $this->tokenService->getActiveTokensForUser($user);

        $result = $tokens->map(function ($tokenModel) {
            $dto = new TokenDTO(
                jti: $tokenModel->jti,
                ip: $tokenModel->ip ?? '',
                userAgent: $tokenModel->user_agent ?? '',
                createdAt: (string) $tokenModel->created_at,
                expiresAt: (string) $tokenModel->access_expires_at,
            );

            return $dto->toArray();
        });

        return response()->json($result->values());
    }

    public function outAll(Request $request): JsonResponse
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'message' => 'Token required'
            ], 401);
        }

        $token = substr($authHeader, 7);

        $user = $this->tokenService->resolveUserByAccessToken($token);

        if (!$user) {
            return response()->json([
                'message' => 'Invalid or expired token'
            ], 401);
        }

        $this->tokenService->revokeAllTokensForUser($user);

        return response()->json([
            'message' => 'Logged out from all devices'
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        return response()->json(['message' => 'Not implemented yet'], 501);
    }
}
