<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator role',
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'User role',
            ],
            [
                'name' => 'Guest',
                'slug' => 'guest',
                'description' => 'Guest role',
            ],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['slug' => $role['slug']],
                [
                    'name' => $role['name'],
                    'description' => $role['description'],
                    'created_by' => null,
                ]
            );
        }
    }
}
