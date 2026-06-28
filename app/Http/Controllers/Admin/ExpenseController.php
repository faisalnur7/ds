<?php

namespace App\Http\Controllers\Admin;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends CrudController
{
    protected function modelClass(): string
    {
        return Expense::class;
    }

    protected function title(): string
    {
        return 'Expenses';
    }

    protected function viewPrefix(): string
    {
        return 'expenses';
    }

    protected function routeParameter(): string
    {
        return 'expense';
    }

    protected function pageDescription(): string
    {
        return 'Track operational spending by category, amount, date, and approval state.';
    }

    protected function with(): array
    {
        return ['category', 'approver'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Category', 'key' => 'category.name'],
            ['label' => 'Title', 'key' => 'title'],
            ['label' => 'Amount', 'key' => 'amount', 'type' => 'money'],
            ['label' => 'Date', 'key' => 'date', 'type' => 'date'],
            ['label' => 'Status', 'key' => 'status'],
            ['label' => 'Approved By', 'key' => 'approver.name'],
        ];
    }

    protected function formFields(?Model $record = null): array
    {
        return [
            ['name' => 'expense_category_id', 'label' => 'Expense Category', 'type' => 'select', 'options' => ['' => 'Select category'] + ExpenseCategory::query()->orderBy('name')->pluck('name', 'id')->all()],
            ['name' => 'title', 'label' => 'Title', 'type' => 'text'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'span' => 2],
            ['name' => 'amount', 'label' => 'Amount', 'type' => 'number', 'step' => '0.01'],
            ['name' => 'date', 'label' => 'Expense Date', 'type' => 'date'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'expense_category_id' => ['required', 'exists:expense_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
            'status' => ['required', 'in:pending,approved,rejected'],
        ];
    }

    protected function transformInput(array $input, ?Model $record = null): array
    {
        $status = $input['status'] ?? 'pending';
        $wasApproved = $record?->status === 'approved';

        if ($status === 'approved' && ! $wasApproved) {
            $input['approved_by'] = auth()->id();
            $input['approved_at'] = now();
        } elseif ($status !== 'approved') {
            $input['approved_by'] = null;
            $input['approved_at'] = null;
        }

        return $input;
    }

    protected function showContext(Model $record): array
    {
        $context = [
            'summary' => [
                ['label' => 'Category', 'value' => $record->category?->name ?? '—'],
                ['label' => 'Status', 'value' => ucfirst((string) $record->status)],
                ['label' => 'Approved By', 'value' => $record->approver?->name ?? '—'],
            ],
        ];

        if ($this->can(request(), 'approve') && $record->status !== 'approved') {
            $context['actions'] = [
                [
                    'label' => 'Approve expense',
                    'action' => route('admin.expenses.approve', $record),
                    'method' => 'PATCH',
                    'buttonClass' => 'bg-emerald-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300',
                ],
            ];
        }

        return $context;
    }

    public function approve(Request $request, Expense $expense): RedirectResponse
    {
        $this->requirePermission($request, 'approve');

        $expense->forceFill([
            'status' => 'approved',
            'approved_by' => $request->user()?->id,
            'approved_at' => now(),
        ])->save();

        return redirect()
            ->route("admin.{$this->viewPrefix()}.show", $expense)
            ->with('status', 'updated');
    }
}
