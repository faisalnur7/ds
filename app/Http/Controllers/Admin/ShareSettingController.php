<?php

namespace App\Http\Controllers\Admin;

use App\Models\ShareSetting;
use Illuminate\Database\Eloquent\Model;

class ShareSettingController extends CrudController
{
    protected function modelClass(): string
    {
        return ShareSetting::class;
    }

    protected function title(): string
    {
        return 'Share Settings';
    }

    protected function viewPrefix(): string
    {
        return 'share-settings';
    }

    protected function routeParameter(): string
    {
        return 'share_setting';
    }

    protected function pageDescription(): string
    {
        return 'Versioned share value and fine rules.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Share Value', 'key' => 'share_value', 'type' => 'money'],
            ['label' => 'Share Cost', 'key' => 'share_cost', 'type' => 'money'],
            ['label' => 'Fine', 'key' => 'fine_amount', 'type' => 'money'],
            ['label' => 'Active', 'key' => 'is_active', 'type' => 'bool'],
        ];
    }

    protected function formFields(?Model $record = null): array
    {
        return [
            ['name' => 'share_value', 'label' => 'Share Value', 'type' => 'number'],
            ['name' => 'share_cost', 'label' => 'Share Cost', 'type' => 'number'],
            ['name' => 'fine_amount', 'label' => 'Flat Fine', 'type' => 'number'],
            ['name' => 'fine_percent', 'label' => 'Fine Percent', 'type' => 'number'],
            ['name' => 'effective_from', 'label' => 'Effective From', 'type' => 'date'],
            ['name' => 'is_active', 'label' => 'Active', 'type' => 'toggle', 'helper' => 'Use this version immediately.'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'share_value' => ['required', 'numeric', 'min:0'],
            'share_cost' => ['required', 'numeric', 'min:0'],
            'fine_amount' => ['required', 'numeric', 'min:0'],
            'fine_percent' => ['required', 'numeric', 'min:0'],
            'effective_from' => ['required', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function transformInput(array $input, ?Model $record = null): array
    {
        $input['is_active'] = (bool) ($input['is_active'] ?? false);
        return $input;
    }
}
