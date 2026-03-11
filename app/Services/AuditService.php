<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ChangeLog;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    /**
     * Сохраняет запись об изменении сущности.
     */
    public function log(string $entityType, int $entityId, array $before, array $after): void
    {
        ChangeLog::query()->create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'before' => $before,
            'after' => $after,
            'created_at' => now(),
            'created_by' => $this->resolveActorId(),
        ]);
    }

    /**
     * Возвращает данные модели без чувствительных и служебных полей.
     */
    public function extractAttributes(Model $model): array
    {
        $attributes = $model->getAttributes();

        unset(
            $attributes['password'],
            $attributes['remember_token']
        );

        return $attributes;
    }

    /**
     * Определяет пользователя, инициировавшего изменение.
     */
    private function resolveActorId(): int
    {
        $user = auth()->user();

        return $user?->id ?? 1;
    }
}
