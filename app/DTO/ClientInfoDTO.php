<?php

namespace App\DTO;

final readonly class ClientInfoDTO
{
    public function __construct(
        public string $ipAddress,
        public string $userAgent
    ) {}

    public function toArray(): array
    {
        return [
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
        ];
    }
}
