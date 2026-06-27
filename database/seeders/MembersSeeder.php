<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class MembersSeeder extends Seeder
{
    public function run(): void
    {
        $memberUser = User::query()->where('email', 'member@example.com')->firstOrFail();

        Member::query()->updateOrCreate(
            ['user_id' => $memberUser->id],
            [
                'member_code' => 'DS-0001',
                'full_name' => $memberUser->name,
                'father_name' => 'Father Name',
                'mother_name' => 'Mother Name',
                'spouse_name' => 'Spouse Name',
                'spouse_phone' => '+8801700000003',
                'phone_search' => '8801700000001',
                'blood_group' => 'B+',
                'religion' => 'Islam',
                'education' => 'Graduate',
                'emergency_contact_name' => 'Emergency Contact',
                'emergency_contact_phone' => '+8801700000004',
                'phone' => '+8801700000001',
                'nid_number' => '1234567890',
                'date_of_birth' => '1990-01-01',
                'occupation' => 'Business',
                'address' => 'Dhaka',
                'present_address' => 'Dhaka',
                'permanent_address' => 'Dhaka',
                'nominee_name' => 'Nominee One',
                'nominee_relation' => 'Spouse',
                'nominee_phone' => '+8801700000002',
                'reference_name' => 'Reference One',
                'reference_phone' => '+8801700000005',
                'remarks' => 'Seeded member profile',
                'join_date' => now()->toDateString(),
                'membership_status' => 'active',
                'checkout_eligible_after_months' => 12,
            ]
        );
    }
}
