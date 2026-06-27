<?php

namespace App\Http\Controllers\Admin;

use App\Models\Member;
use App\Models\MemberDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MemberDocumentController extends CrudController
{
    protected function modelClass(): string
    {
        return MemberDocument::class;
    }

    protected function title(): string
    {
        return 'Member Documents';
    }

    protected function viewPrefix(): string
    {
        return 'member-documents';
    }

    protected function routeParameter(): string
    {
        return 'member_document';
    }

    protected function pageDescription(): string
    {
        return 'KYC uploads, verification metadata, and document references.';
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Member', 'key' => 'member.member_code'],
            ['label' => 'Type', 'key' => 'doc_type'],
            ['label' => 'File', 'key' => 'file_path'],
            ['label' => 'Verified By', 'key' => 'verifier.name'],
        ];
    }

    protected function with(): array
    {
        return ['member', 'verifier'];
    }

    protected function formFields(?Model $record = null): array
    {
        return [
            ['name' => 'member_id', 'label' => 'Member', 'type' => 'select', 'options' => Member::query()->pluck('member_code', 'id')->all()],
            ['name' => 'doc_type', 'label' => 'Document Type', 'type' => 'select', 'options' => ['nid_front' => 'NID Front', 'nid_back' => 'NID Back', 'photo' => 'Photo', 'signature' => 'Signature', 'nominee_nid' => 'Nominee NID']],
            ['name' => 'file_path', 'label' => 'File Path', 'type' => 'text'],
            ['name' => 'uploaded_at', 'label' => 'Uploaded At', 'type' => 'datetime-local'],
            ['name' => 'verified_by', 'label' => 'Verified By', 'type' => 'select', 'options' => User::query()->pluck('name', 'id')->all()],
        ];
    }

    protected function rules(?Model $record = null): array
    {
        return [
            'member_id' => ['required', 'exists:members,id'],
            'doc_type' => ['required', 'in:nid_front,nid_back,photo,signature,nominee_nid'],
            'file_path' => ['required', 'string', 'max:255'],
            'uploaded_at' => ['required', 'date'],
            'verified_by' => ['nullable', 'exists:users,id'],
        ];
    }
}
