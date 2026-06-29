<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;

class ProjectController extends CrudController
{
    protected function modelClass(): string
    {
        return Project::class;
    }

    protected function title(): string
    {
        return 'Projects';
    }

    protected function viewPrefix(): string
    {
        return 'projects';
    }

    protected function routeParameter(): string
    {
        return 'project';
    }

    protected function pageDescription(): string
    {
        return 'Investment projects, balances, and operating status.';
    }

    protected function with(): array
    {
        return ['members.member'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Name', 'key' => 'name'],
            ['label' => 'Invested', 'key' => 'invested_amount', 'type' => 'money'],
            ['label' => 'Start', 'key' => 'start_date'],
            ['label' => 'Status', 'key' => 'status'],
        ];
    }

    protected function formFields(?Model $record = null): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'span' => 2],
            ['name' => 'invested_amount', 'label' => 'Invested Amount', 'type' => 'number'],
            ['name' => 'start_date', 'label' => 'Start Date', 'type' => 'date'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'invested_amount' => ['required', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'status' => ['required', 'in:active,completed,cancelled'],
        ];
    }

    protected function showContext(Model $record): array
    {
        $members = $record->members
            ->sortBy(fn ($projectMember) => $projectMember->member?->full_name ?? $projectMember->member?->member_code ?? '')
            ->values();

        $allocatedTotal = $members->sum(fn ($projectMember) => (float) $projectMember->allocated_share_amount);
        $projectAmount = (float) $record->invested_amount;

        $context = [
            'summary' => [
                ['label' => 'Project Investment', 'value' => $projectAmount, 'type' => 'money'],
                ['label' => 'Member Count', 'value' => $members->count(), 'type' => 'number'],
                ['label' => 'Allocated by Members', 'value' => $allocatedTotal, 'type' => 'money'],
                ['label' => 'Unallocated Balance', 'value' => max($projectAmount - $allocatedTotal, 0), 'type' => 'money'],
            ],
            'members' => $members,
        ];

        if ($this->can(request(), 'create')) {
            $context['actions'] = [
                [
                    'label' => 'Input project profit',
                    'href' => route('admin.project-incomes.create', ['project_id' => $record->getKey()]),
                    'buttonClass' => 'bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300',
                ],
                [
                    'label' => 'Disburse profit',
                    'href' => route('admin.profit-distributions.create', ['project_id' => $record->getKey()]),
                    'buttonClass' => 'border border-emerald-300/20 bg-emerald-400/10 px-4 py-2 text-sm font-semibold text-emerald-200 transition hover:bg-emerald-400/20',
                ],
                [
                    'label' => 'Allocate member',
                    'href' => route('admin.project-members.create', ['project_id' => $record->getKey()]),
                    'buttonClass' => 'bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300',
                ],
            ];
        }

        return $context;
    }
}
