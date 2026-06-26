<?php

namespace Database\Seeders;

use App\Models\CheckoutRequest;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class CheckoutRequestsSeeder extends Seeder
{
    public function run(): void
    {
        $member = Member::query()->firstOrFail();
        $approver = User::query()->where('email', 'admin@example.com')->value('id');

        CheckoutRequest::query()->updateOrCreate(
            ['member_id' => $member->id, 'requested_at' => now()],
            [
                'checkout_type' => 'full',
                'partial_percentage' => null,
                'refundable_amount' => 10000,
                'outstanding_loan_deducted' => 0,
                'status' => 'pending',
                'approved_by' => $approver,
            ]
        );
    }
}
