@extends('layouts.admin')

@section('title', __($title))
@section('header', __($title))

@section('content')
    <div class="space-y-6">
        <section class="overflow-hidden rounded-[2rem] border border-white/10 bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900 p-6 shadow-2xl shadow-slate-950/40 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __('Settings') }}</p>
                    <h2 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">{{ __($title) }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-400">{{ __($description) }}</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">
                    <span class="font-semibold text-white">{{ count($definitions) }}</span>
                    {{ __('operational controls') }}
                </div>
            </div>
        </section>

        @if (session('status') === 'updated')
            <div class="rounded-3xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200">
                {{ __('Setting saved successfully.') }}
            </div>
        @endif

        <div class="grid gap-5 xl:grid-cols-2">
            @foreach ($definitions as $key => $definition)
                @php
                    $record = $records[$key] ?? null;
                    $value = old('setting_key') === $key ? old('value', $record?->value) : $record?->value;
                    $valueType = $definition['value_type'] ?? 'string';
                    $inputType = $definition['type'] ?? 'text';
                @endphp

                <section class="rounded-[2rem] border border-white/10 bg-slate-950/80 p-6 shadow-lg shadow-slate-950/30">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">{{ $key }}</p>
                            <h3 class="font-[family-name:Space_Grotesk] text-2xl font-bold text-white">{{ __($definition['label']) }}</h3>
                            <p class="text-sm leading-6 text-slate-400">{{ __($definition['description']) }}</p>
                        </div>

                        @if ($inputType === 'toggle')
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ filter_var($record?->value ?? false, FILTER_VALIDATE_BOOLEAN) ? 'bg-emerald-400/10 text-emerald-200' : 'bg-slate-800 text-slate-300' }}">
                                {{ __(filter_var($record?->value ?? false, FILTER_VALIDATE_BOOLEAN) ? 'Enabled' : 'Disabled') }}
                            </span>
                        @elseif ($inputType === 'number')
                            <span class="inline-flex items-center rounded-full bg-amber-400/10 px-3 py-1 text-xs font-semibold text-amber-200">
                                {{ __('Numeric') }}
                            </span>
                        @endif
                    </div>

                    @if ($record)
                        <form method="POST" action="{{ route('admin.settings.update', $record) }}" class="mt-6 space-y-4">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="setting_key" value="{{ $key }}">

                        @if ($inputType === 'toggle')
                                <input type="hidden" name="value" value="0">
                                <label class="flex cursor-pointer items-center justify-between gap-4 rounded-3xl border border-white/10 bg-white/5 px-4 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-white">{{ __('Toggle setting') }}</p>
                                        <p class="text-xs text-slate-400">{{ __($definition['help']) }}</p>
                                    </div>
                                    <input
                                        type="checkbox"
                                        name="value"
                                        value="1"
                                        class="h-5 w-5 rounded border-white/10 bg-white/5 text-amber-400 focus:ring-amber-400"
                                        @checked(filter_var($record?->value ?? false, FILTER_VALIDATE_BOOLEAN))
                                        @unless($canUpdate) disabled @endunless
                                    >
                                </label>
                            @else
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-slate-200" for="setting-{{ $key }}">
                                        {{ __($definition['label']) }}
                                    </label>
                                    <div class="flex items-stretch gap-3">
                                        <input
                                            id="setting-{{ $key }}"
                                            name="value"
                                            type="{{ $inputType === 'number' ? 'number' : 'text' }}"
                                            value="{{ $value }}"
                                            class="ccims-input flex-1"
                                            @if ($inputType === 'number' && isset($definition['min'])) min="{{ $definition['min'] }}" @endif
                                            @if ($inputType === 'number' && isset($definition['max'])) max="{{ $definition['max'] }}" @endif
                                            @if ($inputType === 'number' && isset($definition['step'])) step="{{ $definition['step'] }}" @endif
                                            @unless($canUpdate) readonly @endunless
                                        >
                                        @if (! empty($definition['suffix']))
                                            <div class="inline-flex items-center rounded-2xl border border-white/10 bg-white/5 px-4 text-sm text-slate-300">
                                                {{ __($definition['suffix']) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center justify-between gap-4">
                                <div class="text-xs text-slate-500">
                                    @if (! empty($definition['help']))
                                        {{ __($definition['help']) }}
                                    @endif
                                </div>
                                @if ($canUpdate)
                                    <button type="submit" class="rounded-full bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                                        {{ __('Save changes') }}
                                    </button>
                                @else
                                    <span class="text-sm text-slate-500">{{ __('Read only') }}</span>
                                @endif
                            </div>

                            @error('value')
                                <p class="text-xs text-rose-300">{{ $message }}</p>
                            @enderror
                        </form>
                    @else
                        <div class="mt-6 rounded-3xl border border-dashed border-white/10 bg-white/5 px-4 py-5 text-sm text-slate-400">
                            {{ __('This setting is not configured yet.') }}
                        </div>
                    @endif
                </section>
            @endforeach
        </div>
    </div>
@endsection
