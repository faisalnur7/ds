@extends('layouts.admin')

@section('title', __('Operations Dashboard'))
@section('header', __('Operations Dashboard'))

@section('content')
    <div class="space-y-5 sm:space-y-6">
        <section class="rounded-3xl ccims-panel p-4 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs uppercase tracking-[0.3em] text-amber-200/80">{{ __('Admin hub') }}</p>
                    <h2 class="mt-2 font-[family-name:Space_Grotesk] text-2xl font-bold text-white sm:text-3xl">
                        {{ __('Quick access to the main operations on any screen size.') }}
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-slate-300">
                        {{ __('Use the dashboard summary to check current activity, then jump directly into members, payments, projects, checkout, and settings.') }}
                    </p>
                </div>

                <a href="{{ route('admin.members.index') }}" class="inline-flex w-full items-center justify-center rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-100 sm:w-auto">
                    {{ __('View members') }}
                </a>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
            @foreach ([
                ['label' => __('Total accounts'), 'value' => $stats['totalUsers'], 'copy' => __('All registered accounts')],
                ['label' => __('Members'), 'value' => $stats['members'], 'copy' => __('Membership records')],
                ['label' => __('Payments'), 'value' => $stats['payments'], 'copy' => __('Share collection records')],
            ] as $card)
                <div class="rounded-3xl ccims-panel p-4 sm:p-6">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400 sm:text-sm">{{ $card['label'] }}</p>
                    <p class="mt-3 font-[family-name:Space_Grotesk] text-2xl font-bold leading-none text-white sm:text-4xl">{{ number_format($card['value']) }}</p>
                    <p class="mt-3 text-xs leading-5 text-amber-200 sm:text-sm">{{ $card['copy'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
            @foreach ([
                ['label' => __('Projects'), 'value' => $stats['projects']],
                ['label' => __('Profit distributions'), 'value' => $stats['profits']],
                ['label' => __('Expenses'), 'value' => $stats['expenses']],
                ['label' => __('Expense categories'), 'value' => $stats['expenseCategories']],
                ['label' => __('Checkout requests'), 'value' => $stats['checkouts']],
                ['label' => __('Settings rows'), 'value' => $stats['settings']],
            ] as $card)
                <div class="rounded-3xl ccims-panel p-4 sm:p-6">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400 sm:text-sm">{{ $card['label'] }}</p>
                    <p class="mt-3 font-[family-name:Space_Grotesk] text-xl font-bold leading-none text-white sm:text-3xl">{{ number_format($card['value']) }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.35fr_0.9fr]">
            <div class="rounded-3xl ccims-panel p-4 sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">{{ __('Recent accounts') }}</h2>
                        <p class="mt-1 text-sm text-slate-400">{{ __('A quick snapshot of the latest activity.') }}</p>
                    </div>
                    <a href="{{ route('admin.members.index') }}" class="inline-flex w-full items-center justify-center rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/5 sm:w-auto">
                        {{ __('View all') }}
                    </a>
                </div>

                <div class="mt-6 space-y-3 md:hidden">
                    @foreach ($recentUsers as $user)
                        <article class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="truncate font-medium text-white">{{ $user->name }}</div>
                                    <div class="truncate text-sm text-slate-400">{{ $user->email }}</div>
                                </div>
                                <span @class([
                                    'inline-flex shrink-0 rounded-full px-3 py-1 text-xs font-semibold',
                                    'bg-amber-400/10 text-amber-200' => $user->isAdmin(),
                                    'bg-white/5 text-slate-300' => ! $user->isAdmin(),
                                ])>
                                    {{ __($user->isAdmin() ? 'Admin' : 'Member') }}
                                </span>
                            </div>

                            <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <dt class="text-slate-500">{{ __('Status') }}</dt>
                                    <dd class="mt-1 inline-flex rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-200">
                                        {{ __('Active') }}
                                    </dd>
                                </div>
                                <div class="text-right">
                                    <dt class="text-slate-500">{{ __('Joined') }}</dt>
                                    <dd class="mt-1 text-slate-200">{{ $user->created_at->format('M j, Y') }}</dd>
                                </div>
                            </dl>
                        </article>
                    @endforeach
                </div>

                <div class="mt-6 hidden overflow-hidden rounded-2xl border border-white/10 md:block">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead class="bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('User') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Role') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Status') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">{{ __('Joined') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10 bg-slate-950/40">
                            @foreach ($recentUsers as $user)
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="font-medium text-white">{{ $user->name }}</div>
                                        <div class="text-sm text-slate-400">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span @class([
                                            'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                            'bg-amber-400/10 text-amber-200' => $user->isAdmin(),
                                            'bg-white/5 text-slate-300' => ! $user->isAdmin(),
                                        ])>
                                            {{ __($user->isAdmin() ? 'Admin' : 'Member') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-200">
                                            {{ __('Active') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-300">
                                        {{ $user->created_at->format('M j, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl ccims-panel p-4 sm:p-6">
                    <p class="text-sm font-medium uppercase tracking-[0.25em] text-amber-200/80">{{ __('Quick actions') }}</p>
                    <h2 class="mt-3 font-[family-name:Space_Grotesk] text-2xl font-bold text-white">{{ __('Manage the operations center') }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-300">
                        {{ __('The hub is linked to every core module so admins can move directly into members, payments, expenses, projects, checkout, audit, and settings.') }}
                    </p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <a href="{{ route('admin.members.index') }}" class="rounded-full bg-white px-4 py-2 text-center text-sm font-semibold text-slate-950 transition hover:bg-amber-100">
                            {{ __('Member directory') }}
                        </a>
                        <a href="{{ route('profile.edit') }}" class="rounded-full border border-white/10 px-4 py-2 text-center text-sm font-semibold text-white transition hover:bg-white/5">
                            {{ __('Edit profile') }}
                        </a>
                    </div>
                </div>

                <div class="rounded-3xl ccims-panel p-4 sm:p-6">
                    <p class="text-sm font-medium uppercase tracking-[0.25em] text-slate-400">{{ __('Session') }}</p>
                    <div class="mt-4 space-y-3 text-sm text-slate-300">
                        <div class="flex items-center justify-between">
                            <span>{{ __('Current user') }}</span>
                            <span class="font-medium text-white">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>{{ __('Access level') }}</span>
                            <span class="font-medium text-white">{{ auth()->user()->isAdmin() ? __('Administrator') : __('Member') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>{{ __('Panel status') }}</span>
                            <span class="font-medium text-emerald-300">{{ __('Online') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
