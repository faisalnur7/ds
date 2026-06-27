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
}
