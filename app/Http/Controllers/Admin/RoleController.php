<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoleController extends CrudController
{
    protected function modelClass(): string
    {
        return Role::class;
    }

    protected function title(): string
    {
        return 'Roles';
    }

    protected function viewPrefix(): string
    {
        return 'roles';
    }

    protected function routeParameter(): string
    {
        return 'role';
    }

    protected function pageDescription(): string
    {
        return 'System roles and permission sets for ERP access control.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Name', 'key' => 'name'],
            ['label' => 'Slug', 'key' => 'slug'],
            ['label' => 'System', 'key' => 'is_system', 'type' => 'bool'],
        ];
    }

    protected function with(): array
    {
        return ['permissions'];
    }

    protected function formFields(?Model $record = null): array
    {
        $permissions = Permission::query()
            ->orderByRaw('COALESCE(group_name, \'\')')
            ->orderBy('name')
            ->get();

        $groupedPermissions = $permissions
            ->groupBy(fn (Permission $permission) => $permission->group_name ?: 'other')
            ->map(function ($items, string $groupName): array {
                return [
                    'label' => Str::headline(str_replace(['_', '-'], ' ', $groupName)),
                    'permissions' => $items->map(fn (Permission $permission): array => [
                        'id' => $permission->id,
                        'name' => $permission->name,
                    ])->all(),
                ];
            })
            ->values()
            ->all();

        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'slug', 'label' => 'Slug', 'type' => 'text', 'help' => 'Use snake_case.'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'span' => 2],
            [
                'name' => 'permissions',
                'label' => 'Permissions',
                'type' => 'grouped-multiselect',
                'span' => 2,
                'groups' => $groupedPermissions,
            ],
            ['name' => 'is_system', 'label' => 'System Role', 'type' => 'toggle', 'helper' => 'Protect this role from deletion.'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:roles,slug'.($record?->exists ? ','.$record->getKey() : '')],
            'description' => ['nullable', 'string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
            'is_system' => ['nullable', 'boolean'],
        ];
    }

    protected function transformInput(array $input, ?Model $record = null): array
    {
        $input['is_system'] = (bool) ($input['is_system'] ?? false);
        return $input;
    }

    protected function afterSave(Model $record, array $data, \Illuminate\Http\Request $request): void
    {
        $record->permissions()->sync($data['permissions'] ?? []);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $record = $this->resolveRecord($request);

        if ($record->is_system || $record->users()->exists()) {
            return back()->withErrors(['delete' => 'System roles or roles with users cannot be deleted.']);
        }

        return parent::destroy($request);
    }
}
