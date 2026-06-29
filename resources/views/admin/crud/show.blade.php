@extends('layouts.admin')

@section('title', __($title))
@section('header', __($title))

@section('content')
    <div class="{{ $showContainerClass ?? 'mx-auto max-w-4xl space-y-6' }}">
        <section class="rounded-[2rem] ccims-panel-strong p-6 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __('Details') }}</p>
                    <h2 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">{{ __($title) }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-400">{{ __($description) }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @if ($canEdit)
                        <a href="{{ route("admin.{$routePrefix}.edit", $record) }}" class="rounded-full border border-amber-300/20 bg-amber-400/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-400/20">{{ __('Edit') }}</a>
                    @endif
                    <a href="{{ route("admin.{$routePrefix}.index") }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5">{{ __('Back') }}</a>
                </div>
            </div>
        </section>

        <section class="rounded-[2rem] ccims-panel p-6 sm:p-8">
            <dl class="grid gap-4 sm:grid-cols-2">
                @foreach ($fields as $field)
                    <div class="{{ ($field['span'] ?? 1) === 2 ? 'sm:col-span-2' : '' }} rounded-2xl border border-white/10 bg-white/5 p-4">
                        <dt class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ __($field['label']) }}</dt>
                        <dd class="mt-2 text-sm text-white">
                            @php
                                $value = data_get($record, $field['name']);
                            @endphp
                            @if (($field['type'] ?? 'text') === 'toggle')
                                {{ __($value ? 'On' : 'Off') }}
                            @elseif (($field['type'] ?? 'text') === 'multiselect')
                                {{ $value instanceof \Illuminate\Support\Collection ? $value->pluck('name')->join(', ') : collect($value ?? [])->join(', ') }}
                            @elseif (($field['type'] ?? 'text') === 'grouped-multiselect')
                                @php
                                    $permissions = $value instanceof \Illuminate\Support\Collection ? $value : collect($value ?? []);
                                    $groupedPermissions = $permissions->groupBy(fn ($permission) => $permission->group_name ?: 'other');
                                @endphp
                                @if ($groupedPermissions->isNotEmpty())
                                    <div class="space-y-4">
                                        @foreach ($groupedPermissions as $groupName => $items)
                                            <div class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-200/80">{{ __(\Illuminate\Support\Str::headline(str_replace(['_', '-'], ' ', $groupName))) }}</p>
                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    @foreach ($items as $permission)
                                                        <span class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-medium text-slate-200">
                                                            {{ $permission->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-400">{{ __('—') }}</span>
                                @endif
                            @elseif (($field['type'] ?? 'text') === 'computed-date')
                                {{ optional($value)->format('M j, Y') ?? '—' }}
                            @elseif (($field['type'] ?? 'text') === 'date')
                                {{ optional($value)->format('M j, Y') ?? '—' }}
                            @elseif (($field['type'] ?? 'text') === 'datetime')
                                {{ optional($value)->format('M j, Y g:i A') ?? '—' }}
                            @elseif (($field['type'] ?? 'text') === 'money')
                                {{ number_format((float) $value, 2) }}
                            @else
                                {{ is_string($value) ? __($value) : ($value ?: '—') }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </section>

        @if (! empty($showContext['actions']))
            <section class="rounded-[2rem] ccims-panel p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __('Actions') }}</p>
                        <h3 class="mt-2 font-[family-name:Space_Grotesk] text-2xl font-bold text-white">{{ __('Record workflow') }}</h3>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($showContext['actions'] as $action)
                            @if (! empty($action['href']))
                                <a href="{{ $action['href'] }}" class="rounded-full {{ $action['buttonClass'] ?? 'bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300' }}">
                                    {{ $action['label'] }}
                                </a>
                            @else
                                <form method="POST" action="{{ $action['action'] }}">
                                    @csrf
                                    @if (($action['method'] ?? 'POST') !== 'POST')
                                        @method($action['method'])
                                    @endif
                                    <button type="submit" class="rounded-full {{ $action['buttonClass'] ?? 'bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300' }}">
                                        {{ $action['label'] }}
                                    </button>
                                </form>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if (! empty($showContext['summary']))
            <section class="rounded-[2rem] ccims-panel p-6 sm:p-8">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __('Project Breakdown') }}</p>
                        <h3 class="mt-2 font-[family-name:Space_Grotesk] text-2xl font-bold text-white">{{ __('Member investments') }}</h3>
                    </div>
                </div>

                <dl class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($showContext['summary'] as $item)
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <dt class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ __($item['label']) }}</dt>
                            <dd class="mt-2 text-lg font-semibold text-white">
                                @if (($item['type'] ?? 'text') === 'money')
                                    {{ number_format((float) $item['value'], 2) }}
                                @else
                                    {{ $item['value'] }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>

                @if (! empty($showContext['members']))
                    <div class="mt-8 overflow-x-auto">
                        <table class="min-w-full divide-y divide-white/10">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-[0.24em] text-slate-400">
                                    <th class="px-4 py-3">{{ __('Member') }}</th>
                                    <th class="px-4 py-3">{{ __('Code') }}</th>
                                    <th class="px-4 py-3">{{ __('Invested Amount') }}</th>
                                    <th class="px-4 py-3">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach ($showContext['members'] as $projectMember)
                                    <tr>
                                        <td class="px-4 py-4 text-sm text-white">{{ $projectMember->member?->full_name ?? '—' }}</td>
                                        <td class="px-4 py-4 text-sm text-slate-300">{{ $projectMember->member?->member_code ?? '—' }}</td>
                                        <td class="px-4 py-4 text-sm text-slate-300">{{ number_format((float) $projectMember->allocated_share_amount, 2) }}</td>
                                        <td class="px-4 py-4 text-sm text-slate-300">
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $projectMember->is_active ? 'bg-emerald-400/10 text-emerald-200' : 'bg-rose-400/10 text-rose-200' }}">
                                                {{ __($projectMember->is_active ? 'Active' : 'Inactive') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="mt-6 text-sm text-slate-400">{{ __('No members are linked to this project yet.') }}</p>
                @endif
            </section>
        @endif

        @if (array_key_exists('share_history', $showContext ?? []))
            <section class="rounded-[2rem] ccims-panel p-6 sm:p-8">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __('Share History') }}</p>
                        <h3 class="mt-2 font-[family-name:Space_Grotesk] text-2xl font-bold text-white">{{ __('Share change timeline') }}</h3>
                    </div>
                    <p class="text-sm text-slate-400">{{ __('A chronological audit trail of share count changes.') }}</p>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($showContext['share_history'] as $entry)
                        <article class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-white">
                                        @if ($entry->previous_share_number === null)
                                            Initial share allocation: {{ number_format((int) $entry->share_number) }} shares
                                        @else
                                            {{ number_format((int) $entry->previous_share_number) }} shares → {{ number_format((int) $entry->share_number) }} shares
                                        @endif
                                    </p>
                                    <p class="mt-1 text-sm text-slate-400">
                                        {{ optional($entry->changed_at)->format('M j, Y g:i A') ?? '—' }}
                                        @if ($entry->changedBy)
                                            · by {{ $entry->changedBy->name }}
                                        @endif
                                    </p>
                                </div>
                                <div class="grid gap-2 sm:grid-cols-3 lg:min-w-[320px]">
                                    <div class="rounded-xl border border-white/10 bg-slate-950/30 px-3 py-2">
                                        <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">{{ __('Share value') }}</p>
                                        <p class="mt-1 text-sm font-semibold text-white">{{ $entry->share_value_per_share !== null ? number_format((float) $entry->share_value_per_share, 2) : '—' }}</p>
                                    </div>
                                    <div class="rounded-xl border border-white/10 bg-slate-950/30 px-3 py-2">
                                        <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">{{ __('Share cost') }}</p>
                                        <p class="mt-1 text-sm font-semibold text-white">{{ $entry->share_cost_per_share !== null ? number_format((float) $entry->share_cost_per_share, 2) : '—' }}</p>
                                    </div>
                                    <div class="rounded-xl border border-white/10 bg-slate-950/30 px-3 py-2">
                                        <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">{{ __('Monthly total') }}</p>
                                        <p class="mt-1 text-sm font-semibold text-white">{{ $entry->monthly_amount !== null ? number_format((float) $entry->monthly_amount, 2) : '—' }}</p>
                                    </div>
                                </div>
                            </div>

                            @if ($entry->note)
                                <p class="mt-4 text-sm leading-6 text-slate-300">{{ $entry->note }}</p>
                            @endif
                        </article>
                    @empty
                        <p class="text-sm text-slate-400">{{ __('No share history has been recorded yet.') }}</p>
                    @endforelse
                </div>
            </section>
        @endif
    </div>
@endsection
