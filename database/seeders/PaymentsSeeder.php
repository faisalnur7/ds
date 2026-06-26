<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentsSeeder extends Seeder
{
    public function run(): void
    {
        $member = Member::query()->firstOrFail();

        Payment::query()->updateOrCreate(
            ['member_id' => $member->id, 'payment_month' => now()->startOfMonth()->toDateString()],
            [
                'due_date' => now()->startOfMonth()->addDays(10)->toDateString(),
                'share_value' => 1000,
                'share_cost' => 50,
                'fine_amount' => 0,
                'is_late' => false,
                'total_amount' => 1050,
                'amount_paid' => 1050,
                'payment_status_detail' => 'full',
                'payment_method' => 'cash',
                'transaction_no' => 'PAY-0001',
                'status' => 'approved',
                'receipt_no' => 'RCPT-0001',
                'approved_at' => now(),
            ]
        );
    }
}
