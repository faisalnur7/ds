<?php

namespace Tests\Feature\Auth;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'full_name' => 'Test User',
            'father_name' => 'Father Name',
            'mother_name' => 'Mother Name',
            'spouse_name' => 'Spouse Name',
            'spouse_phone' => '+8801700000901',
            'blood_group' => 'B+',
            'religion' => 'Islam',
            'education' => 'Graduate',
            'emergency_contact_name' => 'Emergency Contact',
            'emergency_contact_phone' => '+8801700000902',
            'phone' => '+8801700000903',
            'nid_number' => '1234567890123',
            'date_of_birth' => '1990-01-01',
            'occupation' => 'Business',
            'present_address' => 'Present Address',
            'permanent_address' => 'Permanent Address',
            'nominee_name' => 'Nominee One',
            'nominee_relation' => 'Brother',
            'nominee_phone' => '+8801700000904',
            'reference_name' => 'Reference One',
            'reference_phone' => '+8801700000905',
            'remarks' => 'Test registration',
            'join_date' => '2026-06-28',
            'share_number' => 2,
            'membership_status' => 'active',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::query()->where('email', 'test@example.com')->firstOrFail();
        $member = Member::query()->where('user_id', $user->id)->firstOrFail();

        $this->assertSame('Test User', $user->name);
        $this->assertMatchesRegularExpression('/^DS-\d{4}$/', $member->member_code);
        $this->assertSame('Test User', $member->full_name);
        $this->assertSame('8801700000903', $member->phone_search);
    }
}
