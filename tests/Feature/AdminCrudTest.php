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

    public function test_member_crud_accepts_optional_information(): void
    {
        $admin = User::factory()->admin()->create();
        $linkedUser = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($admin)->post(route('admin.members.store'), [
            'user_id' => $linkedUser->id,
            'member_code' => 'DS-0099',
            'full_name' => 'Optional Info Member',
            'father_name' => 'Father',
            'mother_name' => 'Mother',
            'spouse_name' => 'Spouse',
            'spouse_phone' => '+8801700000099',
            'blood_group' => 'A+',
            'religion' => 'Islam',
            'education' => 'Masters',
            'emergency_contact_name' => 'Emergency Person',
            'emergency_contact_phone' => '+8801700000098',
            'phone' => '+8801700000097',
            'nid_number' => '9988776655',
            'date_of_birth' => '1995-05-05',
            'occupation' => 'Teacher',
            'address' => 'House Address',
            'present_address' => 'Present Address',
            'permanent_address' => 'Permanent Address',
            'nominee_name' => 'Nominee',
            'nominee_relation' => 'Brother',
            'nominee_phone' => '+8801700000096',
            'reference_name' => 'Reference',
            'reference_phone' => '+8801700000095',
            'remarks' => 'Optional details recorded',
            'join_date' => now()->toDateString(),
            'membership_status' => 'active',
            'checkout_eligible_after_months' => 12,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'member_code' => 'DS-0099',
            'full_name' => 'Optional Info Member',
        ]);
    }
}
