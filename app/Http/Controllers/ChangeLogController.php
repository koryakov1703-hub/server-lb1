<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\ChangeLogCollectionDTO;
use App\DTO\ChangeLogDTO;
use App\Models\ChangeLog;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ChangeLogController extends Controller
{
    private const ENTITY_USER = 'user';
    private const ENTITY_ROLE = 'role';
    private const ENTITY_PERMISSION = 'permission';

    /**
     * Возвращает историю изменений пользователя.
     */
    public function userStory(User $user): JsonResponse
    {
        return $this->getStory(
            entityType: self::ENTITY_USER,
            entityId: $user->id,
        );
    }

    /**
     * Возвращает историю изменений роли.
     */
    public function roleStory(Role $role): JsonResponse
    {
        return $this->getStory(
            entityType: self::ENTITY_ROLE,
            entityId: $role->id,
        );
    }

    /**
     * Возвращает историю изменений разрешения.
     */
    public function permissionStory(Permission $permission): JsonResponse
    {
        return $this->getStory(
            entityType: self::ENTITY_PERMISSION,
            entityId: $permission->id,
        );
    }

    /**
     * Формирует историю изменений для указанной сущности.
     */
    private function getStory(string $entityType, int $entityId): JsonResponse
    {
        $logs = ChangeLog::query()
            ->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->orderByDesc('created_at')
            ->get();

        $items = $logs->map(function (ChangeLog $log): ChangeLogDTO {
            $before = is_array($log->before) ? $log->before : [];
            $after = is_array($log->after) ? $log->after : [];

            return new ChangeLogDTO(
                id: $log->id,
                entityType: $log->entity_type,
                entityId: $log->entity_id,
                changedFields: $this->buildChangedFields($before, $after),
                createdAt: $log->created_at->toDateTimeString(),
                createdBy: $log->created_by,
            );
        })->all();

        $collection = new ChangeLogCollectionDTO(
            items: $items,
            total: count($items),
        );

        return response()->json($collection->toArray());
    }

    /**
     * Возвращает только изменившиеся поля в формате old/new.
     * Чувствительные и служебные поля исключаются из результата.
     */
    private function buildChangedFields(array $before, array $after): array
    {
        $changedFields = [];
        $keys = array_unique(array_merge(array_keys($before), array_keys($after)));

        foreach ($keys as $key) {
            if (in_array($key, [
                'password',
                'remember_token',
                'created_at',
                'updated_at',
                'deleted_at',
                'deleted_by',
            ], true)) {
                continue;
            }

            $oldValue = $before[$key] ?? null;
            $newValue = $after[$key] ?? null;

            if ($oldValue === $newValue) {
                continue;
            }

            $changedFields[$key] = [
                'old' => $oldValue,
                'new' => $newValue,
            ];
        }

        return $changedFields;
    }
}
