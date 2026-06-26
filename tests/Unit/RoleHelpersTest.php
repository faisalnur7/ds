<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleHelpersTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_helpers_match_expected_access(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();
        $auditor = User::factory()->create(['role' => 'auditor']);
        $member = User::factory()->create();

        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($auditor->isAuditor());
        $this->assertTrue($member->isMember());
    }
}
