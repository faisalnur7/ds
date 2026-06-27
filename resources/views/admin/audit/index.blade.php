@extends('layouts.admin')

@section('title', 'Audit Logs')
@section('header', 'Audit Logs')

@section('content')
    <div class="space-y-6">
        <section class="rounded-[2rem] ccims-panel-strong p-6 sm:p-8">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">Compliance</p>
            <h2 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">Audit Logs</h2>
            <p class="mt-3 text-sm leading-6 text-slate-400">Read-only history of important system changes.</p>
        </section>

        <section class="rounded-[2rem] ccims-panel p-4 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-[0.24em] text-slate-400">
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Actor</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($records as $record)
                            <tr>
                                <td class="px-4 py-4 text-sm text-white">{{ $record->action }}</td>
                                <td class="px-4 py-4 text-sm text-slate-300">{{ $record->user?->name ?? 'System' }}</td>
                                <td class="px-4 py-4 text-sm text-slate-300">{{ class_basename($record->auditable_type) }}</td>
                                <td class="px-4 py-4 text-sm text-slate-400">{{ $record->created_at?->format('M j, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400">No audit records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $records->links() }}</div>
        </section>
    </div>
@endsection
