@extends('layouts.admin')

@section('title', __($title))
@section('header', __($title))

@section('content')
    <div class="space-y-6">
        <section class="rounded-[2rem] ccims-panel-strong p-6 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __('Directory') }}</p>
                    <h2 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">{{ $title }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-400">{{ $description }}</p>
                </div>
                <a href="{{ route('admin.members.create') }}" class="inline-flex items-center justify-center rounded-full bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                    {{ __('Add member') }}
                </a>
            </div>
        </section>

        <section class="rounded-[2rem] ccims-panel p-6">
            <form method="GET" action="{{ route('admin.members.index') }}" class="grid gap-4 lg:grid-cols-12 lg:items-end">
                <div class="min-w-0 lg:col-span-3">
                    <label class="mb-2 block text-sm font-medium text-slate-200" for="q">{{ __('Search') }}</label>
                    <input id="q" name="q" type="search" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('Search by ID, name, or mobile number') }}" class="ccims-input">
                </div>
                <div class="min-w-0 lg:col-span-3">
                    <label class="mb-2 block text-sm font-medium text-slate-200" for="jimmadar_id">{{ __('Jimmadar') }}</label>
                    <select id="jimmadar_id" name="jimmadar_id" class="ccims-input">
                        <option value="">{{ __('All Jimmadars') }}</option>
                        @foreach ($jimmadars as $id => $name)
                            <option value="{{ $id }}" @selected(($filters['jimmadar_id'] ?? '') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-0 lg:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-slate-200" for="join_from">{{ __('Join From') }}</label>
                    <input id="join_from" name="join_from" type="date" value="{{ $filters['join_from'] ?? '' }}" class="ccims-input">
                </div>
                <div class="min-w-0 lg:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-slate-200" for="join_to">{{ __('Join To') }}</label>
                    <input id="join_to" name="join_to" type="date" value="{{ $filters['join_to'] ?? '' }}" class="ccims-input">
                </div>
                <div class="lg:col-span-2 flex items-end">
                    <div class="flex w-full flex-wrap gap-2 lg:justify-end">
                        <button type="submit" class="inline-flex shrink-0 items-center justify-center rounded-full bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">{{ __('Filter') }}</button>
                        <a href="{{ route('admin.members.index') }}" class="inline-flex shrink-0 items-center justify-center rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5">{{ __('Reset') }}</a>
                    </div>
                </div>
            </form>
        </section>

        <section class="rounded-[2rem] ccims-panel p-4 sm:p-6">
            <div class="space-y-3 md:hidden">
                @forelse ($members as $member)
                    <details class="group rounded-[1.5rem] border border-white/10 bg-slate-950/40 p-4 transition open:bg-slate-950/60">
                        <summary class="flex cursor-pointer list-none items-start justify-between gap-4 [&::-webkit-details-marker]:hidden">
                            <div class="min-w-0">
                                <p class="text-xs uppercase tracking-[0.24em] text-slate-500">{{ __('Code') }}</p>
                                <p class="mt-1 truncate text-base font-semibold text-white">{{ $member->member_code }}</p>
                                <p class="mt-1 truncate text-sm text-slate-300">{{ $member->full_name }}</p>
                            </div>

                            <div class="flex shrink-0 items-start gap-3">
                                <span @class([
                                    'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                    'bg-emerald-400/10 text-emerald-200' => $member->membership_status === 'active',
                                    'bg-rose-400/10 text-rose-200' => $member->membership_status !== 'active',
                                ])>
                                    {{ __(ucfirst(str_replace('_', ' ', $member->membership_status))) }}
                                </span>
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-slate-400 transition duration-200 group-open:rotate-180 group-open:text-slate-200" viewBox="0 0 20 20" fill="none" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 8l5 5 5-5" />
                                </svg>
                            </div>
                        </summary>

                        <dl class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-3">
                                <dt class="text-[11px] uppercase tracking-[0.2em] text-slate-500">{{ __('Shares') }}</dt>
                                <dd class="mt-1 text-sm text-slate-200">{{ number_format((int) $member->share_number) }} {{ __('shares') }}</dd>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-3">
                                <dt class="text-[11px] uppercase tracking-[0.2em] text-slate-500">{{ __('Jimmadar') }}</dt>
                                <dd class="mt-1 text-sm text-slate-200">{{ $member->user?->name ?? '—' }}</dd>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-3">
                                <dt class="text-[11px] uppercase tracking-[0.2em] text-slate-500">{{ __('Mobile') }}</dt>
                                <dd class="mt-1 text-sm text-slate-200">{{ $member->phone_search ?: '—' }}</dd>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-3">
                                <dt class="text-[11px] uppercase tracking-[0.2em] text-slate-500">{{ __('Join Date') }}</dt>
                                <dd class="mt-1 text-sm text-slate-200">{{ optional($member->join_date)->format('M j, Y') }}</dd>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-3">
                                <dt class="text-[11px] uppercase tracking-[0.2em] text-slate-500">{{ __('Eligible On') }}</dt>
                                <dd class="mt-1 text-sm text-slate-200">{{ optional($member->checkout_eligible_on)->format('M j, Y') ?? '—' }}</dd>
                            </div>
                        </dl>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('admin.members.show', $member) }}" class="rounded-full border border-white/10 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/5">{{ __('View') }}</a>
                            <a href="{{ route('admin.members.edit', $member) }}" class="rounded-full border border-amber-300/20 bg-amber-400/10 px-3 py-1.5 text-xs font-semibold text-amber-200 transition hover:bg-amber-400/20">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('admin.members.destroy', $member) }}" onsubmit="return confirm(@js(__('Delete this member?')))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-full border border-rose-300/20 bg-rose-400/10 px-3 py-1.5 text-xs font-semibold text-rose-200 transition hover:bg-rose-400/20">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </details>
                @empty
                    <div class="rounded-[1.5rem] border border-white/10 bg-slate-950/40 p-6 text-center text-sm text-slate-400">
                        {{ __('No members found.') }}
                    </div>
                @endforelse
            </div>

            <div class="hidden md:block">
                <table class="w-full table-fixed divide-y divide-white/10">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-[0.24em] text-slate-400">
                            <th class="w-28 px-4 py-3">{{ __('Code') }}</th>
                            <th class="px-4 py-3">{{ __('Name') }}</th>
                            <th class="w-28 px-4 py-3">{{ __('Shares') }}</th>
                            <th class="px-4 py-3">{{ __('Jimmadar') }}</th>
                            <th class="px-4 py-3">{{ __('Mobile') }}</th>
                            <th class="w-36 px-4 py-3">{{ __('Join Date') }}</th>
                            <th class="w-36 px-4 py-3">{{ __('Eligible On') }}</th>
                            <th class="w-28 px-4 py-3">{{ __('Status') }}</th>
                            <th class="w-44 px-4 py-3">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($members as $member)
                            <tr>
                                <td class="px-4 py-4 text-sm text-white break-words">{{ $member->member_code }}</td>
                                <td class="px-4 py-4 text-sm text-slate-200 break-words">{{ $member->full_name }}</td>
                                <td class="px-4 py-4 text-sm text-slate-300">{{ number_format((int) $member->share_number) }} {{ __('shares') }}</td>
                                <td class="px-4 py-4 text-sm text-slate-300 break-words">{{ $member->user?->name ?? '—' }}</td>
                                <td class="px-4 py-4 text-sm text-slate-300 break-words">{{ $member->phone_search ?: '—' }}</td>
                                <td class="px-4 py-4 text-sm text-slate-300">{{ optional($member->join_date)->format('M j, Y') }}</td>
                                <td class="px-4 py-4 text-sm text-slate-300">{{ optional($member->checkout_eligible_on)->format('M j, Y') ?? '—' }}</td>
                                <td class="px-4 py-4 text-sm text-slate-300">
                                    <span @class([
                                        'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                        'bg-emerald-400/10 text-emerald-200' => $member->membership_status === 'active',
                                        'bg-rose-400/10 text-rose-200' => $member->membership_status !== 'active',
                                    ])>
                                        {{ __(ucfirst(str_replace('_', ' ', $member->membership_status))) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.members.show', $member) }}" class="rounded-full border border-white/10 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/5">{{ __('View') }}</a>
                                        <a href="{{ route('admin.members.edit', $member) }}" class="rounded-full border border-amber-300/20 bg-amber-400/10 px-3 py-1.5 text-xs font-semibold text-amber-200 transition hover:bg-amber-400/20">{{ __('Edit') }}</a>
                                        <form method="POST" action="{{ route('admin.members.destroy', $member) }}" onsubmit="return confirm(@js(__('Delete this member?')))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-full border border-rose-300/20 bg-rose-400/10 px-3 py-1.5 text-xs font-semibold text-rose-200 transition hover:bg-rose-400/20">{{ __('Delete') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-sm text-slate-400">{{ __('No members found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $members->links() }}
            </div>
        </section>
    </div>
@endsection
