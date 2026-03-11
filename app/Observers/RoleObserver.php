<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Role;
use App\Services\AuditService;

class RoleObserver
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {
    }

    /**
     * Логирует создание роли.
     */
    public function created(Role $role): void
    {
        $this->auditService->log(
            entityType: 'role',
            entityId: $role->id,
            before: [],
            after: $this->auditService->extractAttributes($role),
        );
    }

    /**
     * Логирует обновление роли.
     */
    public function updated(Role $role): void
    {
        $this->auditService->log(
            entityType: 'role',
            entityId: $role->id,
            before: $role->getOriginal(),
            after: $this->auditService->extractAttributes($role),
        );
    }

    /**
     * Логирует удаление роли.
     */
    public function deleted(Role $role): void
    {
        $this->auditService->log(
            entityType: 'role',
            entityId: $role->id,
            before: $this->auditService->extractAttributes($role),
            after: [],
        );
    }

    /**
     * Логирует восстановление роли.
     */
    public function restored(Role $role): void
    {
        $after = $this->auditService->extractAttributes($role);

        $this->auditService->log(
            entityType: 'role',
            entityId: $role->id,
            before: $after,
            after: $after,
        );
    }
}
