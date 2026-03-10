<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::query()->where('slug', 'admin')->first();
        $user = Role::query()->where('slug', 'user')->first();
        $guest = Role::query()->where('slug', 'guest')->first();

        if (!$admin || !$user || !$guest) {
            return;
        }

        $allPermissions = Permission::query()->get();

        foreach ($allPermissions as $permission) {
            PermissionRole::query()->updateOrCreate(
                [
                    'role_id' => $admin->id,
                    'permission_id' => $permission->id,
                ],
                [
                    'created_at' => now(),
                    'created_by' => null,
                    'deleted_at' => null,
                    'deleted_by' => null,
                ]
            );
        }

        $userPermissions = Permission::query()
            ->whereIn('slug', [
                'get-list-user',
                'read-user',
                'get-list-role',
                'read-role',
                'get-list-permission',
                'read-permission',
            ])
            ->get();

        foreach ($userPermissions as $permission) {
            PermissionRole::query()->updateOrCreate(
                [
                    'role_id' => $user->id,
                    'permission_id' => $permission->id,
                ],
                [
                    'created_at' => now(),
                    'created_by' => null,
                    'deleted_at' => null,
                    'deleted_by' => null,
                ]
            );
        }

        $guestPermissions = Permission::query()
            ->whereIn('slug', [
                'get-list-role',
                'read-role',
                'get-list-permission',
                'read-permission',
            ])
            ->get();

        foreach ($guestPermissions as $permission) {
            PermissionRole::query()->updateOrCreate(
                [
                    'role_id' => $guest->id,
                    'permission_id' => $permission->id,
                ],
                [
                    'created_at' => now(),
                    'created_by' => null,
                    'deleted_at' => null,
                    'deleted_by' => null,
                ]
            );
        }
    }
}
