<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoansSeeder extends Seeder
{
    public function run(): void
    {
        $member = Member::query()->firstOrFail();
        $approver = User::query()->where('email', 'admin@example.com')->value('id');

        Loan::query()->updateOrCreate(
            ['member_id' => $member->id, 'principal_amount' => 5000, 'tenure_months' => 6],
            [
                'status' => 'approved',
                'approved_by' => $approver,
                'disbursed_at' => now(),
            ]
        );
    }
}
