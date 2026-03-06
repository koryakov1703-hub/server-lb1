<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class TokenDTO
{
    public function __construct(
        public string $jti,
        public string $ip,
        public string $userAgent,
        public string $createdAt,
        public string $expiresAt
    ) {}
}
