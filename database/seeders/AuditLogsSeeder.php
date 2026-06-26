<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditLogsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

        AuditLog::query()->updateOrCreate(
            [
                'action' => 'seed.completed',
                'auditable_type' => User::class,
                'auditable_id' => $admin->id,
            ],
            [
                'user_id' => $admin->id,
                'old_values' => null,
                'new_values' => ['seeded' => true],
                'ip_address' => '127.0.0.1',
                'created_at' => now(),
            ]
        );
    }
}
