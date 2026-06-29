<?php

namespace App\Http\Controllers\Admin;

use App\Models\CheckoutRequest;
use App\Models\Member;
use App\Models\User;
use App\Notifications\MemberCheckoutRequestNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CheckoutRequestController extends CrudController
{
    protected function modelClass(): string
    {
        return CheckoutRequest::class;
    }

    protected function title(): string
    {
        return 'Checkout Requests';
    }

    protected function viewPrefix(): string
    {
        return 'checkout-requests';
    }

    protected function routeParameter(): string
    {
        return 'checkout_request';
    }

    protected function pageDescription(): string
    {
        return 'Full and partial checkout requests with payout details.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Member', 'key' => 'member.member_code'],
            ['label' => 'Type', 'key' => 'checkout_type'],
            ['label' => 'Refundable', 'key' => 'refundable_amount', 'type' => 'money'],
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
            ['name' => 'requested_at', 'label' => 'Requested At', 'type' => 'datetime-local'],
            ['name' => 'checkout_type', 'label' => 'Checkout Type', 'type' => 'select', 'options' => ['full' => 'Full', 'partial' => 'Partial']],
            ['name' => 'partial_percentage', 'label' => 'Partial Percentage', 'type' => 'number'],
            ['name' => 'refundable_amount', 'label' => 'Refundable Amount', 'type' => 'number'],
            ['name' => 'outstanding_loan_deducted', 'label' => 'Outstanding Deduction', 'type' => 'number'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'paid' => 'Paid']],
            ['name' => 'approved_by', 'label' => 'Approved By', 'type' => 'select', 'options' => User::query()->pluck('name', 'id')->all()],
            ['name' => 'paid_at', 'label' => 'Paid At', 'type' => 'datetime-local'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'member_id' => ['required', 'exists:members,id'],
            'requested_at' => ['required', 'date'],
            'checkout_type' => ['required', 'in:full,partial'],
            'partial_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'refundable_amount' => ['required', 'numeric', 'min:0'],
            'outstanding_loan_deducted' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:pending,approved,rejected,paid'],
            'approved_by' => ['nullable', 'exists:users,id'],
            'paid_at' => ['nullable', 'date'],
        ];
    }

    protected function afterSave(Model $record, array $data, Request $request): void
    {
        $memberUser = $record->member?->user;

        if (! $memberUser) {
            return;
        }

        $notification = match ($record->status) {
            'approved', 'paid', 'rejected' => MemberCheckoutRequestNotification::updated($record),
            default => MemberCheckoutRequestNotification::submitted($record),
        };

        $memberUser->notify($notification);
    }
}
