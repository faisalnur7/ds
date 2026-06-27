<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserController extends CrudController
{
    protected function modelClass(): string
    {
        return User::class;
    }

    protected function title(): string
    {
        return 'Users';
    }

    protected function viewPrefix(): string
    {
        return 'users';
    }

    protected function routeParameter(): string
    {
        return 'user';
    }

    protected function pageDescription(): string
    {
        return 'Assign roles, status, and access to operator accounts.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Name', 'key' => 'name'],
            ['label' => 'Email', 'key' => 'email'],
            ['label' => 'Role', 'key' => 'roleModel.name'],
            ['label' => 'Status', 'key' => 'status'],
        ];
    }

    protected function with(): array
    {
        return ['roleModel'];
    }

    protected function formFields(?Model $record = null): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
            ['name' => 'role', 'label' => 'Role', 'type' => 'select', 'options' => Role::query()->pluck('name', 'slug')->all()],
            [
                'name' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
            ],
            ['name' => 'password', 'label' => 'Password', 'type' => 'password', 'help' => 'Leave blank to keep the current password.'],
            ['name' => 'two_factor_enabled', 'label' => 'Two-factor', 'type' => 'toggle', 'helper' => 'Require 2FA for this user.'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'.($record?->exists ? ','.$record->getKey() : '')],
            'role' => ['required', 'exists:roles,slug'],
            'status' => ['required', 'in:active,inactive'],
            'password' => [$record?->exists ? 'nullable' : 'required', 'string', 'min:8'],
            'two_factor_enabled' => ['nullable', 'boolean'],
        ];
    }

    protected function transformInput(array $input, ?Model $record = null): array
    {
        if (blank($input['password'] ?? null)) {
            unset($input['password']);
        }

        $input['two_factor_enabled'] = (bool) ($input['two_factor_enabled'] ?? false);

        return $input;
    }
}
