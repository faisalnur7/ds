@extends('layouts.admin')

@section('title', __($title))
@section('header', __($title))

@section('content')
    @php
        $member = $record->member;
        $repayments = $record->repayments ?? collect();
        $totalDue = $repayments->sum(fn ($repayment) => (float) $repayment->amount_due + (float) $repayment->late_fee);
        $totalPaid = $repayments->sum(fn ($repayment) => (float) $repayment->amount_paid);
        $outstanding = max($totalDue - $totalPaid, 0);
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">
        <section class="rounded-[2rem] ccims-panel-strong p-6 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __('Loan dossier') }}</p>
                    <h2 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">
                        {{ $member?->full_name ?? __('Unknown borrower') }}
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-slate-400">{{ __($description) }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route("admin.{$routePrefix}.edit", $record) }}" class="rounded-full border border-amber-300/20 bg-amber-400/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-400/20">{{ __('Edit') }}</a>
                    <a href="{{ route("admin.{$routePrefix}.index") }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5">{{ __('Back') }}</a>
                </div>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-4">
            <div class="rounded-[2rem] border border-white/10 bg-white/5 p-5">
                <p class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('Principal') }}</p>
                <p class="mt-3 text-2xl font-semibold text-white">{{ number_format((float) $record->principal_amount, 2) }}</p>
            </div>
            <div class="rounded-[2rem] border border-white/10 bg-white/5 p-5">
                <p class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('Tenure') }}</p>
                <p class="mt-3 text-2xl font-semibold text-white">{{ $record->tenure_months }} {{ __('months') }}</p>
            </div>
            <div class="rounded-[2rem] border border-white/10 bg-white/5 p-5">
                <p class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('Total Repaid') }}</p>
                <p class="mt-3 text-2xl font-semibold text-white">{{ number_format($totalPaid, 2) }}</p>
            </div>
            <div class="rounded-[2rem] border border-white/10 bg-white/5 p-5">
                <p class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('Outstanding') }}</p>
                <p class="mt-3 text-2xl font-semibold text-white">{{ number_format($outstanding, 2) }}</p>
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-[2rem] ccims-panel p-6 sm:p-8">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('Loan details') }}</p>
                        <h3 class="mt-2 text-xl font-semibold text-white">{{ __('Loan record') }}</h3>
                    </div>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $record->status === 'active' ? 'bg-emerald-400/10 text-emerald-200' : 'bg-white/10 text-slate-200' }}">
                        {{ ucfirst($record->status) }}
                    </span>
                </div>

                <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <dt class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('Member code') }}</dt>
                        <dd class="mt-2 text-sm text-white">{{ $member?->member_code ?? '—' }}</dd>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <dt class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('Approved by') }}</dt>
                        <dd class="mt-2 text-sm text-white">{{ $record->approver?->name ?? '—' }}</dd>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <dt class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('Disbursed at') }}</dt>
                        <dd class="mt-2 text-sm text-white">{{ optional($record->disbursed_at)->format('M j, Y g:i A') ?? '—' }}</dd>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <dt class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('Repayments') }}</dt>
                        <dd class="mt-2 text-sm text-white">{{ $repayments->count() }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-[2rem] ccims-panel p-6 sm:p-8">
                <div>
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('Borrower') }}</p>
                    <h3 class="mt-2 text-xl font-semibold text-white">{{ __('Member profile') }}</h3>
                </div>

                @if ($member)
                    <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                        @php
                            $memberFields = [
                                ['label' => 'Full name', 'value' => $member->full_name],
                                ['label' => 'Father name', 'value' => $member->father_name],
                                ['label' => 'Mother name', 'value' => $member->mother_name],
                                ['label' => 'Spouse name', 'value' => $member->spouse_name],
                                ['label' => 'Phone', 'value' => $member->phone],
                                ['label' => 'Emergency contact', 'value' => trim(($member->emergency_contact_name ?? '') . ' ' . ($member->emergency_contact_phone ? "({$member->emergency_contact_phone})" : ''))],
                                ['label' => 'NID number', 'value' => $member->nid_number],
                                ['label' => 'Date of birth', 'value' => optional($member->date_of_birth)->format('M j, Y')],
                                ['label' => 'Occupation', 'value' => $member->occupation],
                                ['label' => 'Blood group', 'value' => $member->blood_group],
                                ['label' => 'Religion', 'value' => $member->religion],
                                ['label' => 'Education', 'value' => $member->education],
                                ['label' => 'Join date', 'value' => optional($member->join_date)->format('M j, Y')],
                                ['label' => 'Membership status', 'value' => $member->membership_status],
                                ['label' => 'Nominee', 'value' => trim(($member->nominee_name ?? '') . ' ' . ($member->nominee_relation ? "({$member->nominee_relation})" : ''))],
                                ['label' => 'Nominee phone', 'value' => $member->nominee_phone],
                                ['label' => 'Reference', 'value' => trim(($member->reference_name ?? '') . ' ' . ($member->reference_phone ? "({$member->reference_phone})" : ''))],
                                ['label' => 'Jimmadar', 'value' => $member->user?->name],
                            ];
                        @endphp

                        @foreach ($memberFields as $field)
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 @if(in_array($field['label'], ['Occupation', 'Membership status', 'Nominee', 'Reference', 'Jimmadar'], true)) sm:col-span-2 @endif">
                                <dt class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ __($field['label']) }}</dt>
                                <dd class="mt-2 text-sm text-white">{{ $field['value'] ?: '—' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @else
                    <p class="mt-6 text-sm text-slate-400">{{ __('No member is linked to this loan.') }}</p>
                @endif
            </div>
        </section>

        <section class="rounded-[2rem] ccims-panel p-6 sm:p-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('Repayment schedule') }}</p>
                    <h3 class="mt-2 text-xl font-semibold text-white">{{ __('Installments') }}</h3>
                </div>
            </div>

            @if ($repayments->isEmpty())
                <p class="mt-6 text-sm text-slate-400">{{ __('No repayment rows have been recorded yet.') }}</p>
            @else
                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-[0.24em] text-slate-400">
                                <th class="px-4 py-3">{{ __('Due date') }}</th>
                                <th class="px-4 py-3">{{ __('Amount due') }}</th>
                                <th class="px-4 py-3">{{ __('Amount paid') }}</th>
                                <th class="px-4 py-3">{{ __('Late fee') }}</th>
                                <th class="px-4 py-3">{{ __('Status') }}</th>
                                <th class="px-4 py-3">{{ __('Paid at') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach ($repayments as $repayment)
                                <tr>
                                    <td class="px-4 py-4 text-sm text-slate-300">{{ optional($repayment->due_date)->format('M j, Y') ?? '—' }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-300">{{ number_format((float) $repayment->amount_due, 2) }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-300">{{ number_format((float) $repayment->amount_paid, 2) }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-300">{{ number_format((float) $repayment->late_fee, 2) }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-300">{{ ucfirst($repayment->status) }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-300">{{ optional($repayment->paid_at)->format('M j, Y g:i A') ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
