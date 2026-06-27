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
            ['name' => 'father_name', 'label' => 'Father Name', 'type' => 'text'],
            ['name' => 'mother_name', 'label' => 'Mother Name', 'type' => 'text'],
            ['name' => 'spouse_name', 'label' => 'Spouse Name', 'type' => 'text'],
            ['name' => 'spouse_phone', 'label' => 'Spouse Phone', 'type' => 'text'],
            ['name' => 'blood_group', 'label' => 'Blood Group', 'type' => 'select', 'options' => [
                '' => 'Select blood group',
                'A+' => 'A+',
                'A-' => 'A-',
                'B+' => 'B+',
                'B-' => 'B-',
                'AB+' => 'AB+',
                'AB-' => 'AB-',
                'O+' => 'O+',
                'O-' => 'O-',
            ]],
            ['name' => 'religion', 'label' => 'Religion', 'type' => 'text'],
            ['name' => 'education', 'label' => 'Education', 'type' => 'text'],
            ['name' => 'emergency_contact_name', 'label' => 'Emergency Contact Name', 'type' => 'text'],
            ['name' => 'emergency_contact_phone', 'label' => 'Emergency Contact Phone', 'type' => 'text'],
            ['name' => 'phone', 'label' => 'Phone', 'type' => 'text'],
            ['name' => 'nid_number', 'label' => 'NID Number', 'type' => 'text'],
            ['name' => 'date_of_birth', 'label' => 'Date of Birth', 'type' => 'date'],
            ['name' => 'occupation', 'label' => 'Occupation', 'type' => 'text'],
            ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'span' => 2],
            ['name' => 'present_address', 'label' => 'Present Address', 'type' => 'textarea', 'span' => 2],
            ['name' => 'permanent_address', 'label' => 'Permanent Address', 'type' => 'textarea', 'span' => 2],
            ['name' => 'nominee_name', 'label' => 'Nominee Name', 'type' => 'text'],
            ['name' => 'nominee_relation', 'label' => 'Nominee Relation', 'type' => 'text'],
            ['name' => 'nominee_phone', 'label' => 'Nominee Phone', 'type' => 'text'],
            ['name' => 'reference_name', 'label' => 'Reference Name', 'type' => 'text'],
            ['name' => 'reference_phone', 'label' => 'Reference Phone', 'type' => 'text'],
            ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'textarea', 'span' => 2],
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
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'spouse_phone' => ['nullable', 'string', 'max:255'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'religion' => ['nullable', 'string', 'max:255'],
            'education' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'nid_number' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'present_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            'nominee_name' => ['nullable', 'string', 'max:255'],
            'nominee_relation' => ['nullable', 'string', 'max:255'],
            'nominee_phone' => ['nullable', 'string', 'max:255'],
            'reference_name' => ['nullable', 'string', 'max:255'],
            'reference_phone' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
            'join_date' => ['required', 'date'],
            'membership_status' => ['required', 'in:active,revoked,checked_out'],
            'checkout_eligible_after_months' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
