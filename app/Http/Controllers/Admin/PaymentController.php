<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class PaymentController extends CrudController
{
    protected function modelClass(): string
    {
        return Payment::class;
    }

    protected function title(): string
    {
        return 'Payments';
    }

    protected function viewPrefix(): string
    {
        return 'payments';
    }

    protected function routeParameter(): string
    {
        return 'payment';
    }

    protected function pageDescription(): string
    {
        return 'Payment capture, approval state, and receipt tracking.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Member', 'key' => 'member.member_code'],
            ['label' => 'Month', 'key' => 'payment_month'],
            ['label' => 'Paid', 'key' => 'amount_paid', 'type' => 'money'],
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
            ['name' => 'payment_month', 'label' => 'Payment Month', 'type' => 'date'],
            ['name' => 'due_date', 'label' => 'Due Date', 'type' => 'date'],
            ['name' => 'share_value', 'label' => 'Share Value', 'type' => 'number'],
            ['name' => 'share_cost', 'label' => 'Share Cost', 'type' => 'number'],
            ['name' => 'fine_amount', 'label' => 'Fine', 'type' => 'number'],
            ['name' => 'amount_paid', 'label' => 'Amount Paid', 'type' => 'number'],
            ['name' => 'payment_method', 'label' => 'Payment Method', 'type' => 'select', 'options' => ['cash' => 'Cash', 'bank' => 'Bank', 'bkash' => 'bKash', 'nagad' => 'Nagad', 'rocket' => 'Rocket', 'other' => 'Other']],
            ['name' => 'transaction_no', 'label' => 'Transaction No', 'type' => 'text'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']],
            ['name' => 'receipt_no', 'label' => 'Receipt No', 'type' => 'text'],
            ['name' => 'payment_status_detail', 'label' => 'Payment Status Detail', 'type' => 'select', 'options' => ['full' => 'Full', 'partial' => 'Partial']],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'member_id' => ['required', 'exists:members,id'],
            'payment_month' => [
                'required',
                'date',
                Rule::unique('payments', 'payment_month')->where(fn ($query) => $query->where('member_id', request('member_id')))->ignore($record?->getKey()),
            ],
            'due_date' => ['required', 'date'],
            'share_value' => ['required', 'numeric', 'min:0'],
            'share_cost' => ['required', 'numeric', 'min:0'],
            'fine_amount' => ['required', 'numeric', 'min:0'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,bank,bkash,nagad,rocket,other'],
            'transaction_no' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:pending,approved,rejected'],
            'receipt_no' => ['nullable', 'string', 'max:255'],
            'payment_status_detail' => ['required', 'in:full,partial'],
        ];
    }
}
