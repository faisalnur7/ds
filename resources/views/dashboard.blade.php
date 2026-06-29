<x-app-layout>
    <x-slot name="header">
        <h2 class="font-[family-name:Space_Grotesk] text-xl font-semibold leading-tight text-white">
            {{ __('Member Dashboard') }}
        </h2>
    </x-slot>

    @php
        $memberName = $member->full_name ?: auth()->user()->name;
        $memberCode = $member->member_code ?: '—';
        $membershipStatus = __(ucfirst((string) $member->membership_status));
        $checkoutEligibleOn = $summary['checkoutEligibleOn'] ? \Illuminate\Support\Carbon::parse($summary['checkoutEligibleOn']) : null;
        $recentPayment = $latestPayment;
        $recentCheckout = $latestCheckoutRequest;
    @endphp

    <div class="space-y-6">
        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/80">{{ __('Member portal') }}</p>
                    <h3 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white sm:text-4xl">
                        {{ $memberName }}
                    </h3>
                    <p class="mt-3 text-sm leading-6 text-slate-300 sm:text-base">
                        {{ __('Track your share contributions, document status, and checkout requests from one place.') }}
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3 lg:w-[32rem]">
                    <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ __('Member code') }}</p>
                        <p class="mt-2 text-xl font-bold text-white">{{ $memberCode }}</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ __('Status') }}</p>
                        <p class="mt-2 text-xl font-bold text-emerald-200">{{ $membershipStatus }}</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ __('Eligibility') }}</p>
                        <p class="mt-2 text-xl font-bold text-white">
                            {{ $checkoutEligibleOn ? $checkoutEligibleOn->format('M j, Y') : __('Not set') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => __('Share count'), 'value' => $summary['shareNumber'], 'tone' => 'text-white'],
                ['label' => __('Approved payments'), 'value' => $summary['approvedPayments'], 'tone' => 'text-emerald-200'],
                ['label' => __('Total contributed'), 'value' => $summary['totalContributed'], 'tone' => 'text-amber-200', 'money' => true],
                ['label' => __('Documents'), 'value' => $summary['documents'], 'tone' => 'text-sky-200'],
            ] as $card)
                <div class="rounded-3xl ccims-panel p-5 sm:p-6">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ $card['label'] }}</p>
                    <p class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold leading-none {{ $card['tone'] }}">
                        @if (! empty($card['money']))
                            {{ number_format((float) $card['value'], 2) }}
                        @else
                            {{ number_format((int) $card['value']) }}
                        @endif
                    </p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.35fr_0.95fr]">
            <div class="space-y-6">
                <div class="rounded-3xl ccims-panel p-5 sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h3 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">{{ __('Quick actions') }}</h3>
                            <p class="mt-1 text-sm text-slate-400">{{ __('Access the most common member tasks.') }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('payment-history.index') }}" class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-100">
                                {{ __('Payment history') }}
                            </a>
                            <a href="{{ route('checkout-requests.index') }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/5">
                                {{ __('Checkout requests') }}
                            </a>
                            <a href="{{ route('profile.edit') }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/5">
                                {{ __('Profile') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl ccims-panel p-5 sm:p-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <h3 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">{{ __('Recent payments') }}</h3>
                            <p class="mt-1 text-sm text-slate-400">{{ __('Latest approved and pending payment records.') }}</p>
                        </div>
                        <a href="{{ route('payment-history.index') }}" class="text-sm font-medium text-amber-200 hover:text-amber-100">
                            {{ __('View all') }}
                        </a>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse ($recentPayments as $payment)
                            <article class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ optional($payment->payment_month)->format('M Y') ?? __('Payment') }}</p>
                                        <p class="mt-1 text-sm text-slate-400">{{ __('Due') }}: {{ optional($payment->due_date)->format('M j, Y') ?? '—' }}</p>
                                    </div>
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $payment->status === 'approved' ? 'bg-emerald-400/10 text-emerald-200' : ($payment->status === 'pending' ? 'bg-amber-400/10 text-amber-200' : 'bg-rose-400/10 text-rose-200') }}">
                                        {{ __(ucfirst($payment->status)) }}
                                    </span>
                                </div>
                                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                    <div>
                                        <p class="text-[0.7rem] uppercase tracking-[0.18em] text-slate-500">{{ __('Paid') }}</p>
                                        <p class="mt-1 text-sm font-semibold text-white">{{ number_format((float) $payment->amount_paid, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[0.7rem] uppercase tracking-[0.18em] text-slate-500">{{ __('Method') }}</p>
                                        <p class="mt-1 text-sm font-semibold text-white">{{ __(ucfirst($payment->payment_method)) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[0.7rem] uppercase tracking-[0.18em] text-slate-500">{{ __('Reference') }}</p>
                                        <p class="mt-1 text-sm font-semibold text-white">{{ $payment->receipt_no ?? $payment->transaction_no ?? '—' }}</p>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-6 text-sm text-slate-400">
                                {{ __('No payment records are available yet.') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl ccims-panel p-5 sm:p-6">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <h3 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">{{ __('Share history') }}</h3>
                            <p class="mt-1 text-sm text-slate-400">{{ __('Changes to your share count over time.') }}</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse ($shareHistory as $entry)
                            <article class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ $entry->share_number }} {{ __('shares') }}</p>
                                        <p class="mt-1 text-sm text-slate-400">{{ optional($entry->changed_at)->format('M j, Y g:i A') ?? '—' }}</p>
                                    </div>
                                    <span class="rounded-full bg-white/5 px-3 py-1 text-xs font-semibold text-slate-300">
                                        {{ $entry->note ?: __('Recorded update') }}
                                    </span>
                                </div>
                            </article>
                        @empty
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-6 text-sm text-slate-400">
                                {{ __('No share history found.') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl ccims-panel p-5 sm:p-6">
                    <h3 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">{{ __('Checkout status') }}</h3>
                    <div class="mt-4 rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">{{ __('Latest request') }}</p>
                        <p class="mt-2 text-lg font-semibold text-white">{{ $recentCheckout?->checkout_type ? __(ucfirst($recentCheckout->checkout_type)) : __('No request yet') }}</p>
                        <p class="mt-2 text-sm text-slate-400">
                            {{ $recentCheckout ? __('Status: :status', ['status' => __(ucfirst($recentCheckout->status))]) : __('Submit a request when you are eligible.') }}
                        </p>
                        @if ($recentCheckout)
                            <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <dt class="text-slate-500">{{ __('Refundable') }}</dt>
                                    <dd class="mt-1 text-white">{{ number_format((float) $recentCheckout->refundable_amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">{{ __('Requested') }}</dt>
                                    <dd class="mt-1 text-white">{{ optional($recentCheckout->requested_at)->format('M j, Y') ?? '—' }}</dd>
                                </div>
                            </dl>
                        @endif
                    </div>
                    <a href="{{ route('checkout-requests.index') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-white px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-100">
                        {{ __('Open checkout requests') }}
                    </a>
                </div>

                <div class="rounded-3xl ccims-panel p-5 sm:p-6">
                    <h3 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">{{ __('Documents') }}</h3>
                    <div class="mt-4 space-y-3">
                        @forelse ($recentDocuments as $document)
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ __(str_replace('_', ' ', $document->doc_type)) }}</p>
                                        <p class="mt-1 text-sm text-slate-400">{{ $document->file_path }}</p>
                                    </div>
                                    <span class="rounded-full bg-white/5 px-3 py-1 text-xs font-semibold text-slate-300">
                                        {{ $document->verified_by ? __('Verified') : __('Pending') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-6 text-sm text-slate-400">
                                {{ __('No documents have been uploaded for this account yet.') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl ccims-panel p-5 sm:p-6">
                    <h3 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">{{ __('Portal summary') }}</h3>
                    <div class="mt-4 space-y-3 text-sm text-slate-300">
                        <div class="flex items-center justify-between gap-4">
                            <span>{{ __('Eligible for checkout') }}</span>
                            <span class="font-semibold text-white">{{ $checkoutEligibleOn ? $checkoutEligibleOn->format('M j, Y') : __('Not set') }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span>{{ __('Checkout requests') }}</span>
                            <span class="font-semibold text-white">{{ number_format((int) $summary['checkoutRequests']) }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span>{{ __('Documents uploaded') }}</span>
                            <span class="font-semibold text-white">{{ number_format((int) $summary['documents']) }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span>{{ __('Latest payment') }}</span>
                            <span class="font-semibold text-white">{{ $recentPayment ? optional($recentPayment->payment_month)->format('M Y') : __('No payment yet') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
