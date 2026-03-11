<?php

declare(strict_types=1);

namespace App\DTO;

final class ChangeLogDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $entityType,
        public readonly int $entityId,
        public readonly array $changedFields,
        public readonly string $createdAt,
        public readonly int $createdBy,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'changed_fields' => $this->changedFields,
            'created_at' => $this->createdAt,
            'created_by' => $this->createdBy,
        ];
    }
}
