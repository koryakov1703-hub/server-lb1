<?php

declare(strict_types=1);

namespace App\DTO;

final class ChangeLogCollectionDTO
{
    /**
     * @param ChangeLogDTO[] $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $total,
    ) {
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(
                static fn (ChangeLogDTO $dto): array => $dto->toArray(),
                $this->items
            ),
            'meta' => [
                'total' => $this->total,
            ],
        ];
    }
}
