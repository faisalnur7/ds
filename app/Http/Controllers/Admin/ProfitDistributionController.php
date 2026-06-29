<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\Project;
use App\Models\ProfitDistribution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

class ProfitDistributionController extends CrudController
{
    protected function modelClass(): string
    {
        return ProfitDistribution::class;
    }

    protected function title(): string
    {
        return 'Profit Distributions';
    }

    protected function viewPrefix(): string
    {
        return 'profit-distributions';
    }

    protected function routeParameter(): string
    {
        return 'profit_distribution';
    }

    protected function pageDescription(): string
    {
        return 'Member profit payouts and reference records.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Project', 'key' => 'project.name'],
            ['label' => 'Member', 'key' => 'member.member_code'],
            ['label' => 'Profit', 'key' => 'profit_amount', 'type' => 'money'],
            ['label' => 'Date', 'key' => 'distribution_date'],
        ];
    }

    protected function with(): array
    {
        return ['project', 'member'];
    }

    public function create(): View
    {
        $request = request();
        $this->requirePermission($request, 'create');

        $model = $this->modelClass();
        $record = new $model();

        if ($projectId = $request->integer('project_id')) {
            $record->project_id = $projectId;
        }

        return view('admin.crud.form', [
            'title' => $this->title(),
            'description' => $this->pageDescription(),
            'fields' => $this->formFields($record),
            'record' => $record,
            'routePrefix' => $this->viewPrefix(),
            'action' => route("admin.{$this->viewPrefix()}.store"),
            'method' => 'POST',
            'submitLabel' => 'Create',
            'canSubmit' => $this->can($request, 'create'),
            'formContainerClass' => $this->formContainerClass(),
        ]);
    }

    protected function formFields(?Model $record = null): array
    {
        $projectOptions = Project::query()->pluck('name', 'id')->all();
        $memberOptions = Member::query()->pluck('member_code', 'id')->all();

        if ($record?->project_id) {
            $project = Project::query()->with(['members.member'])->find($record->project_id);

            if ($project) {
                $memberOptions = $project->members
                    ->sortBy(fn ($projectMember) => $projectMember->member?->full_name ?? $projectMember->member?->member_code ?? '')
                    ->mapWithKeys(fn ($projectMember) => [
                        $projectMember->member_id => $projectMember->member?->full_name
                            ? ($projectMember->member?->full_name.' ('.$projectMember->member?->member_code.')')
                            : ($projectMember->member?->member_code ?? (string) $projectMember->member_id),
                    ])
                    ->all();
            }
        }

        return [
            ['name' => 'project_id', 'label' => 'Project', 'type' => 'select', 'options' => $projectOptions],
            ['name' => 'member_id', 'label' => 'Member', 'type' => 'select', 'options' => $memberOptions],
            ['name' => 'profit_amount', 'label' => 'Profit Amount', 'type' => 'number'],
            ['name' => 'distribution_date', 'label' => 'Distribution Date', 'type' => 'date'],
            ['name' => 'reference_no', 'label' => 'Reference No', 'type' => 'text'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'member_id' => ['required', 'exists:members,id'],
            'profit_amount' => ['required', 'numeric', 'min:0'],
            'distribution_date' => ['required', 'date'],
            'reference_no' => ['required', 'string', 'max:255', 'unique:profit_distributions,reference_no'.($record?->exists ? ','.$record->getKey() : '')],
        ];
    }
}
