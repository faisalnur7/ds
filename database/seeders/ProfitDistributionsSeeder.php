<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Project;
use App\Models\ProfitDistribution;
use Illuminate\Database\Seeder;

class ProfitDistributionsSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::query()->firstOrFail();
        $member = Member::query()->firstOrFail();

        ProfitDistribution::query()->updateOrCreate(
            ['reference_no' => 'PROFIT-0001'],
            [
                'project_id' => $project->id,
                'member_id' => $member->id,
                'profit_amount' => 275,
                'distribution_date' => now()->toDateString(),
            ]
        );
    }
}
