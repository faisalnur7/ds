<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use App\Models\ProjectIncome;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

class ProjectIncomeController extends CrudController
{
    protected function modelClass(): string
    {
        return ProjectIncome::class;
    }

    protected function title(): string
    {
        return 'Project Incomes';
    }

    protected function viewPrefix(): string
    {
        return 'project-incomes';
    }

    protected function routeParameter(): string
    {
        return 'project_income';
    }

    protected function pageDescription(): string
    {
        return 'Recurring income entries for active projects.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Project', 'key' => 'project.name'],
            ['label' => 'Type', 'key' => 'income_type'],
            ['label' => 'Amount', 'key' => 'amount', 'type' => 'money'],
            ['label' => 'Date', 'key' => 'income_date'],
        ];
    }

    protected function with(): array
    {
        return ['project'];
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
            ['name' => 'income_type', 'label' => 'Income Type', 'type' => 'select', 'options' => ['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'yearly' => 'Yearly']],
            ['name' => 'amount', 'label' => 'Amount', 'type' => 'number'],
            ['name' => 'income_date', 'label' => 'Income Date', 'type' => 'date'],
            ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'textarea', 'span' => 2],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'income_type' => ['required', 'in:daily,weekly,monthly,yearly'],
            'amount' => ['required', 'numeric', 'min:0'],
            'income_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
