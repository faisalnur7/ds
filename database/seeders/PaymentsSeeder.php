<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Payment;
use App\Models\ShareSetting;
use Illuminate\Database\Seeder;

class PaymentsSeeder extends Seeder
{
    public function run(): void
    {
        $member = Member::query()->firstOrFail();
        $shareSetting = ShareSetting::current();

        Payment::query()->updateOrCreate(
            ['member_id' => $member->id, 'payment_month' => now()->startOfMonth()->toDateString()],
            [
                'share_value' => $shareSetting?->share_value ?? 0,
                'share_cost' => $shareSetting?->share_cost ?? 0,
                'is_late' => false,
                'total_amount' => $shareSetting ? ($shareSetting->share_value + $shareSetting->share_cost) : 0,
                'amount_paid' => $shareSetting ? ($shareSetting->share_value + $shareSetting->share_cost) : 0,
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
