@extends('layouts.admin')

@section('title', __('Reports'))
@section('header', __('Reports'))

@section('content')
    <div class="space-y-6">
        <section class="rounded-3xl ccims-panel p-4 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-amber-200/80">{{ __('Ledger reports') }}</p>
                    <h2 class="mt-2 font-[family-name:Space_Grotesk] text-2xl font-bold text-white sm:text-3xl">{{ __('Project investments, payments, and expenses') }}</h2>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">
                        {{ __('This view reads from the transaction ledger so the totals stay aligned across operational modules.') }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if (auth()->user()?->hasPermission('view_expenses'))
                        <a href="{{ route('admin.expenses.index') }}" class="inline-flex items-center justify-center rounded-full border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                            {{ __('Expenses') }}
                        </a>
                    @endif
                    <a href="{{ route('admin.reports.export', request()->only(['from', 'to'])) }}" class="inline-flex items-center justify-center rounded-full bg-white px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-100">
                        {{ __('Export CSV') }}
                    </a>
                </div>
            </div>

            <form method="GET" class="mt-6 grid gap-4 md:grid-cols-3">
                <label class="block">
                    <span class="mb-2 block text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('From') }}</span>
                    <input type="date" name="from" value="{{ $filters['from'] }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition focus:border-amber-300/40">
                </label>
                <label class="block">
                    <span class="mb-2 block text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('To') }}</span>
                    <input type="date" name="to" value="{{ $filters['to'] }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition focus:border-amber-300/40">
                </label>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex w-full justify-center rounded-full bg-white px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-100">
                        {{ __('Apply filter') }}
                    </button>
                </div>
            </form>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            @foreach ([
                ['label' => __('Payments'), 'value' => $summary['payments'], 'tone' => 'text-emerald-200'],
                ['label' => __('Expenses'), 'value' => $summary['expenses'], 'tone' => 'text-rose-200'],
                ['label' => __('Share cost after expenses'), 'value' => $summary['netShareCost'], 'tone' => 'text-white'],
                ['label' => __('Project investments'), 'value' => $summary['projectInvestments'], 'tone' => 'text-amber-200'],
                ['label' => __('Net cash flow'), 'value' => $summary['netCashFlow'], 'tone' => 'text-white'],
                ['label' => __('Transactions'), 'value' => $summary['transactions'], 'tone' => 'text-sky-200'],
            ] as $card)
                <div class="rounded-3xl ccims-panel p-4 sm:p-6">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ $card['label'] }}</p>
                    <p class="mt-3 font-[family-name:Space_Grotesk] text-2xl font-bold leading-none {{ $card['tone'] }}">
                        @if ($card['label'] === __('Transactions'))
                            {{ number_format((int) $card['value']) }}
                        @else
                            {{ number_format((float) $card['value'], 2) }}
                        @endif
                    </p>
                </div>
            @endforeach
        </section>

        <section class="rounded-3xl ccims-panel p-4 sm:p-6">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h3 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">{{ __('Monthly summary') }}</h3>
                    <p class="mt-1 text-sm text-slate-400">{{ __('Share cost after expenses, plus project investments by month.') }}</p>
                </div>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-[0.24em] text-slate-400">
                            <th class="px-4 py-3">{{ __('Month') }}</th>
                            <th class="px-4 py-3">{{ __('Payments') }}</th>
                            <th class="px-4 py-3">{{ __('Expenses') }}</th>
                            <th class="px-4 py-3">{{ __('Share cost after expenses') }}</th>
                            <th class="px-4 py-3">{{ __('Project investments') }}</th>
                            <th class="px-4 py-3">{{ __('Net cash flow') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($monthlyRows as $row)
                            <tr>
                                <td class="px-4 py-4 text-sm text-white">{{ $row['month'] }}</td>
                                <td class="px-4 py-4 text-sm text-emerald-200">{{ number_format((float) $row['payments'], 2) }}</td>
                                <td class="px-4 py-4 text-sm text-rose-200">{{ number_format((float) $row['expenses'], 2) }}</td>
                                <td class="px-4 py-4 text-sm text-slate-200">{{ number_format((float) $row['netShareCost'], 2) }}</td>
                                <td class="px-4 py-4 text-sm text-amber-200">{{ number_format((float) $row['projectInvestments'], 2) }}</td>
                                <td class="px-4 py-4 text-sm text-slate-200">{{ number_format((float) $row['netCashFlow'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-400">{{ __('No transactions found for the selected range.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-3">
            <div class="rounded-3xl ccims-panel p-4 sm:p-6">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h3 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">{{ __('Payments') }}</h3>
                        <p class="mt-1 text-sm text-slate-400">{{ __('Incoming member collections.') }}</p>
                    </div>
                    <span class="text-xs uppercase tracking-[0.24em] text-emerald-200">{{ number_format($payments->count()) }}</span>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-[0.24em] text-slate-400">
                                <th class="px-4 py-3">{{ __('Member') }}</th>
                                <th class="px-4 py-3">{{ __('Amount') }}</th>
                                <th class="px-4 py-3">{{ __('Date') }}</th>
                                <th class="px-4 py-3">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($payments as $transaction)
                                <tr>
                                    <td class="px-4 py-4 text-sm text-white">{{ $transaction->member?->full_name ?? $transaction->member?->member_code ?? '—' }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-200">{{ number_format((float) $transaction->amount, 2) }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-300">{{ optional($transaction->transaction_date)->format('M j, Y') ?? '—' }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-300">{{ __(ucfirst($transaction->status)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">{{ __('No payment transactions found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl ccims-panel p-4 sm:p-6">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h3 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">{{ __('Expenses') }}</h3>
                        <p class="mt-1 text-sm text-slate-400">{{ __('Operational spending records.') }}</p>
                    </div>
                    <span class="text-xs uppercase tracking-[0.24em] text-rose-200">{{ number_format($expenses->count()) }}</span>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-[0.24em] text-slate-400">
                                <th class="px-4 py-3">{{ __('Category') }}</th>
                                <th class="px-4 py-3">{{ __('Amount') }}</th>
                                <th class="px-4 py-3">{{ __('Date') }}</th>
                                <th class="px-4 py-3">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($expenses as $transaction)
                                <tr>
                                    <td class="px-4 py-4 text-sm text-white">{{ $transaction->expenseCategory?->name ?? $transaction->description ?? '—' }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-200">{{ number_format((float) $transaction->amount, 2) }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-300">{{ optional($transaction->transaction_date)->format('M j, Y') ?? '—' }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-300">{{ __(ucfirst($transaction->status)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">{{ __('No expense transactions found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-3xl ccims-panel p-4 sm:p-6">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h3 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">{{ __('Project investments') }}</h3>
                        <p class="mt-1 text-sm text-slate-400">{{ __('Capital invested in projects.') }}</p>
                    </div>
                    <span class="text-xs uppercase tracking-[0.24em] text-amber-200">{{ number_format($projectInvestments->count()) }}</span>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-[0.24em] text-slate-400">
                                <th class="px-4 py-3">{{ __('Project') }}</th>
                                <th class="px-4 py-3">{{ __('Amount') }}</th>
                                <th class="px-4 py-3">{{ __('Date') }}</th>
                                <th class="px-4 py-3">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($projectInvestments as $transaction)
                                <tr>
                                    <td class="px-4 py-4 text-sm text-white">{{ $transaction->project?->name ?? $transaction->description ?? '—' }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-200">{{ number_format((float) $transaction->amount, 2) }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-300">{{ optional($transaction->transaction_date)->format('M j, Y') ?? '—' }}</td>
                                    <td class="px-4 py-4 text-sm text-slate-300">{{ __(ucfirst($transaction->status)) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">{{ __('No project investment transactions found.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endsection
