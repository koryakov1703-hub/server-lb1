<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Token;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TokenService
{
    public function generateTokenPair(User $user, ?string $ip = null, ?string $userAgent = null): array
    {
        $accessTtl = (int) env('ACCESS_TOKEN_TTL', 60);
        $refreshTtl = (int) env('REFRESH_TOKEN_TTL', 10080);

        $now = Carbon::now();
        $accessExpiresAt = $now->copy()->addMinutes($accessTtl);
        $refreshExpiresAt = $now->copy()->addMinutes($refreshTtl);

        $jti = (string) Str::uuid();
        $refreshPlain = Str::random(64);

        Token::create([
            'user_id' => $user->id,
            'jti' => $jti,
            'refresh_hash' => hash('sha256', $refreshPlain),
            'ip' => $ip,
            'user_agent' => $userAgent,
            'access_expires_at' => $accessExpiresAt,
            'refresh_expires_at' => $refreshExpiresAt,
        ]);

        $accessToken = $this->buildSignedToken(
            userId: $user->id,
            jti: $jti,
            expiresAt: $accessExpiresAt,
            type: 'access'
        );

        $refreshToken = $this->buildSignedToken(
            userId: $user->id,
            jti: $jti,
            expiresAt: $refreshExpiresAt,
            type: 'refresh',
            secretPart: $refreshPlain
        );

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ];
    }

    private function buildSignedToken(
        int $userId,
        string $jti,
        Carbon $expiresAt,
        string $type,
        ?string $secretPart = null
    ): string {
        $payload = [
            'uid' => $userId,
            'jti' => $jti,
            'exp' => $expiresAt->timestamp,
            'type' => $type,
        ];

        if ($secretPart !== null) {
            $payload['rnd'] = $secretPart;
        }

        $encodedPayload = base64_encode(json_encode($payload, JSON_UNESCAPED_UNICODE));
        $signature = hash_hmac('sha256', $encodedPayload, env('TOKEN_SECRET', 'default_secret'));

        return $encodedPayload . '.' . $signature;
    }
    public function parseAndValidateToken(string $token): ?array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 2) {
            return null;
        }

        [$encodedPayload, $signature] = $parts;

        $expectedSignature = hash_hmac(
            'sha256',
            $encodedPayload,
            env('TOKEN_SECRET', 'default_secret')
        );

        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }

        $payload = json_decode(base64_decode($encodedPayload), true);

        if (!is_array($payload)) {
            return null;
        }

        if (!isset($payload['uid'], $payload['jti'], $payload['exp'], $payload['type'])) {
            return null;
        }

        if ((int) $payload['exp'] < now()->timestamp) {
            return null;
        }

        return $payload;
    }
    public function validateAccessToken(string $token): ?Token
    {
        $payload = $this->parseAndValidateToken($token);

        if ($payload === null) {
            return null;
        }

        if ($payload['type'] !== 'access') {
            return null;
        }

        return Token::query()
            ->where('jti', $payload['jti'])
            ->whereNull('revoked_at')
            ->where('access_expires_at', '>', now())
            ->first();
    }
    public function resolveUserByAccessToken(string $token): ?\App\Models\User
    {
        $tokenModel = $this->validateAccessToken($token);

        if ($tokenModel === null) {
            return null;
        }

        return $tokenModel->user;
    }
}
