<?php

namespace Database\Seeders;

use App\Models\ShareSetting;
use Illuminate\Database\Seeder;

class ShareSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $setting = ShareSetting::query()->updateOrCreate(
            ['effective_from' => now()->toDateString()],
            [
                'share_value' => 1000,
                'share_cost' => 50,
                'fine_amount' => 20,
                'is_active' => true,
            ]
        );

        ShareSetting::query()
            ->whereKeyNot($setting->getKey())
            ->update(['is_active' => false]);

        $setting->forceFill(['is_active' => true])->save();
    }
}
