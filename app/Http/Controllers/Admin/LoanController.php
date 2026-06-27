<?php

namespace App\Http\Controllers\Admin;

use App\Models\Loan;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LoanController extends CrudController
{
    protected function modelClass(): string
    {
        return Loan::class;
    }

    protected function title(): string
    {
        return 'Loans';
    }

    protected function viewPrefix(): string
    {
        return 'loans';
    }

    protected function routeParameter(): string
    {
        return 'loan';
    }

    protected function pageDescription(): string
    {
        return 'Loan against share requests, approvals, and disbursements.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Member', 'key' => 'member.member_code'],
            ['label' => 'Principal', 'key' => 'principal_amount', 'type' => 'money'],
            ['label' => 'Tenure', 'key' => 'tenure_months'],
            ['label' => 'Status', 'key' => 'status'],
        ];
    }

    protected function with(): array
    {
        return ['member'];
    }

    protected function formFields(?Model $record = null): array
    {
        return [
            ['name' => 'member_id', 'label' => 'Member', 'type' => 'select', 'options' => Member::query()->pluck('member_code', 'id')->all()],
            ['name' => 'principal_amount', 'label' => 'Principal Amount', 'type' => 'number'],
            ['name' => 'tenure_months', 'label' => 'Tenure Months', 'type' => 'number'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['pending' => 'Pending', 'approved' => 'Approved', 'active' => 'Active', 'closed' => 'Closed', 'defaulted' => 'Defaulted', 'rejected' => 'Rejected']],
            ['name' => 'approved_by', 'label' => 'Approved By', 'type' => 'select', 'options' => User::query()->pluck('name', 'id')->all()],
            ['name' => 'disbursed_at', 'label' => 'Disbursed At', 'type' => 'datetime-local'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'member_id' => ['required', 'exists:members,id'],
            'principal_amount' => ['required', 'numeric', 'min:0'],
            'tenure_months' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:pending,approved,active,closed,defaulted,rejected'],
            'approved_by' => ['nullable', 'exists:users,id'],
            'disbursed_at' => ['nullable', 'date'],
        ];
    }
}
