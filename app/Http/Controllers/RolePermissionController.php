<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AttachRolePermissionRequest;
use App\Http\Requests\DetachRolePermissionRequest;
use App\Models\PermissionRole;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RolePermissionController extends Controller
{
    public function attach(AttachRolePermissionRequest $request, int $role): JsonResponse
    {
        $roleModel = Role::query()->findOrFail($role);

        $permissionId = $request->permissionId();

        PermissionRole::create([
            'role_id' => $roleModel->id,
            'permission_id' => $permissionId,
            'created_at' => now(),
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Permission attached to role'
        ]);
    }

    public function detach(DetachRolePermissionRequest $request, int $role): JsonResponse
    {
        $permissionId = $request->permissionId();

        $relation = PermissionRole::query()
            ->where('role_id', $role)
            ->where('permission_id', $permissionId)
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
}
