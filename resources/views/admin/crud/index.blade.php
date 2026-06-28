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
            <div class="overflow-x-auto">
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
