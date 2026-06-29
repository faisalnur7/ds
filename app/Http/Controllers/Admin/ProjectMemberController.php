<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectMemberController extends CrudController
{
    protected function modelClass(): string
    {
        return ProjectMember::class;
    }

    protected function title(): string
    {
        return 'Project Members';
    }

    protected function viewPrefix(): string
    {
        return 'project-members';
    }

    protected function routeParameter(): string
    {
        return 'project_member';
    }

    protected function pageDescription(): string
    {
        return 'Project allocations and active share participation.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Project', 'key' => 'project.name'],
            ['label' => 'Member', 'key' => 'member.member_code'],
            ['label' => 'Allocation', 'key' => 'allocated_share_amount', 'type' => 'money'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'bool'],
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
            'fields' => $this->formFields(),
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
        return [
            ['name' => 'project_id', 'label' => 'Project', 'type' => 'select', 'options' => Project::query()->pluck('name', 'id')->all()],
            ['name' => 'member_id', 'label' => 'Member', 'type' => 'select', 'options' => Member::query()->pluck('member_code', 'id')->all()],
            ['name' => 'allocated_share_amount', 'label' => 'Allocated Share Amount', 'type' => 'number'],
            ['name' => 'is_active', 'label' => 'Active', 'type' => 'toggle'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        $projectId = (int) request()->input('project_id', $record?->project_id);
        $project = $projectId ? Project::query()->find($projectId) : null;

        return [
            'project_id' => ['required', 'exists:projects,id'],
            'member_id' => ['required', 'exists:members,id'],
            'allocated_share_amount' => array_filter([
                'required',
                'numeric',
                'min:0',
                $project
                    ? 'lte:'.$project->invested_amount
                    : null,
            ]),
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function transformInput(array $input, ?Model $record = null): array
    {
        $input['is_active'] = (bool) ($input['is_active'] ?? false);
        return $input;
    }
}
