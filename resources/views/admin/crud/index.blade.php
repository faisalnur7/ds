@extends('layouts.admin')

@section('title', __($title))
@section('header', __($title))

@section('content')
    <div class="space-y-6">
        <section class="rounded-[2rem] ccims-panel-strong p-6 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __('CRUD') }}</p>
                    <h2 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">{{ __($title) }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-400">{{ __($description) }}</p>
                </div>
                @if ($canCreate)
                    <a href="{{ route("admin.{$routePrefix}.create") }}" class="inline-flex items-center justify-center rounded-full bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                        {{ __('Create new') }}
                    </a>
                @endif
            </div>
        </section>

        <section class="rounded-[2rem] ccims-panel p-4 sm:p-6">
            <div class="space-y-3 md:hidden">
                @forelse ($records as $record)
                    <article class="rounded-[1.5rem] border border-white/10 bg-slate-950/40 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                @php($primaryColumn = $columns[0] ?? null)
                                @if ($primaryColumn)
                                    @php($primaryValue = data_get($record, $primaryColumn['key']))
                                    <p class="text-xs uppercase tracking-[0.24em] text-slate-500">{{ __($primaryColumn['label']) }}</p>
                                    <p class="mt-1 truncate text-base font-semibold text-white">
                                        @if (($primaryColumn['type'] ?? 'text') === 'date')
                                            {{ optional($primaryValue)->format('M j, Y') ?? '—' }}
                                        @elseif (($primaryColumn['type'] ?? 'text') === 'datetime')
                                            {{ optional($primaryValue)->format('M j, Y g:i A') ?? '—' }}
                                        @elseif (($primaryColumn['type'] ?? 'text') === 'money')
                                            {{ number_format((float) $primaryValue, 2) }}
                                        @else
                                            {{ is_string($primaryValue) ? __($primaryValue) : ($primaryValue ?? '—') }}
                                        @endif
                                    </p>
                                @endif
                            </div>

                            <div class="flex shrink-0 flex-wrap justify-end gap-2">
                                @if ($canView)
                                    <a href="{{ route("admin.{$routePrefix}.show", $record) }}" class="rounded-full border border-white/10 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/5">{{ __('View') }}</a>
                                @endif
                                @if ($canEdit)
                                    <a href="{{ route("admin.{$routePrefix}.edit", $record) }}" class="rounded-full border border-amber-300/20 bg-amber-400/10 px-3 py-1.5 text-xs font-semibold text-amber-200 transition hover:bg-amber-400/20">{{ __('Edit') }}</a>
                                @endif
                            </div>
                        </div>

                        <dl class="mt-4 grid grid-cols-1 gap-3 text-sm">
                            @foreach (array_slice($columns, 1) as $column)
                                @php($value = data_get($record, $column['key']))
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-3">
                                    <dt class="text-[11px] uppercase tracking-[0.2em] text-slate-500">{{ __($column['label']) }}</dt>
                                    <dd class="mt-1 text-sm text-slate-200">
                                        @if (($column['type'] ?? 'text') === 'bool' && $column['key'] === 'is_active' && $routePrefix === 'share-settings')
                                            @if ($canUpdate)
                                                <form method="POST" action="{{ route("admin.{$routePrefix}.toggle", $record) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center gap-3 rounded-full border px-3 py-1.5 text-xs font-semibold transition {{ $value ? 'border-emerald-300/20 bg-emerald-400/10 text-emerald-200' : 'border-slate-700 bg-slate-900 text-slate-300 hover:bg-slate-800' }}"
                                                        aria-label="{{ $value ? __('Deactivate share setting') : __('Activate share setting') }}"
                                                        title="{{ $value ? __('Deactivate this share setting') : __('Activate this share setting') }}"
                                                    >
                                                        <span class="relative inline-flex h-5 w-9 items-center rounded-full transition {{ $value ? 'bg-emerald-400' : 'bg-slate-600' }}">
                                                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $value ? 'translate-x-4' : 'translate-x-0.5' }}"></span>
                                                        </span>
                                                        <span>{{ __($value ? 'Active' : 'Inactive') }}</span>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $value ? 'bg-emerald-400/10 text-emerald-200' : 'bg-rose-400/10 text-rose-200' }}">
                                                    {{ __($value ? 'Active' : 'Inactive') }}
                                                </span>
                                            @endif
                                        @elseif (($column['type'] ?? 'text') === 'bool')
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $value ? 'bg-emerald-400/10 text-emerald-200' : 'bg-rose-400/10 text-rose-200' }}">
                                                {{ __($value ? 'On' : 'Off') }}
                                            </span>
                                        @elseif (($column['type'] ?? 'text') === 'date')
                                            {{ optional($value)->format('M j, Y') ?? '—' }}
                                        @elseif (($column['type'] ?? 'text') === 'datetime')
                                            {{ optional($value)->format('M j, Y g:i A') ?? '—' }}
                                        @elseif (($column['type'] ?? 'text') === 'money')
                                            {{ number_format((float) $value, 2) }}
                                        @else
                                            {{ is_string($value) ? __($value) : $value }}
                                        @endif
                                    </dd>
                                </div>
                            @endforeach
                        </dl>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @if ($routePrefix === 'payments' && $canView)
                                <a href="{{ route('admin.payments.receipt', $record) }}" target="_blank" rel="noopener" class="rounded-full border border-sky-300/20 bg-sky-400/10 px-3 py-1.5 text-xs font-semibold text-sky-200 transition hover:bg-sky-400/20">
                                    {{ __('Print receipt') }}
                                </a>
                            @endif
                            @if ($canDelete)
                                <form method="POST" action="{{ route("admin.{$routePrefix}.destroy", $record) }}" onsubmit="return confirm(@js(__('Delete this record?')))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-full border border-rose-300/20 bg-rose-400/10 px-3 py-1.5 text-xs font-semibold text-rose-200 transition hover:bg-rose-400/20">{{ __('Delete') }}</button>
                                </form>
                            @endif
                            @if (
                                $routePrefix === 'expenses'
                                && $canApprove
                                && data_get($record, 'status') !== 'approved'
                            )
                                <form method="POST" action="{{ route("admin.{$routePrefix}.approve", $record) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded-full border border-emerald-300/20 bg-emerald-400/10 px-3 py-1.5 text-xs font-semibold text-emerald-200 transition hover:bg-emerald-400/20">
                                        {{ __('Approve') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-[1.5rem] border border-white/10 bg-slate-950/40 p-6 text-center text-sm text-slate-400">
                        {{ __('No records found.') }}
                    </div>
                @endforelse
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full divide-y divide-white/10">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-[0.24em] text-slate-400">
                            @foreach ($columns as $column)
                                <th class="px-4 py-3">{{ __($column['label']) }}</th>
                            @endforeach
                            <th class="px-4 py-3">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($records as $record)
                            <tr>
                                @foreach ($columns as $column)
                                    <td class="px-4 py-4 text-sm text-slate-300">
                                        @php($value = data_get($record, $column['key']))
                                        @if (($column['type'] ?? 'text') === 'bool' && $column['key'] === 'is_active' && $routePrefix === 'share-settings')
                                            @if ($canUpdate)
                                                <form method="POST" action="{{ route("admin.{$routePrefix}.toggle", $record) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center gap-3 rounded-full border px-3 py-1.5 text-xs font-semibold transition {{ $value ? 'border-emerald-300/20 bg-emerald-400/10 text-emerald-200' : 'border-slate-700 bg-slate-900 text-slate-300 hover:bg-slate-800' }}"
                                                        aria-label="{{ $value ? __('Deactivate share setting') : __('Activate share setting') }}"
                                                        title="{{ $value ? __('Deactivate this share setting') : __('Activate this share setting') }}"
                                                    >
                                                        <span class="relative inline-flex h-5 w-9 items-center rounded-full transition {{ $value ? 'bg-emerald-400' : 'bg-slate-600' }}">
                                                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $value ? 'translate-x-4' : 'translate-x-0.5' }}"></span>
                                                        </span>
                                                        <span>{{ __($value ? 'Active' : 'Inactive') }}</span>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $value ? 'bg-emerald-400/10 text-emerald-200' : 'bg-rose-400/10 text-rose-200' }}">
                                                    {{ __($value ? 'Active' : 'Inactive') }}
                                                </span>
                                            @endif
                                        @elseif (($column['type'] ?? 'text') === 'bool')
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $value ? 'bg-emerald-400/10 text-emerald-200' : 'bg-rose-400/10 text-rose-200' }}">
                                                {{ __($value ? 'On' : 'Off') }}
                                            </span>
                                        @elseif (($column['type'] ?? 'text') === 'date')
                                            {{ optional($value)->format('M j, Y') ?? '—' }}
                                        @elseif (($column['type'] ?? 'text') === 'datetime')
                                            {{ optional($value)->format('M j, Y g:i A') ?? '—' }}
                                        @elseif (($column['type'] ?? 'text') === 'money')
                                            {{ number_format((float) $value, 2) }}
                                        @else
                                            {{ is_string($value) ? __($value) : $value }}
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        @if ($canView)
                                            <a href="{{ route("admin.{$routePrefix}.show", $record) }}" class="rounded-full border border-white/10 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/5">{{ __('View') }}</a>
                                        @endif
                                        @if ($canEdit)
                                            <a href="{{ route("admin.{$routePrefix}.edit", $record) }}" class="rounded-full border border-amber-300/20 bg-amber-400/10 px-3 py-1.5 text-xs font-semibold text-amber-200 transition hover:bg-amber-400/20">{{ __('Edit') }}</a>
                                        @endif
                                        @if ($routePrefix === 'payments' && $canView)
                                            <a href="{{ route('admin.payments.receipt', $record) }}" target="_blank" rel="noopener" class="rounded-full border border-sky-300/20 bg-sky-400/10 px-3 py-1.5 text-xs font-semibold text-sky-200 transition hover:bg-sky-400/20">
                                                {{ __('Print receipt') }}
                                            </a>
                                        @endif
                                        @if ($canDelete)
                                            <form method="POST" action="{{ route("admin.{$routePrefix}.destroy", $record) }}" onsubmit="return confirm(@js(__('Delete this record?')))">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-full border border-rose-300/20 bg-rose-400/10 px-3 py-1.5 text-xs font-semibold text-rose-200 transition hover:bg-rose-400/20">{{ __('Delete') }}</button>
                                            </form>
                                        @endif
                                        @if (
                                            $routePrefix === 'expenses'
                                            && $canApprove
                                            && data_get($record, 'status') !== 'approved'
                                        )
                                            <form method="POST" action="{{ route("admin.{$routePrefix}.approve", $record) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="rounded-full border border-emerald-300/20 bg-emerald-400/10 px-3 py-1.5 text-xs font-semibold text-emerald-200 transition hover:bg-emerald-400/20">
                                                    {{ __('Approve') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) + 1 }}" class="px-4 py-8 text-center text-sm text-slate-400">{{ __('No records found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $records->links() }}</div>
        </section>
    </div>
@endsection
