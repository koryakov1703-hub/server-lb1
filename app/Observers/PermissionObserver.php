<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Permission;
use App\Services\AuditService;

class PermissionObserver
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {
    }

    /**
     * Логирует создание разрешения.
     */
    public function created(Permission $permission): void
    {
        $this->auditService->log(
            entityType: 'permission',
            entityId: $permission->id,
            before: [],
            after: $this->auditService->extractAttributes($permission),
        );
    }

    /**
     * Логирует обновление разрешения.
     */
    public function updated(Permission $permission): void
    {
        $this->auditService->log(
            entityType: 'permission',
            entityId: $permission->id,
            before: $permission->getOriginal(),
            after: $this->auditService->extractAttributes($permission),
        );
    }

    /**
     * Логирует удаление разрешения.
     */
    public function deleted(Permission $permission): void
    {
        $this->auditService->log(
            entityType: 'permission',
            entityId: $permission->id,
            before: $this->auditService->extractAttributes($permission),
            after: [],
        );
    }

    /**
     * Логирует восстановление разрешения.
     */
    public function restored(Permission $permission): void
    {
        $after = $this->auditService->extractAttributes($permission);

        $this->auditService->log(
            entityType: 'permission',
            entityId: $permission->id,
            before: $after,
            after: $after,
        );
    }
}
