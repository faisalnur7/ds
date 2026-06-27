<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super_admin', 'description' => 'Full system access', 'is_system' => true],
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Operations management', 'is_system' => true],
            ['name' => 'Cashier', 'slug' => 'cashier', 'description' => 'Collection and payment entry', 'is_system' => true],
            ['name' => 'Auditor', 'slug' => 'auditor', 'description' => 'Read-only compliance role', 'is_system' => true],
            ['name' => 'Member', 'slug' => 'member', 'description' => 'Self-service member access', 'is_system' => true],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
