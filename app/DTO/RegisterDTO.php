<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class RegisterDTO
{
    public function __construct(
        public string $username,
        public string $email,
        public string $password,
        public string $birthday
    ) {}
}
