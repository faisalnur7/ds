<?php

namespace App\Http\Controllers\Admin;

use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategoryController extends CrudController
{
    protected function modelClass(): string
    {
        return ExpenseCategory::class;
    }

    protected function title(): string
    {
        return 'Expense Categories';
    }

    protected function viewPrefix(): string
    {
        return 'expense-categories';
    }

    protected function routeParameter(): string
    {
        return 'expense_category';
    }

    protected function pageDescription(): string
    {
        return 'Organize operational expense buckets for reporting and approval review.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Name', 'key' => 'name'],
            ['label' => 'Description', 'key' => 'description'],
            ['label' => 'Status', 'key' => 'status'],
        ];
    }

    protected function formFields(?Model $record = null): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'span' => 2],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:expense_categories,name'.($record?->exists ? ','.$record->getKey() : '')],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}
