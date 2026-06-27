<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        $phone = fake()->numerify('880170#######');

        return [
            'user_id' => User::factory(),
            'member_code' => 'DS-'.fake()->unique()->numberBetween(1000, 9999),
            'full_name' => fake()->name(),
            'father_name' => fake()->name('male'),
            'mother_name' => fake()->name('female'),
            'spouse_name' => fake()->name(),
            'spouse_phone' => fake()->numerify('880170#######'),
            'phone' => $phone,
            'phone_search' => $phone,
            'nid_number' => fake()->numerify('############'),
            'date_of_birth' => fake()->date(),
            'occupation' => fake()->jobTitle(),
            'address' => fake()->address(),
            'present_address' => fake()->address(),
            'permanent_address' => fake()->address(),
            'nominee_name' => fake()->name(),
            'nominee_relation' => 'Brother',
            'nominee_phone' => fake()->numerify('880170#######'),
            'reference_name' => fake()->name(),
            'reference_phone' => fake()->numerify('880170#######'),
            'remarks' => fake()->sentence(),
            'join_date' => now()->subMonths(3)->toDateString(),
            'membership_status' => 'active',
            'checkout_eligible_after_months' => 12,
        ];
    }
}
