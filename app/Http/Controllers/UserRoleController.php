<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\RoleDTO;
use App\Http\Requests\AttachUserRoleRequest;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function users(): JsonResponse
    {
        $users = User::query()
            ->select(['id', 'name', 'email', 'birthday'])
            ->get();

        return response()->json($users);
    }

    public function index(int $user): JsonResponse
    {
        $userModel = User::query()
            ->with(['roles'])
            ->findOrFail($user);

        $result = $userModel->roles->map(function ($role) {
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

    public function attach(AttachUserRoleRequest $request, int $user): JsonResponse
    {
        $userModel = User::query()->findOrFail($user);

        UserRole::create([
            'user_id' => $userModel->id,
            'role_id' => $request->roleId(),
            'created_at' => now(),
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Role attached to user'
        ], 201);
    }

    public function destroy(int $user, int $role): JsonResponse
    {
        $relation = UserRole::withTrashed()
            ->where('user_id', $user)
            ->where('role_id', $role)
            ->firstOrFail();

        $relation->forceDelete();

        return response()->json([
            'message' => 'Role detached from user permanently'
        ]);
    }

    public function softDelete(Request $request, int $user, int $role): JsonResponse
    {
        $relation = UserRole::query()
            ->where('user_id', $user)
            ->where('role_id', $role)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $relation->update([
            'deleted_by' => $request->user()->id,
        ]);

        $relation->delete();

        return response()->json([
            'message' => 'Role detached from user'
        ]);
    }

    public function restore(int $user, int $role): JsonResponse
    {
        $relation = UserRole::withTrashed()
            ->where('user_id', $user)
            ->where('role_id', $role)
            ->whereNotNull('deleted_at')
            ->firstOrFail();

        $relation->update([
            'deleted_by' => null,
        ]);

        $relation->restore();

        return response()->json([
            'message' => 'Role restored for user'
        ]);
    }
}
