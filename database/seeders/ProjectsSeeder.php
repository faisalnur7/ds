<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectsSeeder extends Seeder
{
    public function run(): void
    {
        Project::query()->updateOrCreate(
            ['name' => 'Seeded Investment Project'],
            [
                'description' => 'Seeded demonstration project',
                'invested_amount' => 50000,
                'start_date' => now()->toDateString(),
                'status' => 'active',
            ]
        );
    }
}
