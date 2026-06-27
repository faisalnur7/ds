<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\Project;
use App\Models\ProfitDistribution;
use Illuminate\Database\Eloquent\Model;

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

    protected function formFields(?Model $record = null): array
    {
        return [
            ['name' => 'project_id', 'label' => 'Project', 'type' => 'select', 'options' => Project::query()->pluck('name', 'id')->all()],
            ['name' => 'member_id', 'label' => 'Member', 'type' => 'select', 'options' => Member::query()->pluck('member_code', 'id')->all()],
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
