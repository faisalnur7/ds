@extends('layouts.admin')

@section('title', __($title))
@section('header', __($title))

@section('content')
    <div class="{{ $formContainerClass ?? 'mx-auto max-w-4xl space-y-6' }}">
        <section class="rounded-[2rem] ccims-panel-strong p-6 sm:p-8">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __('Form') }}</p>
            <h2 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">{{ __($title) }}</h2>
            <p class="mt-3 text-sm leading-6 text-slate-400">{{ __($description) }}</p>
        </section>

        <form
            method="POST"
            action="{{ $action }}"
            class="space-y-6 rounded-[2rem] ccims-panel p-6 sm:p-8"
            @isset($memberDefaults)
                @if (! empty($paymentAutoFill))
                x-data="paymentDefaults(@js($memberDefaults), @js($memberPaymentMonths ?? []))"
                x-init="applyMemberDefaults(document.getElementById('member_id')?.value)"
                x-on:submit="validatePaymentMonth($event)"
                @endif
            @endisset
        >
            @csrf
            @if ($method !== 'POST')
                @method($method)
            @endif

            <div class="grid gap-5 sm:grid-cols-2">
                @foreach ($fields as $field)
                    <div class="{{ ($field['span'] ?? 1) === 2 ? 'sm:col-span-2' : '' }}">
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="{{ $field['name'] }}">{{ __($field['label']) }}</label>
                        @php
                            $rawValue = old($field['name'], data_get($record, $field['name']));
                            $isReadonly = (bool) ($field['readonly'] ?? false);
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
                            <select
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                class="ccims-input"
                                @if (($field['name'] ?? '') === 'member_id' && isset($memberDefaults) && ! empty($paymentAutoFill))
                                    x-on:change="applyMemberDefaults($event.target.value); checkDuplicatePaymentMonth()"
                                @endif
                            >
                                @foreach ($field['options'] as $value => $label)
                                    <option value="{{ $value }}" @selected($rawValue == $value)>{{ __($label) }}</option>
                                @endforeach
                            </select>
                        @elseif (($field['type'] ?? 'text') === 'toggle')
                            <label class="inline-flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                <input type="checkbox" name="{{ $field['name'] }}" value="1" @checked((bool) $rawValue) class="h-5 w-5 rounded border-white/10 bg-white/5 text-amber-400">
                                <span class="text-sm text-slate-200">{{ __($field['helper'] ?? 'Toggle this setting') }}</span>
                            </label>
                        @elseif (($field['type'] ?? 'text') === 'grouped-multiselect')
                            <div class="space-y-4 rounded-2xl border border-white/10 bg-white/5 p-4">
                                @php($selected = ($rawValue instanceof \Illuminate\Support\Collection ? $rawValue->modelKeys() : collect($rawValue ?? [])->map(fn ($value) => is_object($value) && isset($value->id) ? $value->id : $value)->all()))
                                @php($selected = collect($selected)->map(fn ($value) => (string) $value)->all())
                                @forelse ($field['groups'] ?? [] as $group)
                                    <section class="rounded-2xl border border-white/10 bg-slate-950/30 p-4">
                                        <div class="flex items-center justify-between gap-3">
                                            <h3 class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/80">{{ __($group['label'] ?? 'Other') }}</h3>
                                            <span class="text-xs text-slate-500">{{ count($group['permissions'] ?? []) }} {{ __('items') }}</span>
                                        </div>
                                        <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                            @foreach ($group['permissions'] ?? [] as $permission)
                                                <label class="inline-flex items-start gap-3 rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200">
                                                    <input type="checkbox" name="{{ $field['name'] }}[]" value="{{ $permission['id'] }}" @checked(in_array((string) $permission['id'], $selected, true)) class="mt-0.5 h-4 w-4 rounded border-white/10 bg-white/5 text-amber-400">
                                                    <span>{{ __($permission['name']) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </section>
                                @empty
                                    <p class="text-sm text-slate-400">{{ __('No permissions available.') }}</p>
                                @endforelse
                            </div>
                        @elseif (($field['type'] ?? 'text') === 'multiselect')
                            <div class="grid gap-2 rounded-2xl border border-white/10 bg-white/5 p-4">
                                @php($selected = ($rawValue instanceof \Illuminate\Support\Collection ? $rawValue->modelKeys() : collect($rawValue ?? [])->map(fn ($value) => is_object($value) && isset($value->id) ? $value->id : $value)->all()))
                                @php($selected = collect($selected)->map(fn ($value) => (string) $value)->all())
                                @foreach ($field['options'] as $value => $label)
                                    <label class="inline-flex items-center gap-3 text-sm text-slate-200">
                                        <input type="checkbox" name="{{ $field['name'] }}[]" value="{{ $value }}" @checked(in_array((string) $value, $selected, true)) class="h-4 w-4 rounded border-white/10 bg-white/5 text-amber-400">
                                        <span>{{ __($label) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif (($field['type'] ?? 'text') === 'computed-date')
                            <div
                                x-data="{
                                    value: '',
                                    update() {
                                        const joinInput = document.getElementById(@js($field['date_source'] ?? 'join_date'));
                                        const joinValue = joinInput?.value || '';
                                        const monthsValue = @js($field['months_value'] ?? null);

                                        if (! joinValue) {
                                            this.value = '';
                                            return;
                                        }

                                        const date = new Date(`${joinValue}T00:00:00`);

                                        if (Number.isNaN(date.getTime())) {
                                            this.value = '';
                                            return;
                                        }

                                        if (monthsValue !== null) {
                                            date.setMonth(date.getMonth() + monthsValue);
                                        }

                                        this.value = date.toISOString().slice(0, 10);
                                    },
                                    bind() {
                                        const joinInput = document.getElementById(@js($field['date_source'] ?? 'join_date'));
                                        joinInput?.addEventListener('input', () => this.update());
                                        joinInput?.addEventListener('change', () => this.update());
                                    }
                                }"
                                x-init="update(); bind()"
                                class="space-y-2"
                            >
                                <input type="text" readonly :value="value" class="ccims-input bg-white/10">
                            </div>
                        @else
                            <input
                                id="{{ $field['name'] }}"
                                type="{{ $field['type'] ?? 'text' }}"
                                name="{{ $field['name'] }}"
                                value="{{ ($field['type'] ?? 'text') === 'password' ? '' : $rawValue }}"
                                class="ccims-input {{ $isReadonly ? 'bg-white/10' : '' }}"
                                @if (($field['name'] ?? '') === 'payment_month' && isset($memberPaymentMonths) && ! empty($paymentAutoFill))
                                    x-on:change="checkDuplicatePaymentMonth()"
                                    x-on:input="checkDuplicatePaymentMonth()"
                                @endif
                                @if (isset($field['min'])) min="{{ $field['min'] }}" @endif
                                @if (isset($field['max'])) max="{{ $field['max'] }}" @endif
                                @if (isset($field['step'])) step="{{ $field['step'] }}" @endif
                                @if ($isReadonly) readonly @endif
                            >
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
                <a href="{{ route("admin.{$routePrefix}.index") }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/5">{{ __('Back') }}</a>
                @if ($canSubmit)
                    <button type="submit" class="rounded-full bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">{{ __($submitLabel) }}</button>
                @else
                    <p class="text-sm text-slate-400">{{ __('You can view this form, but you do not have permission to submit changes.') }}</p>
                @endif
            </div>
        </form>
    </div>

    @isset($memberDefaults)
        <script>
            window.paymentDefaults = function paymentDefaults(defaults, paymentMonths) {
                return {
                    defaults,
                    paymentMonths,
                    applyMemberDefaults(memberId) {
                        const selected = memberId ? this.defaults[String(memberId)] : null;
                        const fallback = this.defaults.__default ?? {};

                        const values = selected ?? fallback;

                        for (const [key, value] of Object.entries(values)) {
                            const input = document.getElementById(key);

                            if (input) {
                                input.value = value ?? '';
                                input.dispatchEvent(new Event('input', { bubbles: true }));
                                input.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                        }

                        const amountInput = document.getElementById('amount_paid');
                        const shareValue = Number(document.getElementById('share_value')?.value || 0);
                        const shareCost = Number(document.getElementById('share_cost')?.value || 0);

                        if (amountInput) {
                            amountInput.value = (shareValue + shareCost).toFixed(2);
                        }
                    },
                    selectedPaymentMonths(memberId) {
                        if (! memberId) {
                            return [];
                        }

                        return this.paymentMonths[String(memberId)] ?? [];
                    },
                    checkDuplicatePaymentMonth() {
                        const memberId = document.getElementById('member_id')?.value;
                        const paymentMonth = document.getElementById('payment_month')?.value;

                        if (! memberId || ! paymentMonth) {
                            return false;
                        }

                        const alreadyPaid = this.selectedPaymentMonths(memberId).includes(paymentMonth);

                        if (alreadyPaid) {
                            alert(@js(__('Payment is already done for the selected month.')));
                            return true;
                        }

                        return false;
                    },
                    validatePaymentMonth(event) {
                        if (this.checkDuplicatePaymentMonth()) {
                            event.preventDefault();
                            event.stopPropagation();
                            return false;
                        }

                        return true;
                    }
                };
            };
        </script>
    @endisset
@endsection
