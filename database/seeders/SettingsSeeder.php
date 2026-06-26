<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use App\Services\SettingsService;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()->where('email', 'admin@example.com')->value('id');
        $settings = app(SettingsService::class);

        $settings->put('auto_approve_payments', false, 'bool', $adminId);
        $settings->put('checkout_eligible_months', 12, 'int', $adminId);
        $settings->put('loan_max_percent_of_share', 80, 'int', $adminId);
        $settings->put('email_enabled', true, 'bool', $adminId);

        Setting::query()->updateOrCreate(
            ['key' => 'notification_channels'],
            [
                'value' => json_encode(['email' => true, 'in_app' => true], JSON_THROW_ON_ERROR),
                'value_type' => 'json',
                'updated_by' => $adminId,
            ]
        );
    }
}
