<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectIncome;
use Illuminate\Database\Seeder;

class ProjectIncomesSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::query()->firstOrFail();

        ProjectIncome::query()->updateOrCreate(
            ['project_id' => $project->id, 'income_date' => now()->toDateString(), 'income_type' => 'monthly'],
            [
                'amount' => 2500,
                'remarks' => 'Seeded income',
            ]
        );
    }
}
