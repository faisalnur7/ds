<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\LoanRepayment;
use Illuminate\Database\Seeder;

class LoanRepaymentsSeeder extends Seeder
{
    public function run(): void
    {
        $loan = Loan::query()->firstOrFail();

        LoanRepayment::query()->updateOrCreate(
            ['loan_id' => $loan->id, 'due_date' => now()->addMonth()->toDateString()],
            [
                'amount_due' => 833.33,
                'amount_paid' => 0,
                'late_fee' => 0,
                'status' => 'pending',
            ]
        );
    }
}
