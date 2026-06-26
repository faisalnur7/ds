<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'member@example.com')->firstOrFail();

        DB::table('notifications')->updateOrInsert(
            ['id' => (string) Str::uuid()],
            [
                'type' => 'database',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => 'Welcome to CCIMS',
                    'message' => 'Your membership profile is ready.',
                ], JSON_THROW_ON_ERROR),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
