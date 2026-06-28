<x-guest-layout>
    @include('partials.member-onboarding-form', [
        'action' => route('register'),
        'submitLabel' => 'Create member account',
        'backUrl' => route('login'),
        'backLabel' => 'Already have an account?',
        'memberCode' => $memberCode ?? \App\Models\Member::nextMemberCode(),
        'formClass' => 'space-y-6',
        'heroClass' => 'rounded-[1.75rem] border border-white/10 bg-white/5 p-5 sm:p-6',
        'eyebrow' => 'Member onboarding',
        'heading' => 'Register a new member',
        'description' => 'Complete the registration in steps. The account is created in users and the profile is stored in members.',
    ])
</x-guest-layout>
