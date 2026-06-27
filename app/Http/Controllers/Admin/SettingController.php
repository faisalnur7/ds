<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends CrudController
{
    protected function modelClass(): string
    {
        return Setting::class;
    }

    protected function title(): string
    {
        return 'Settings';
    }

    protected function viewPrefix(): string
    {
        return 'settings';
    }

    protected function routeParameter(): string
    {
        return 'setting';
    }

    protected function pageDescription(): string
    {
        return 'On/off toggles and typed configuration values.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Key', 'key' => 'key'],
            ['label' => 'Value', 'key' => 'value'],
            ['label' => 'Type', 'key' => 'value_type'],
        ];
    }

    protected function formFields(?Model $record = null): array
    {
        return [
            ['name' => 'key', 'label' => 'Key', 'type' => 'text'],
            [
                'name' => 'value_type',
                'label' => 'Type',
                'type' => 'select',
                'options' => [
                    'string' => 'String',
                    'bool' => 'Boolean',
                    'int' => 'Integer',
                    'float' => 'Float',
                    'json' => 'JSON',
                ],
            ],
            ['name' => 'value', 'label' => 'Value', 'type' => 'text', 'span' => 2],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'key' => ['required', 'string', 'max:255', 'unique:settings,key'.($record?->exists ? ','.$record->getKey() : '')],
            'value_type' => ['required', 'in:string,bool,int,float,json'],
            'value' => ['nullable', 'string'],
        ];
    }

    protected function transformInput(array $input, ?Model $record = null): array
    {
        if ($input['value_type'] === 'bool') {
            $input['value'] = in_array(strtolower((string) $input['value']), ['1', 'true', 'on', 'yes'], true) ? '1' : '0';
        }

        if ($input['value_type'] === 'json' && is_string($input['value'])) {
            json_decode($input['value'], true, 512, JSON_THROW_ON_ERROR);
        }

        return $input;
    }

    protected function afterSave(Model $record, array $data, Request $request): void
    {
        Cache::forget("ccims.setting.{$record->key}");
    }
}
