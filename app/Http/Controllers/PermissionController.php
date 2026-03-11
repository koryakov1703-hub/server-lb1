<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\PermissionDTO;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Возвращает список активных разрешений.
     */
    public function index(): JsonResponse
    {
        $permissions = Permission::query()
            ->whereNull('deleted_at')
            ->get();

        $result = $permissions->map(function (Permission $permission) {
            $dto = new PermissionDTO(
                id: $permission->id,
                name: $permission->name,
                slug: $permission->slug,
                description: $permission->description,
                createdAt: (string) $permission->created_at
            );

            return $dto->toArray();
        });

        return response()->json($result->values());
    }

    /**
     * Создаёт новое разрешение.
     */
    public function store(StorePermissionRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request): JsonResponse {
            $dto = $request->toDTO();

            $permission = Permission::query()->create([
                'name' => $dto->name,
                'slug' => $dto->slug,
                'description' => $dto->description,
                'created_by' => $request->user()->id,
            ]);

            $result = new PermissionDTO(
                id: $permission->id,
                name: $permission->name,
                slug: $permission->slug,
                description: $permission->description,
                createdAt: (string) $permission->created_at
            );

            return response()->json($result->toArray(), 201);
        });
    }

    /**
     * Возвращает конкретное разрешение.
     */
    public function show(int $permission): JsonResponse
    {
        $permissionModel = Permission::query()
            ->whereNull('deleted_at')
            ->findOrFail($permission);

        $dto = new PermissionDTO(
            id: $permissionModel->id,
            name: $permissionModel->name,
            slug: $permissionModel->slug,
            description: $permissionModel->description,
            createdAt: (string) $permissionModel->created_at
        );

        return response()->json($dto->toArray());
    }

    /**
     * Обновляет данные разрешения.
     */
    public function update(UpdatePermissionRequest $request, int $permission): JsonResponse
    {
        return DB::transaction(function () use ($request, $permission): JsonResponse {
            $permissionModel = Permission::query()
                ->whereNull('deleted_at')
                ->findOrFail($permission);

            $dto = $request->toDTO();

            $permissionModel->update([
                'name' => $dto->name,
                'slug' => $dto->slug,
                'description' => $dto->description,
            ]);

            $result = new PermissionDTO(
                id: $permissionModel->id,
                name: $permissionModel->name,
                slug: $permissionModel->slug,
                description: $permissionModel->description,
                createdAt: (string) $permissionModel->created_at
            );

            return response()->json($result->toArray());
        });
    }

    /**
     * Выполняет жёсткое удаление разрешения.
     */
    public function destroy(int $permission): JsonResponse
    {
        return DB::transaction(function () use ($permission): JsonResponse {
            $permissionModel = Permission::withTrashed()->findOrFail($permission);

            $permissionModel->forceDelete();

            return response()->json([
                'message' => 'Permission deleted permanently'
            ]);
        });
    }

    /**
     * Выполняет мягкое удаление разрешения.
     */
    public function softDelete(Request $request, int $permission): JsonResponse
    {
        return DB::transaction(function () use ($request, $permission): JsonResponse {
            $permissionModel = Permission::query()
                ->whereNull('deleted_at')
                ->findOrFail($permission);

            $permissionModel->update([
                'deleted_by' => $request->user()->id,
            ]);

            $permissionModel->delete();

            return response()->json([
                'message' => 'Permission soft deleted'
            ]);
        });
    }

    /**
     * Восстанавливает мягко удалённое разрешение.
     */
    public function restore(int $permission): JsonResponse
    {
        return DB::transaction(function () use ($permission): JsonResponse {
            $permissionModel = Permission::withTrashed()->findOrFail($permission);

            $permissionModel->update([
                'deleted_by' => null,
            ]);

            $permissionModel->restore();

            $dto = new PermissionDTO(
                id: $permissionModel->id,
                name: $permissionModel->name,
                slug: $permissionModel->slug,
                description: $permissionModel->description,
                createdAt: (string) $permissionModel->created_at
            );

            return response()->json($dto->toArray());
        });
    }
}
