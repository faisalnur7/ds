<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Database\Seeder;

class ProjectMembersSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::query()->firstOrFail();
        $member = Member::query()->firstOrFail();

        ProjectMember::query()->updateOrCreate(
            ['project_id' => $project->id, 'member_id' => $member->id],
            [
                'allocated_share_amount' => 10000,
                'is_active' => true,
            ]
        );
    }
}
