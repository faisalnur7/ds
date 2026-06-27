@extends('layouts.admin')

@section('title', $title)
@section('header', $title)

@section('content')
    <div class="space-y-6">
        <section class="rounded-[2rem] ccims-panel-strong p-6 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">CRUD</p>
                    <h2 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">{{ $title }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-400">{{ $description }}</p>
                </div>
                <a href="{{ route("admin.{$routePrefix}.create") }}" class="inline-flex items-center justify-center rounded-full bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                    Create new
                </a>
            </div>
        </section>

        <section class="rounded-[2rem] ccims-panel p-4 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-[0.24em] text-slate-400">
                            @foreach ($columns as $column)
                                <th class="px-4 py-3">{{ $column['label'] }}</th>
                            @endforeach
                            <th class="px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($records as $record)
                            <tr>
                                @foreach ($columns as $column)
                                    <td class="px-4 py-4 text-sm text-slate-300">
                                        @php($value = data_get($record, $column['key']))
                                        @if (($column['type'] ?? 'text') === 'bool')
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $value ? 'bg-emerald-400/10 text-emerald-200' : 'bg-rose-400/10 text-rose-200' }}">
                                                {{ $value ? 'On' : 'Off' }}
                                            </span>
                                        @elseif (($column['type'] ?? 'text') === 'money')
                                            {{ number_format((float) $value, 2) }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route("admin.{$routePrefix}.show", $record) }}" class="rounded-full border border-white/10 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/5">View</a>
                                        <a href="{{ route("admin.{$routePrefix}.edit", $record) }}" class="rounded-full border border-amber-300/20 bg-amber-400/10 px-3 py-1.5 text-xs font-semibold text-amber-200 transition hover:bg-amber-400/20">Edit</a>
                                        <form method="POST" action="{{ route("admin.{$routePrefix}.destroy", $record) }}" onsubmit="return confirm('Delete this record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-full border border-rose-300/20 bg-rose-400/10 px-3 py-1.5 text-xs font-semibold text-rose-200 transition hover:bg-rose-400/20">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) + 1 }}" class="px-4 py-8 text-center text-sm text-slate-400">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $records->links() }}</div>
        </section>
    </div>
@endsection
