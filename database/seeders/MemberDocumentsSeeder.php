<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\MemberDocument;
use App\Models\User;
use Illuminate\Database\Seeder;

class MemberDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        $member = Member::query()->firstOrFail();
        $verifier = User::query()->where('email', 'admin@example.com')->value('id');

        foreach (['nid_front', 'nid_back', 'photo', 'signature', 'nominee_nid'] as $docType) {
            MemberDocument::query()->updateOrCreate(
                [
                    'member_id' => $member->id,
                    'doc_type' => $docType,
                ],
                [
                    'file_path' => "seeded/{$docType}.pdf",
                    'uploaded_at' => now(),
                    'verified_by' => $verifier,
                ]
            );
        }
    }
}
