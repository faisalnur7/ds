<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = array_merge(
            $this->crudPermissions('users', 'Users'),
            $this->crudPermissions('roles', 'Roles'),
            $this->crudPermissions('permissions', 'Permissions'),
            $this->crudPermissions('settings', 'Settings'),
            $this->crudPermissions('share_settings', 'Share Settings'),
            $this->crudPermissions('members', 'Members'),
            $this->crudPermissions('member_documents', 'Member Documents'),
            $this->crudPermissions('payments', 'Payments'),
            $this->crudPermissions('projects', 'Projects'),
            $this->crudPermissions('project_members', 'Project Members'),
            $this->crudPermissions('project_incomes', 'Project Incomes'),
            $this->crudPermissions('profit_distributions', 'Profit Distributions'),
            $this->crudPermissions('checkout_requests', 'Checkout Requests'),
            $this->crudPermissions('expense_categories', 'Expense Categories'),
            $this->crudPermissions('expenses', 'Expenses'),
            [
                ['name' => 'View dashboard', 'slug' => 'view_dashboard', 'group_name' => 'dashboard'],
                ['name' => 'View payment history', 'slug' => 'view_payment_history', 'group_name' => 'payments'],
                ['name' => 'View audit logs', 'slug' => 'view_audit_logs', 'group_name' => 'compliance'],
                ['name' => 'View expense menu', 'slug' => 'view_expense_menu', 'group_name' => 'expenses'],
                ['name' => 'Approve expenses', 'slug' => 'approve_expenses', 'group_name' => 'expenses'],
            ],
        );

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }

        $map = [
            'super_admin' => array_column($permissions, 'slug'),
            'admin' => array_column($permissions, 'slug'),
            'cashier' => [
                'view_dashboard',
                'view_payments',
                'create_payments',
                'edit_payments',
                'update_payments',
                'view_expense_categories',
                'create_expense_categories',
                'edit_expense_categories',
                'update_expense_categories',
                'delete_expense_categories',
                'view_expenses',
                'create_expenses',
                'edit_expenses',
                'update_expenses',
                'delete_expenses',
                'approve_expenses',
                'view_payment_history',
            ],
            'auditor' => ['view_dashboard', 'view_audit_logs'],
            'member' => ['view_dashboard', 'view_payment_history'],
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

    /**
     * @return array<int, array{name: string, slug: string, group_name: string}>
     */
    private function crudPermissions(string $subject, string $label): array
    {
        return [
            ['name' => "View {$label}", 'slug' => "view_{$subject}", 'group_name' => $subject],
            ['name' => "Create {$label}", 'slug' => "create_{$subject}", 'group_name' => $subject],
            ['name' => "Edit {$label}", 'slug' => "edit_{$subject}", 'group_name' => $subject],
            ['name' => "Update {$label}", 'slug' => "update_{$subject}", 'group_name' => $subject],
            ['name' => "Delete {$label}", 'slug' => "delete_{$subject}", 'group_name' => $subject],
        ];
    }
}
