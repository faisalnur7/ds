@extends('layouts.admin')

@section('title', __($title))
@section('header', __($title))

@section('content')
    <div class="space-y-6">
        <section class="rounded-[2rem] ccims-panel-strong p-6 sm:p-8">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __('Module') }}</p>
                    <h2 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">{{ __($title) }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-400">{{ __($description) }}</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5">
                    {{ __('Back to dashboard') }}
                </a>
            </div>
        </section>

        <section class="rounded-[2rem] ccims-panel p-4 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-[0.24em] text-slate-400">
                            <th class="px-4 py-3">{{ __('Record') }}</th>
                            <th class="px-4 py-3">{{ __('Details') }}</th>
                            <th class="px-4 py-3">{{ __('Created') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($items as $item)
                            <tr class="bg-white/0">
                                <td class="px-4 py-4 font-medium text-white">
                                @php($recordValue = data_get($item, 'name') ?? data_get($item, 'member_code') ?? data_get($item, 'action') ?? data_get($item, 'key') ?? data_get($item, 'reference_no') ?? data_get($item, 'status'))
                                {{ is_string($recordValue) ? __($recordValue) : $recordValue }}
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-300">
                                @php($detailValue = data_get($item, 'description') ?? data_get($item, 'full_name') ?? data_get($item, 'action') ?? data_get($item, 'value_type') ?? data_get($item, 'status'))
                                {{ is_string($detailValue) ? __($detailValue) : $detailValue }}
                            </td>
                                <td class="px-4 py-4 text-sm text-slate-400">
                                    {{ optional(data_get($item, 'created_at'))?->format('M j, Y') ?? optional(data_get($item, 'requested_at'))?->format('M j, Y') ?? optional(data_get($item, 'distribution_date'))?->format('M j, Y') ?? optional(data_get($item, 'created_at')) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-400">{{ __('No records found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $items->links() }}
            </div>
        </section>
    </div>
@endsection
