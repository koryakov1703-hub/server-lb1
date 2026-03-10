<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $entities = ['user', 'role', 'permission'];
        $actions = ['get-list', 'read', 'create', 'update', 'delete', 'restore'];

        foreach ($entities as $entity) {
            foreach ($actions as $action) {
                $slug = $action . '-' . $entity;

                Permission::query()->updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $slug,
                        'description' => $slug,
                        'created_by' => null,
                    ]
                );
            }
        }
    }
}
