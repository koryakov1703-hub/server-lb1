<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AttachUserRoleRequest;
use App\Http\Requests\DetachUserRoleRequest;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function attach(AttachUserRoleRequest $request, int $user): JsonResponse
    {
        $userModel = User::query()->findOrFail($user);

        $roleId = $request->roleId();

        UserRole::create([
            'user_id' => $userModel->id,
            'role_id' => $roleId,
            'created_at' => now(),
            'created_by' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Role attached to user'
        ]);
    }

    public function detach(DetachUserRoleRequest $request, int $user): JsonResponse
    {
        $roleId = $request->roleId();

        $relation = UserRole::query()
            ->where('user_id', $user)
            ->where('role_id', $roleId)
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
}
