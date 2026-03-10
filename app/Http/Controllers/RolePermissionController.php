<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\PermissionDTO;
use App\Http\Requests\AttachRolePermissionRequest;
use App\Models\PermissionRole;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index(int $role): JsonResponse
    {
        $roleModel = Role::query()
            ->with(['permissions'])
            ->findOrFail($role);

        $result = $roleModel->permissions->map(function ($permission) {
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

    public function attach(AttachRolePermissionRequest $request, int $role): JsonResponse
    {
        $roleModel = Role::query()->findOrFail($role);

        PermissionRole::create([
            'role_id' => $roleModel->id,
            'permission_id' => $request->permissionId(),
            'created_at' => now(),
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Permission attached to role'
        ], 201);
    }

    public function destroy(int $role, int $permission): JsonResponse
    {
        $relation = PermissionRole::withTrashed()
            ->where('role_id', $role)
            ->where('permission_id', $permission)
            ->firstOrFail();

        $relation->forceDelete();

        return response()->json([
            'message' => 'Permission detached from role permanently'
        ]);
    }

    public function softDelete(Request $request, int $role, int $permission): JsonResponse
    {
        $relation = PermissionRole::query()
            ->where('role_id', $role)
            ->where('permission_id', $permission)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $relation->update([
            'deleted_by' => $request->user()->id,
        ]);

        $relation->delete();

        return response()->json([
            'message' => 'Permission detached from role'
        ]);
    }

    public function restore(int $role, int $permission): JsonResponse
    {
        $relation = PermissionRole::withTrashed()
            ->where('role_id', $role)
            ->where('permission_id', $permission)
            ->whereNotNull('deleted_at')
            ->firstOrFail();

        $relation->update([
            'deleted_by' => null,
        ]);

        $relation->restore();

        return response()->json([
            'message' => 'Permission restored for role'
        ]);
    }
}
