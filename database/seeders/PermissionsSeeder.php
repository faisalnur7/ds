<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'View dashboard', 'slug' => 'view_dashboard', 'group_name' => 'dashboard'],
            ['name' => 'Manage members', 'slug' => 'manage_members', 'group_name' => 'members'],
            ['name' => 'Manage payments', 'slug' => 'manage_payments', 'group_name' => 'payments'],
            ['name' => 'Manage projects', 'slug' => 'manage_projects', 'group_name' => 'projects'],
            ['name' => 'Manage profits', 'slug' => 'manage_profits', 'group_name' => 'profits'],
            ['name' => 'Manage loans', 'slug' => 'manage_loans', 'group_name' => 'loans'],
            ['name' => 'Manage checkout', 'slug' => 'manage_checkout', 'group_name' => 'checkout'],
            ['name' => 'Manage settings', 'slug' => 'manage_settings', 'group_name' => 'settings'],
            ['name' => 'View audit logs', 'slug' => 'view_audit_logs', 'group_name' => 'compliance'],
            ['name' => 'Manage roles', 'slug' => 'manage_roles', 'group_name' => 'access'],
            ['name' => 'Manage permissions', 'slug' => 'manage_permissions', 'group_name' => 'access'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        $map = [
            'super_admin' => array_column($permissions, 'slug'),
            'admin' => [
                'view_dashboard', 'manage_members', 'manage_payments', 'manage_projects',
                'manage_profits', 'manage_loans', 'manage_checkout', 'manage_settings',
                'view_audit_logs',
            ],
            'cashier' => ['view_dashboard', 'manage_payments'],
            'auditor' => ['view_dashboard', 'view_audit_logs'],
            'member' => ['view_dashboard'],
        ];

        foreach ($map as $roleSlug => $permissionSlugs) {
            $role = Role::query()->where('slug', $roleSlug)->first();
            if (! $role) {
                continue;
            }

            $permissionIds = Permission::query()->whereIn('slug', $permissionSlugs)->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }
}
