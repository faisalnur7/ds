@extends('layouts.admin')

@section('title', $title)
@section('header', $title)

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <section class="rounded-[2rem] ccims-panel-strong p-6 sm:p-8">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">Form</p>
            <h2 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">{{ $title }}</h2>
            <p class="mt-3 text-sm leading-6 text-slate-400">{{ $description }}</p>
        </section>

        <form method="POST" action="{{ $action }}" class="space-y-6 rounded-[2rem] ccims-panel p-6 sm:p-8">
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="grid gap-5 sm:grid-cols-2">
                @foreach ($fields as $field)
                    <div class="{{ ($field['span'] ?? 1) === 2 ? 'sm:col-span-2' : '' }}">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="{{ $field['name'] }}">{{ $field['label'] }}</label>
                        @php
                            $rawValue = old($field['name'], data_get($record, $field['name']));
                            if ($rawValue instanceof \Illuminate\Support\Carbon) {
                                $rawValue = match ($field['type'] ?? 'text') {
                                    'date' => $rawValue->format('Y-m-d'),
                                    'datetime-local' => $rawValue->format('Y-m-d\\TH:i'),
                                    default => $rawValue->format('Y-m-d H:i:s'),
                                };
                            } elseif (in_array($field['type'] ?? 'text', ['date', 'datetime-local'], true) && is_string($rawValue) && str_contains($rawValue, ' ')) {
                                $rawValue = \Illuminate\Support\Carbon::parse($rawValue)->format($field['type'] === 'datetime-local' ? 'Y-m-d\\TH:i' : 'Y-m-d');
                            }
                        @endphp
                        @if (($field['type'] ?? 'text') === 'textarea')
                            <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" rows="4" class="ccims-input">{{ $rawValue }}</textarea>
                        @elseif (($field['type'] ?? 'text') === 'select')
                            <select id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="ccims-input">
                                @foreach ($field['options'] as $value => $label)
                                    <option value="{{ $value }}" @selected($rawValue == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        @elseif (($field['type'] ?? 'text') === 'toggle')
                            <label class="inline-flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                <input type="checkbox" name="{{ $field['name'] }}" value="1" @checked((bool) $rawValue) class="h-5 w-5 rounded border-white/10 bg-white/5 text-amber-400">
                                <span class="text-sm text-slate-200">{{ $field['helper'] ?? 'Toggle this setting' }}</span>
                            </label>
                        @elseif (($field['type'] ?? 'text') === 'multiselect')
                            <div class="grid gap-2 rounded-2xl border border-white/10 bg-white/5 p-4">
                                @php($selected = $rawValue instanceof \Illuminate\Support\Collection ? $rawValue->modelKeys() : collect($rawValue ?? [])->map(fn ($value) => is_object($value) && isset($value->id) ? $value->id : $value)->all())
                                @foreach ($field['options'] as $value => $label)
                                    <label class="inline-flex items-center gap-3 text-sm text-slate-200">
                                        <input type="checkbox" name="{{ $field['name'] }}[]" value="{{ $value }}" @checked(in_array((string) $value, $selected, true)) class="h-4 w-4 rounded border-white/10 bg-white/5 text-amber-400">
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <input id="{{ $field['name'] }}" type="{{ $field['type'] ?? 'text' }}" name="{{ $field['name'] }}" value="{{ ($field['type'] ?? 'text') === 'password' ? '' : $rawValue }}" class="ccims-input">
                        @endif
                        @if (! empty($field['help']))
                            <p class="mt-2 text-xs text-slate-400">{{ $field['help'] }}</p>
                        @endif
                        @error($field['name'])
                            <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between gap-4">
                <a href="{{ route("admin.{$routePrefix}.index") }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5">Back</a>
                <button type="submit" class="rounded-full bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">{{ $submitLabel }}</button>
            </div>
        </form>
    </div>
@endsection
