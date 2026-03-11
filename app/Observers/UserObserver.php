<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use App\Services\AuditService;

class UserObserver
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {
    }

    /**
     * Логирует создание пользователя.
     */
    public function created(User $user): void
    {
        $this->auditService->log(
            entityType: 'user',
            entityId: $user->id,
            before: [],
            after: $this->auditService->extractAttributes($user),
        );
    }

    /**
     * Логирует обновление пользователя.
     */
    public function updated(User $user): void
    {
        $before = $this->filterAttributes($user->getOriginal());
        $after = $this->auditService->extractAttributes($user);

        $this->auditService->log(
            entityType: 'user',
            entityId: $user->id,
            before: $before,
            after: $after,
        );
    }

    /**
     * Логирует удаление пользователя.
     */
    public function deleted(User $user): void
    {
        $this->auditService->log(
            entityType: 'user',
            entityId: $user->id,
            before: $this->auditService->extractAttributes($user),
            after: [],
        );
    }

    /**
     * Логирует восстановление пользователя.
     */
    public function restored(User $user): void
    {
        $after = $this->auditService->extractAttributes($user);

        $this->auditService->log(
            entityType: 'user',
            entityId: $user->id,
            before: $after,
            after: $after,
        );
    }

    /**
     * Исключает чувствительные и служебные поля из исходных атрибутов.
     */
    private function filterAttributes(array $attributes): array
    {
        unset(
            $attributes['password'],
            $attributes['remember_token']
        );

        return $attributes;
    }
}
