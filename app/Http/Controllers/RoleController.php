<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\RoleDTO;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Возвращает список активных ролей.
     */
    public function index(): JsonResponse
    {
        $roles = Role::query()
            ->whereNull('deleted_at')
            ->get();

        $result = $roles->map(function (Role $role) {
            $dto = new RoleDTO(
                id: $role->id,
                name: $role->name,
                slug: $role->slug,
                description: $role->description,
                createdAt: (string) $role->created_at
            );

            return $dto->toArray();
        });

        return response()->json($result->values());
    }

    /**
     * Создаёт новую роль.
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request): JsonResponse {
            $dto = $request->toDTO();

            $role = Role::query()->create([
                'name' => $dto->name,
                'slug' => $dto->slug,
                'description' => $dto->description,
                'created_by' => $request->user()->id,
            ]);

            $result = new RoleDTO(
                id: $role->id,
                name: $role->name,
                slug: $role->slug,
                description: $role->description,
                createdAt: (string) $role->created_at
            );

            return response()->json($result->toArray(), 201);
        });
    }

    /**
     * Возвращает конкретную роль.
     */
    public function show(int $role): JsonResponse
    {
        $roleModel = Role::query()
            ->whereNull('deleted_at')
            ->findOrFail($role);

        $dto = new RoleDTO(
            id: $roleModel->id,
            name: $roleModel->name,
            slug: $roleModel->slug,
            description: $roleModel->description,
            createdAt: (string) $roleModel->created_at
        );

        return response()->json($dto->toArray());
    }

    /**
     * Обновляет данные роли.
     */
    public function update(UpdateRoleRequest $request, int $role): JsonResponse
    {
        return DB::transaction(function () use ($request, $role): JsonResponse {
            $roleModel = Role::query()
                ->whereNull('deleted_at')
                ->findOrFail($role);

            $dto = $request->toDTO();

            $roleModel->update([
                'name' => $dto->name,
                'slug' => $dto->slug,
                'description' => $dto->description,
            ]);

            $result = new RoleDTO(
                id: $roleModel->id,
                name: $roleModel->name,
                slug: $roleModel->slug,
                description: $roleModel->description,
                createdAt: (string) $roleModel->created_at
            );

            return response()->json($result->toArray());
        });
    }

    /**
     * Выполняет жёсткое удаление роли.
     */
    public function destroy(int $role): JsonResponse
    {
        return DB::transaction(function () use ($role): JsonResponse {
            $roleModel = Role::withTrashed()->findOrFail($role);

            $roleModel->forceDelete();

            return response()->json([
                'message' => 'Role deleted permanently'
            ]);
        });
    }

    /**
     * Выполняет мягкое удаление роли.
     */
    public function softDelete(Request $request, int $role): JsonResponse
    {
        return DB::transaction(function () use ($request, $role): JsonResponse {
            $roleModel = Role::query()
                ->whereNull('deleted_at')
                ->findOrFail($role);

            $roleModel->update([
                'deleted_by' => $request->user()->id,
            ]);

            $roleModel->delete();

            return response()->json([
                'message' => 'Role soft deleted'
            ]);
        });
    }

    /**
     * Восстанавливает мягко удалённую роль.
     */
    public function restore(int $role): JsonResponse
    {
        return DB::transaction(function () use ($role): JsonResponse {
            $roleModel = Role::withTrashed()->findOrFail($role);

            $roleModel->update([
                'deleted_by' => null,
            ]);

            $roleModel->restore();

            $dto = new RoleDTO(
                id: $roleModel->id,
                name: $roleModel->name,
                slug: $roleModel->slug,
                description: $roleModel->description,
                createdAt: (string) $roleModel->created_at
            );

            return response()->json($dto->toArray());
        });
    }
}
