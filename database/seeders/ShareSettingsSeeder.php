<?php

namespace Database\Seeders;

use App\Models\ShareSetting;
use Illuminate\Database\Seeder;

class ShareSettingsSeeder extends Seeder
{
    public function run(): void
    {
        ShareSetting::query()->updateOrCreate(
            ['effective_from' => now()->toDateString()],
            [
                'share_value' => 1000,
                'share_cost' => 50,
                'fine_amount' => 20,
                'fine_percent' => 0,
                'is_active' => true,
            ]
        );
    }
}
