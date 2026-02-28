<?php

namespace App\DTO;

final readonly class DatabaseInfoDTO
{
    public function __construct(
        public string $driver,
        public string $database,
        public string $serverVersion
    ) {}

    public function toArray(): array
    {
        return [
            'driver' => $this->driver,
            'database' => $this->database,
            'server_version' => $this->serverVersion,
        ];
    }
}
