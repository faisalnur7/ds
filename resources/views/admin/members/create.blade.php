@extends('layouts.admin')

@section('title', $title)
@section('header', $title)

@section('content')
    <div class="mx-auto max-w-7xl space-y-6">
        <section class="rounded-[2rem] ccims-panel-strong p-6 sm:p-8">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">Form</p>
            <h2 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">{{ $title }}</h2>
            <p class="mt-3 text-sm leading-6 text-slate-400">{{ $description }}</p>
        </section>

        @include('partials.member-onboarding-form', [
            'action' => $action,
            'submitLabel' => 'Create member account',
            'backUrl' => $backUrl,
            'backLabel' => 'Back to members',
            'memberCode' => $memberCode,
            'formClass' => 'space-y-6 rounded-[2rem] border border-white/10 bg-slate-950/70 p-6 shadow-2xl shadow-slate-950/30 backdrop-blur sm:p-8',
            'heroClass' => 'rounded-[1.75rem] border border-white/10 bg-white/5 p-5 sm:p-6',
            'eyebrow' => 'Member onboarding',
            'heading' => 'Create a new member',
            'description' => 'Complete the same stepped onboarding flow used on the public registration page.',
        ])
    </div>
@endsection
