<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class PermissionController extends CrudController
{
    protected function modelClass(): string
    {
        return Permission::class;
    }

    protected function title(): string
    {
        return 'Permissions';
    }

    protected function viewPrefix(): string
    {
        return 'permissions';
    }

    protected function routeParameter(): string
    {
        return 'permission';
    }

    protected function pageDescription(): string
    {
        return 'Atomic permission records grouped by menu category and action.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Name', 'key' => 'name'],
            ['label' => 'Slug', 'key' => 'slug'],
            ['label' => 'Category', 'key' => 'group_name'],
        ];
    }

    protected function formFields(?Model $record = null): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'help' => 'Example: Create Members'],
            ['name' => 'slug', 'label' => 'Slug', 'type' => 'text', 'help' => 'Example: create_members'],
            ['name' => 'group_name', 'label' => 'Category', 'type' => 'text', 'help' => 'Example: members'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:permissions,slug'.($record?->exists ? ','.$record->getKey() : '')],
            'group_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
