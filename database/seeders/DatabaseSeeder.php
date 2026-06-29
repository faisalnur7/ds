<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            PermissionsSeeder::class,
            RolesAndUsersSeeder::class,
            SettingsSeeder::class,
            ShareSettingsSeeder::class,
            MembersSeeder::class,
            MemberDocumentsSeeder::class,
            PaymentsSeeder::class,
            ProjectsSeeder::class,
            ProjectMembersSeeder::class,
            ProjectIncomesSeeder::class,
            ProfitDistributionsSeeder::class,
            CheckoutRequestsSeeder::class,
            NotificationsSeeder::class,
            AuditLogsSeeder::class,
        ]);
    }
}
