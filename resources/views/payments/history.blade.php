<x-app-layout>
    <x-slot name="header">
        <h2 class="font-[family-name:Space_Grotesk] text-xl font-semibold leading-tight text-white">
            {{ __('Payment History') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-black/20 backdrop-blur-xl">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __('Member payments') }}</p>
            <h3 class="mt-4 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">
                {{ $member->full_name }}
            </h3>
            <p class="mt-4 max-w-3xl text-base leading-7 text-slate-300">
                {{ __('View your monthly share payments and approval status in one place.') }}
            </p>
        </section>

        <section class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-5">
                <p class="text-sm text-slate-400">{{ __('Total payments') }}</p>
                <p class="mt-2 text-2xl font-bold text-white">{{ $payments->total() }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-5">
                <p class="text-sm text-slate-400">{{ __('Current month') }}</p>
                <p class="mt-2 text-2xl font-bold text-white">{{ now()->format('M Y') }}</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-5">
                <p class="text-sm text-slate-400">{{ __('Member code') }}</p>
                <p class="mt-2 text-2xl font-bold text-white">{{ $member->member_code }}</p>
            </div>
        </section>

        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-4 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-6">
            <div class="space-y-3 md:hidden">
                @forelse ($payments as $payment)
                    <article class="rounded-[1.5rem] border border-white/10 bg-slate-950/40 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.24em] text-slate-500">{{ __('Month') }}</p>
                                <p class="mt-1 text-base font-semibold text-white">{{ optional($payment->payment_month)->format('M Y') ?? '—' }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $payment->status === 'approved' ? 'bg-emerald-400/10 text-emerald-200' : ($payment->status === 'pending' ? 'bg-amber-400/10 text-amber-200' : 'bg-rose-400/10 text-rose-200') }}">
                                {{ __(ucfirst($payment->status)) }}
                            </span>
                        </div>

                        <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-3">
                                <dt class="text-[11px] uppercase tracking-[0.2em] text-slate-500">{{ __('Paid') }}</dt>
                                <dd class="mt-1 text-slate-200">{{ number_format((float) $payment->amount_paid, 2) }}</dd>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-3">
                                <dt class="text-[11px] uppercase tracking-[0.2em] text-slate-500">{{ __('Method') }}</dt>
                                <dd class="mt-1 text-slate-200">{{ __(ucfirst($payment->payment_method)) }}</dd>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-3">
                                <dt class="text-[11px] uppercase tracking-[0.2em] text-slate-500">{{ __('Status') }}</dt>
                                <dd class="mt-1 text-slate-200">{{ __(ucfirst($payment->status)) }}</dd>
                            </div>
                        </dl>
                    </article>
                @empty
                    <div class="rounded-[1.5rem] border border-white/10 bg-slate-950/40 p-6 text-center text-sm text-slate-400">{{ __('No payment history found.') }}</div>
                @endforelse
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full divide-y divide-white/10">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-[0.24em] text-slate-400">
                            <th class="px-4 py-3">{{ __('Month') }}</th>
                            <th class="px-4 py-3">{{ __('Paid') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3">{{ __('Method') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($payments as $payment)
                            <tr>
                                <td class="px-4 py-4 text-sm text-slate-200">
                                    {{ optional($payment->payment_month)->format('M Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-300">
                                    {{ number_format((float) $payment->amount_paid, 2) }}
                                </td>
                                <td class="px-4 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $payment->status === 'approved' ? 'bg-emerald-400/10 text-emerald-200' : ($payment->status === 'pending' ? 'bg-amber-400/10 text-amber-200' : 'bg-rose-400/10 text-rose-200') }}">
                                        {{ __(ucfirst($payment->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-slate-300">
                                    {{ __(ucfirst($payment->payment_method)) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">{{ __('No payment history found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $payments->links() }}</div>
        </section>
    </div>
</x-app-layout>
