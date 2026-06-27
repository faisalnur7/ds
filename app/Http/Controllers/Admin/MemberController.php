<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class MemberController extends CrudController
{
    protected function modelClass(): string
    {
        return Member::class;
    }

    protected function title(): string
    {
        return 'Members';
    }

    protected function viewPrefix(): string
    {
        return 'members';
    }

    protected function routeParameter(): string
    {
        return 'member';
    }

    protected function pageDescription(): string
    {
        return 'Member onboarding, KYC, and lifecycle records.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Code', 'key' => 'member_code'],
            ['label' => 'Name', 'key' => 'full_name'],
            ['label' => 'Status', 'key' => 'membership_status'],
            ['label' => 'Join Date', 'key' => 'join_date'],
        ];
    }

    protected function with(): array
    {
        return ['user'];
    }

    protected function formFields(?Model $record = null): array
    {
        return [
            ['name' => 'user_id', 'label' => 'Linked User', 'type' => 'select', 'options' => User::query()->pluck('name', 'id')->all()],
            ['name' => 'member_code', 'label' => 'Member Code', 'type' => 'text'],
            ['name' => 'full_name', 'label' => 'Full Name', 'type' => 'text'],
            ['name' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['name' => 'nid_number', 'label' => 'NID Number', 'type' => 'text'],
            ['name' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date'],
            ['name' => 'occupation', 'label' => 'Occupation', 'type' => 'text'],
            ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'span' => 2],
            ['name' => 'nominee_name', 'label' => 'Nominee Name', 'type' => 'text'],
            ['name' => 'nominee_relation', 'label' => 'Nominee Relation', 'type' => 'text'],
            ['name' => 'nominee_phone', 'label' => 'Nominee Phone', 'type' => 'text'],
            ['name' => 'join_date', 'label' => 'Join Date', 'type' => 'date'],
            ['name' => 'membership_status', 'label' => 'Membership Status', 'type' => 'select', 'options' => ['active' => 'Active', 'revoked' => 'Revoked', 'checked_out' => 'Checked Out']],
            ['name' => 'checkout_eligible_after_months', 'label' => 'Checkout Eligible After Months', 'type' => 'number'],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'user_id' => ['required', 'exists:users,id', Rule::unique('members', 'user_id')->ignore($record?->getKey())],
            'member_code' => ['required', 'string', 'max:255', Rule::unique('members', 'member_code')->ignore($record?->getKey())],
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'nid_number' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'nominee_name' => ['nullable', 'string', 'max:255'],
            'nominee_relation' => ['nullable', 'string', 'max:255'],
            'nominee_phone' => ['nullable', 'string', 'max:255'],
            'join_date' => ['required', 'date'],
            'membership_status' => ['required', 'in:active,revoked,checked_out'],
            'checkout_eligible_after_months' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
