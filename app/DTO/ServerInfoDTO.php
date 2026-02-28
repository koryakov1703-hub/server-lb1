<?php

namespace App\DTO;

final readonly class ServerInfoDTO
{
    public function __construct(
        public string $phpVersion,
        public string $phpSapi,
        public string $os
    ) {}

    public function toArray(): array
    {
        return [
            'php_version' => $this->phpVersion,
            'php_sapi' => $this->phpSapi,
            'os' => $this->os,
        ];
    }
}
