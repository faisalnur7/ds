@extends('layouts.admin')

@section('title', $title)
@section('header', $title)

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <section class="rounded-[2rem] ccims-panel-strong p-6 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">Details</p>
                    <h2 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">{{ $title }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-400">{{ $description }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route("admin.{$routePrefix}.edit", $record) }}" class="rounded-full border border-amber-300/20 bg-amber-400/10 px-4 py-2 text-sm font-semibold text-amber-200 transition hover:bg-amber-400/20">Edit</a>
                    <a href="{{ route("admin.{$routePrefix}.index") }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5">Back</a>
                </div>
            </div>
        </section>

        <section class="rounded-[2rem] ccims-panel p-6 sm:p-8">
            <dl class="grid gap-4 sm:grid-cols-2">
                @foreach ($fields as $field)
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <dt class="text-xs uppercase tracking-[0.24em] text-slate-400">{{ $field['label'] }}</dt>
                        <dd class="mt-2 text-sm text-white">
                            @php($value = data_get($record, $field['name']))
                            @if (($field['type'] ?? 'text') === 'toggle')
                                {{ $value ? 'On' : 'Off' }}
                            @elseif (($field['type'] ?? 'text') === 'multiselect')
                                {{ $value instanceof \Illuminate\Support\Collection ? $value->pluck('name')->join(', ') : collect($value ?? [])->join(', ') }}
                            @elseif (($field['type'] ?? 'text') === 'money')
                                {{ number_format((float) $value, 2) }}
                            @else
                                {{ $value ?: '—' }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </section>
    </div>
@endsection
