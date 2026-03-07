<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\PermissionDTO;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
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

    public function store(StorePermissionRequest $request): JsonResponse
    {
        $dto = $request->toDTO();

        $permission = Permission::create([
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
    }

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

    public function update(UpdatePermissionRequest $request, int $permission): JsonResponse
    {
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
    }

    public function destroy(Request $request, int $permission): JsonResponse
    {
        $permissionModel = Permission::query()
            ->whereNull('deleted_at')
            ->findOrFail($permission);

        $permissionModel->update([
            'deleted_by' => $request->user()->id,
        ]);

        $permissionModel->delete();

        return response()->json([
            'message' => 'Permission deleted'
        ]);
    }
}
