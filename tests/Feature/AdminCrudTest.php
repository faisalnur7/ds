<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_roles_and_permissions(): void
    {
        $admin = User::factory()->admin()->create();
        $role = Role::create([
            'name' => 'Supervisor',
            'slug' => 'supervisor',
            'description' => 'Test role',
            'is_system' => false,
        ]);
        $permission = Permission::create([
            'name' => 'Manage test data',
            'slug' => 'manage_test_data',
            'group_name' => 'testing',
        ]);

        $this->actingAs($admin)->get(route('admin.roles.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.roles.show', $role))->assertOk();
        $this->actingAs($admin)->get(route('admin.permissions.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.permissions.show', $permission))->assertOk();
    }

    public function test_admin_can_open_payment_and_member_crud_pages(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.members.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.members.create'))->assertOk();
        $this->actingAs($admin)->get(route('admin.payments.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.payments.create'))->assertOk();
    }
}
