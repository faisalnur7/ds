<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Setting;
use App\Models\Permission;
use App\Models\Payment;
use App\Models\Role;
use App\Models\ShareSetting;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

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
        $this->actingAs($admin)
            ->get(route('admin.members.create'))
            ->assertOk()
            ->assertSee('Create a new member')
            ->assertSee('Email')
            ->assertSee('Password')
            ->assertSee('Confirm Password');
        $this->actingAs($admin)->get(route('admin.payments.index'))->assertOk();
        $this->actingAs($admin)
            ->get(route('admin.payments.create'))
            ->assertOk()
            ->assertDontSee('Transaction No')
            ->assertDontSee('Receipt No');
    }

    public function test_admin_can_view_settings_as_cards(): void
    {
        $admin = User::factory()->admin()->create();
        $settings = app(SettingsService::class);

        $settings->put('auto_approve_payments', false, 'bool');
        $settings->put('checkout_eligible_months', 12, 'int');
        $settings->put('loan_max_percent_of_share', 80, 'int');
        $settings->put('email_enabled', true, 'bool');
        $settings->put('notification_channels', true, 'bool');

        $this->actingAs($admin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Auto approve payments')
            ->assertSee('Checkout eligibility window')
            ->assertSee('Maximum loan percentage')
            ->assertSee('Email notifications')
            ->assertSee('Notification channels')
            ->assertSee('Save changes');
    }

    public function test_admin_can_update_settings_from_individual_cards(): void
    {
        $admin = User::factory()->admin()->create();
        $settings = app(SettingsService::class);

        $settings->put('auto_approve_payments', false, 'bool');
        $settings->put('checkout_eligible_months', 12, 'int');
        $settings->put('loan_max_percent_of_share', 80, 'int');
        $settings->put('email_enabled', true, 'bool');
        $settings->put('notification_channels', true, 'bool');

        $autoApprove = Setting::query()->where('key', 'auto_approve_payments')->firstOrFail();
        $checkoutMonths = Setting::query()->where('key', 'checkout_eligible_months')->firstOrFail();
        $loanPercent = Setting::query()->where('key', 'loan_max_percent_of_share')->firstOrFail();
        $emailEnabled = Setting::query()->where('key', 'email_enabled')->firstOrFail();
        $notificationChannels = Setting::query()->where('key', 'notification_channels')->firstOrFail();

        $this->actingAs($admin)
            ->patch(route('admin.settings.update', $autoApprove), [
                'setting_key' => 'auto_approve_payments',
                'value' => '1',
            ])
            ->assertRedirect(route('admin.settings.index'));

        $this->actingAs($admin)
            ->patch(route('admin.settings.update', $checkoutMonths), [
                'setting_key' => 'checkout_eligible_months',
                'value' => 18,
            ])
            ->assertRedirect(route('admin.settings.index'));

        $this->actingAs($admin)
            ->patch(route('admin.settings.update', $loanPercent), [
                'setting_key' => 'loan_max_percent_of_share',
                'value' => 90,
            ])
            ->assertRedirect(route('admin.settings.index'));

        $this->actingAs($admin)
            ->patch(route('admin.settings.update', $emailEnabled), [
                'setting_key' => 'email_enabled',
                'value' => '0',
            ])
            ->assertRedirect(route('admin.settings.index'));

        $this->actingAs($admin)
            ->patch(route('admin.settings.update', $notificationChannels), [
                'setting_key' => 'notification_channels',
                'value' => '0',
            ])
            ->assertRedirect(route('admin.settings.index'));

        $this->assertSame('1', Setting::query()->where('key', 'auto_approve_payments')->value('value'));
        $this->assertSame('18', Setting::query()->where('key', 'checkout_eligible_months')->value('value'));
        $this->assertSame('90', Setting::query()->where('key', 'loan_max_percent_of_share')->value('value'));
        $this->assertSame('0', Setting::query()->where('key', 'email_enabled')->value('value'));
        $this->assertSame('0', Setting::query()->where('key', 'notification_channels')->value('value'));
    }

    public function test_project_show_page_lists_member_investments(): void
    {
        $admin = User::factory()->admin()->create();
        $project = Project::query()->create([
            'name' => 'River House',
            'description' => 'Test project',
            'invested_amount' => 50000,
            'start_date' => '2026-06-01',
            'status' => 'active',
        ]);
        $memberA = Member::factory()->create(['full_name' => 'Alpha Member', 'member_code' => 'DS-2001']);
        $memberB = Member::factory()->create(['full_name' => 'Beta Member', 'member_code' => 'DS-2002']);

        ProjectMember::query()->create([
            'project_id' => $project->id,
            'member_id' => $memberA->id,
            'allocated_share_amount' => 12000,
            'is_active' => true,
        ]);
        ProjectMember::query()->create([
            'project_id' => $project->id,
            'member_id' => $memberB->id,
            'allocated_share_amount' => 18000,
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.projects.show', $project))
            ->assertOk()
            ->assertSee('Member investments')
            ->assertSee('Alpha Member')
            ->assertSee('Beta Member')
            ->assertSee('12,000.00')
            ->assertSee('18,000.00')
            ->assertSee('30,000.00')
            ->assertSee('20,000.00');
    }

    public function test_admin_can_version_share_settings_and_payment_defaults_follow_active_setting(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.share-settings.store'), [
            'share_value' => 1000,
            'share_cost' => 50,
            'fine_amount' => 20,
            'fine_percent' => 0,
            'effective_from' => '2026-06-01',
            'is_active' => true,
        ])->assertRedirect();

        $firstSetting = ShareSetting::query()->where('share_value', 1000)->firstOrFail();
        $this->assertTrue($firstSetting->is_active);

        $this->actingAs($admin)->post(route('admin.share-settings.store'), [
            'share_value' => 1500,
            'share_cost' => 50,
            'fine_amount' => 25,
            'fine_percent' => 0,
            'effective_from' => '2026-07-01',
            'is_active' => true,
        ])->assertRedirect();

        $firstSetting->refresh();
        $secondSetting = ShareSetting::query()->where('share_value', 1500)->firstOrFail();

        $this->assertFalse($firstSetting->is_active);
        $this->assertTrue($secondSetting->is_active);

        $this->actingAs($admin)
            ->get(route('admin.payments.create'))
            ->assertOk()
            ->assertSee('1500.00')
            ->assertSee('50.00')
            ->assertSee('25.00');
    }

    public function test_settings_page_renders_checkout_months_setting_as_a_number(): void
    {
        $admin = User::factory()->admin()->create();
        app(SettingsService::class)->put('checkout_eligible_months', 10, 'int', $admin->id);

        $this->actingAs($admin)
            ->get(route('admin.settings.index'))
            ->assertOk()
            ->assertSee('Checkout eligibility window')
            ->assertSee('id="setting-checkout_eligible_months"', false)
            ->assertSee('type="number"', false);
    }

    public function test_share_setting_toggle_can_activate_and_deactivate_records(): void
    {
        $admin = User::factory()->admin()->create();
        $activeSetting = ShareSetting::query()->create([
            'share_value' => 1000,
            'share_cost' => 50,
            'fine_amount' => 20,
            'fine_percent' => 0,
            'effective_from' => '2026-06-01',
            'is_active' => true,
        ]);
        $inactiveSetting = ShareSetting::query()->create([
            'share_value' => 1500,
            'share_cost' => 75,
            'fine_amount' => 25,
            'fine_percent' => 0,
            'effective_from' => '2026-07-01',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.share-settings.toggle', $activeSetting))
            ->assertRedirect(route('admin.share-settings.index'));

        $activeSetting->refresh();
        $this->assertFalse($activeSetting->is_active);

        $this->actingAs($admin)
            ->patch(route('admin.share-settings.toggle', $inactiveSetting))
            ->assertRedirect(route('admin.share-settings.index'));

        $activeSetting->refresh();
        $inactiveSetting->refresh();

        $this->assertFalse($activeSetting->is_active);
        $this->assertTrue($inactiveSetting->is_active);
    }

    public function test_member_crud_accepts_optional_information(): void
    {
        $admin = User::factory()->admin()->create();
        app(SettingsService::class)->put('checkout_eligible_months', 12, 'int');

        $response = $this->actingAs($admin)->post(route('admin.members.store'), [
            'email' => 'optional.member@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
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
            'present_address' => 'Present Address',
            'permanent_address' => 'Permanent Address',
            'nominee_name' => 'Nominee',
            'nominee_relation' => 'Brother',
            'nominee_phone' => '+8801700000096',
            'reference_name' => 'Reference',
            'reference_phone' => '+8801700000095',
            'remarks' => 'Optional details recorded',
            'join_date' => now()->toDateString(),
            'share_number' => 1,
            'membership_status' => 'active',
        ]);

        $response->assertRedirect();
        $user = User::query()->where('email', 'optional.member@example.com')->firstOrFail();
        $this->assertSame('Optional Info Member', $user->name);
        $this->assertDatabaseHas('members', [
            'full_name' => 'Optional Info Member',
            'user_id' => $user->id,
        ]);
        $this->assertMatchesRegularExpression(
            '/^DS-\d{4}$/',
            (string) Member::query()->where('user_id', $user->id)->firstOrFail()->member_code
        );
    }

    public function test_member_share_number_is_capped_by_settings_value(): void
    {
        $admin = User::factory()->admin()->create();

        app(SettingsService::class)->put('maximum_share_per_member', 8, 'int');

        $this->actingAs($admin)
            ->post(route('admin.members.store'), [
                'email' => 'over.limit@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'full_name' => 'Over Limit Member',
                'phone' => '+8801700000108',
                'nid_number' => '1081081081',
                'join_date' => now()->toDateString(),
                'share_number' => 9,
                'membership_status' => 'active',
            ])
            ->assertSessionHasErrors('share_number');
    }

    public function test_member_checkout_eligibility_uses_global_setting(): void
    {
        $admin = User::factory()->admin()->create();
        $member = \App\Models\Member::factory()->create([
            'join_date' => '2026-01-15',
        ]);

        app(SettingsService::class)->put('checkout_eligible_months', 6, 'int');

        $this->actingAs($admin)
            ->get(route('admin.members.index'))
            ->assertOk()
            ->assertSee('Jul 15, 2026');

        $this->assertSame('2026-07-15', optional($member->checkout_eligible_on)->toDateString());
    }

    public function test_member_create_page_does_not_show_member_level_checkout_months_field(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.members.create'))
            ->assertOk()
            ->assertDontSee('Checkout Eligible After Months');
    }

    public function test_member_share_changes_are_recorded_and_rendered_on_the_show_page(): void
    {
        $admin = User::factory()->admin()->create();

        ShareSetting::query()->create([
            'share_value' => 1000,
            'share_cost' => 50,
            'fine_amount' => 20,
            'fine_percent' => 0,
            'effective_from' => '2026-06-01',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.members.store'), [
                'email' => 'history.member@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'full_name' => 'History Member',
                'phone' => '+8801700000110',
                'nid_number' => '1234567890123',
                'join_date' => '2026-06-01',
                'share_number' => 1,
                'membership_status' => 'active',
            ])
            ->assertRedirect();

        $member = Member::query()->where('full_name', 'History Member')->firstOrFail();

        $this->assertDatabaseHas('member_share_histories', [
            'member_id' => $member->id,
            'previous_share_number' => null,
            'share_number' => 1,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.members.update', $member), [
                'user_id' => $member->user_id,
                'member_code' => $member->member_code,
                'full_name' => 'History Member',
                'father_name' => $member->father_name,
                'mother_name' => $member->mother_name,
                'spouse_name' => $member->spouse_name,
                'spouse_phone' => $member->spouse_phone,
                'blood_group' => $member->blood_group,
                'religion' => $member->religion,
                'education' => $member->education,
                'emergency_contact_name' => $member->emergency_contact_name,
                'emergency_contact_phone' => $member->emergency_contact_phone,
                'phone' => '+8801700000110',
                'nid_number' => '1234567890123',
                'date_of_birth' => optional($member->date_of_birth)->toDateString(),
                'occupation' => $member->occupation,
                'present_address' => $member->present_address,
                'permanent_address' => $member->permanent_address,
                'nominee_name' => $member->nominee_name,
                'nominee_relation' => $member->nominee_relation,
                'nominee_phone' => $member->nominee_phone,
                'reference_name' => $member->reference_name,
                'reference_phone' => $member->reference_phone,
                'remarks' => $member->remarks,
                'join_date' => optional($member->join_date)->toDateString(),
                'share_number' => 3,
                'membership_status' => 'active',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('member_share_histories', [
            'member_id' => $member->id,
            'previous_share_number' => 1,
            'share_number' => 3,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.members.show', $member))
            ->assertOk()
            ->assertSee('Share change timeline')
            ->assertSee('1 shares')
            ->assertSee('3 shares');
    }

    public function test_payment_create_form_uses_member_defaults_and_generates_references_on_save(): void
    {
        $admin = User::factory()->admin()->create();
        ShareSetting::query()->create([
            'share_value' => 1000,
            'share_cost' => 50,
            'fine_amount' => 20,
            'fine_percent' => 0,
            'effective_from' => '2026-06-01',
            'is_active' => true,
        ]);
        $member = Member::factory()->create(['share_number' => 5]);

        Payment::query()->create([
            'member_id' => $member->id,
            'payment_month' => now()->subMonth()->startOfMonth()->toDateString(),
            'due_date' => now()->subMonth()->startOfMonth()->addDays(10)->toDateString(),
            'share_value' => 1250,
            'share_cost' => 75,
            'fine_amount' => 30,
            'is_late' => false,
            'total_amount' => 1355,
            'amount_paid' => 1355,
            'payment_status_detail' => 'full',
            'payment_method' => 'cash',
            'transaction_no' => 'PAY-OLD-0001',
            'status' => 'approved',
            'receipt_no' => 'RCPT-OLD-0001',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.payments.store'), [
            'member_id' => $member->id,
            'payment_month' => now()->startOfMonth()->toDateString(),
            'due_date' => now()->startOfMonth()->addDays(10)->toDateString(),
            'amount_paid' => '',
            'payment_method' => 'cash',
            'status' => 'pending',
            'payment_status_detail' => 'full',
        ]);

        $response->assertRedirect();

        $payment = Payment::query()
            ->where('member_id', $member->id)
            ->whereDate('payment_month', now()->startOfMonth())
            ->firstOrFail();

        $this->assertSame('5000.00', (string) $payment->share_value);
        $this->assertSame('250.00', (string) $payment->share_cost);
        $this->assertSame('20.00', (string) $payment->fine_amount);
        $this->assertSame('5270.00', (string) $payment->total_amount);
        $this->assertSame('5270.00', (string) $payment->amount_paid);
        $this->assertNotEmpty($payment->transaction_no);
        $this->assertNotEmpty($payment->receipt_no);
        $this->assertStringStartsWith('PAY-', $payment->transaction_no);
        $this->assertStringStartsWith('RCPT-', $payment->receipt_no);
    }

    public function test_member_index_shows_share_number(): void
    {
        $admin = User::factory()->admin()->create();
        $member = Member::factory()->create([
            'share_number' => 5,
            'member_code' => 'DS-5555',
            'full_name' => 'Share Count Member',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.members.index'))
            ->assertOk()
            ->assertSee('Shares')
            ->assertSee('5 shares')
            ->assertSee('DS-5555')
            ->assertSee('Share Count Member');
    }

    public function test_member_index_supports_search_and_filters(): void
    {
        $admin = User::factory()->admin()->create();
        $jimmadar = User::factory()->create(['name' => 'Jimmadar One']);
        $otherJimmadar = User::factory()->create(['name' => 'Jimmadar Two']);

        $memberA = \App\Models\Member::factory()->create([
            'user_id' => $jimmadar->id,
            'member_code' => 'DS-1001',
            'full_name' => 'Alpha Member',
            'phone' => '+8801700001001',
            'phone_search' => '8801700001001',
            'join_date' => '2026-01-15',
        ]);

        $memberB = \App\Models\Member::factory()->create([
            'user_id' => $otherJimmadar->id,
            'member_code' => 'DS-1002',
            'full_name' => 'Beta Member',
            'phone' => '+8801700001002',
            'phone_search' => '8801700001002',
            'join_date' => '2026-03-20',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.members.index', ['q' => '1001']))
            ->assertOk()
            ->assertSee('DS-1001')
            ->assertDontSee('DS-1002');

        $this->actingAs($admin)
            ->get(route('admin.members.index', ['jimmadar_id' => $jimmadar->id]))
            ->assertOk()
            ->assertSee('Alpha Member')
            ->assertDontSee('Beta Member');

        $this->actingAs($admin)
            ->get(route('admin.members.index', ['join_from' => '2026-03-01', 'join_to' => '2026-03-31']))
            ->assertOk()
            ->assertSee('Beta Member')
            ->assertDontSee('Alpha Member');
    }

    public function test_member_permissions_are_action_specific(): void
    {
        $permission = Permission::create([
            'name' => 'Create Members',
            'slug' => 'create_members',
            'group_name' => 'members',
        ]);

        $role = Role::create([
            'name' => 'Member Creator',
            'slug' => 'member_creator',
            'description' => 'Can open the create member form',
            'is_system' => false,
        ]);
        $role->permissions()->sync([$permission->id]);

        $user = User::factory()->create([
            'role' => $role->slug,
            'is_admin' => true,
        ]);

        $this->actingAs($user)->get(route('admin.members.create'))->assertOk();
        $this->actingAs($user)->get(route('admin.members.index'))->assertForbidden();
    }

    public function test_legacy_manage_members_permission_still_grants_member_access(): void
    {
        $permission = Permission::create([
            'name' => 'Manage members',
            'slug' => 'manage_members',
            'group_name' => 'legacy',
        ]);

        $role = Role::create([
            'name' => 'Legacy Member Manager',
            'slug' => 'legacy_member_manager',
            'description' => 'Compatibility test role',
            'is_system' => false,
        ]);
        $role->permissions()->sync([$permission->id]);

        $user = User::factory()->create([
            'role' => $role->slug,
            'is_admin' => true,
        ]);

        $this->actingAs($user)->get(route('admin.members.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.members.create'))->assertOk();
    }
}
