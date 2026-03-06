<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class UserDTO
{
    public function __construct(
        public int $id,
        public string $username,
        public string $email,
        public string $birthday
    ) {}
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'birthday' => $this->birthday,
        ];
    }
}
