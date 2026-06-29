<x-app-layout>
    <x-slot name="header">
        <h2 class="font-[family-name:Space_Grotesk] text-xl font-semibold leading-tight text-white">
            {{ __('Checkout Requests') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/20 backdrop-blur-xl sm:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200/80">{{ __('Member self-service') }}</p>
            <h3 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">
                {{ $member->full_name }}
            </h3>
            <p class="mt-3 text-sm leading-6 text-slate-300 sm:text-base">
                {{ __('Submit a checkout request, review your estimated refundable amount, and track approval status in one place.') }}
            </p>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-3xl ccims-panel p-5 sm:p-6">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ __('Available amount') }}</p>
                <p class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-emerald-200">{{ number_format($availableAmount, 2) }}</p>
            </div>
            <div class="rounded-3xl ccims-panel p-5 sm:p-6">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ __('Checkout eligibility') }}</p>
                <p class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">
                    {{ $checkoutEligibleOn ? $checkoutEligibleOn->format('M j, Y') : __('Not set') }}
                </p>
            </div>
            <div class="rounded-3xl ccims-panel p-5 sm:p-6">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ __('Latest request') }}</p>
                <p class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">
                    {{ $latestRequest?->checkout_type ? __(ucfirst($latestRequest->checkout_type)) : __('None') }}
                </p>
            </div>
            <div class="rounded-3xl ccims-panel p-5 sm:p-6">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-400">{{ __('Current status') }}</p>
                <p class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-amber-200">
                    {{ $latestRequest ? __(ucfirst($latestRequest->status)) : __('N/A') }}
                </p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="rounded-3xl ccims-panel p-5 sm:p-6">
                <h3 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">{{ __('Request checkout') }}</h3>
                <p class="mt-2 text-sm text-slate-400">
                    {{ __('Use a full checkout to close the account or a partial checkout to withdraw a percentage of your available amount.') }}
                </p>

                @if (session('status') === 'created')
                    <div class="mt-4 rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                        {{ __('Your request has been submitted.') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-4 rounded-2xl border border-rose-300/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('checkout-requests.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <label class="block">
                        <span class="mb-2 block text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('Checkout type') }}</span>
                        <select name="checkout_type" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition focus:border-amber-300/40">
                            <option value="full">{{ __('Full') }}</option>
                            <option value="partial">{{ __('Partial') }}</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('Partial percentage') }}</span>
                        <input type="number" name="partial_percentage" min="1" max="100" step="0.01" value="{{ old('partial_percentage') }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition focus:border-amber-300/40">
                    </label>

                    <label class="block">
                        <span class="mb-2 block text-xs uppercase tracking-[0.24em] text-slate-400">{{ __('Outstanding loan deduction') }}</span>
                        <input type="number" name="outstanding_loan_deducted" min="0" step="0.01" value="{{ old('outstanding_loan_deducted', 0) }}" class="w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-sm text-white outline-none transition focus:border-amber-300/40">
                    </label>

                    <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4 text-sm text-slate-300">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('Estimated refundable amount') }}</p>
                        <p class="mt-2 text-2xl font-bold text-white">{{ number_format($availableAmount, 2) }}</p>
                        <p class="mt-2 text-xs leading-5 text-slate-400">
                            {{ __('The final refundable amount is stored after the request is submitted and may be adjusted by the administrator if needed.') }}
                        </p>
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-white px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-100">
                        {{ __('Submit request') }}
                    </button>
                </form>
            </div>

            <div class="rounded-3xl ccims-panel p-5 sm:p-6">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h3 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">{{ __('Request history') }}</h3>
                        <p class="mt-1 text-sm text-slate-400">{{ __('Track the state of your recent checkout requests.') }}</p>
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse ($requests as $requestRecord)
                        <article class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ __(ucfirst($requestRecord->checkout_type)) }}</p>
                                    <p class="mt-1 text-sm text-slate-400">{{ optional($requestRecord->requested_at)->format('M j, Y g:i A') ?? '—' }}</p>
                                </div>
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $requestRecord->status === 'approved' || $requestRecord->status === 'paid' ? 'bg-emerald-400/10 text-emerald-200' : ($requestRecord->status === 'rejected' ? 'bg-rose-400/10 text-rose-200' : 'bg-amber-400/10 text-amber-200') }}">
                                    {{ __(ucfirst($requestRecord->status)) }}
                                </span>
                            </div>

                            <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-3">
                                    <dt class="text-[11px] uppercase tracking-[0.2em] text-slate-500">{{ __('Refundable') }}</dt>
                                    <dd class="mt-1 text-slate-200">{{ number_format((float) $requestRecord->refundable_amount, 2) }}</dd>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-3">
                                    <dt class="text-[11px] uppercase tracking-[0.2em] text-slate-500">{{ __('Loan deduction') }}</dt>
                                    <dd class="mt-1 text-slate-200">{{ number_format((float) $requestRecord->outstanding_loan_deducted, 2) }}</dd>
                                </div>
                            </dl>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-6 text-sm text-slate-400">
                            {{ __('No checkout requests found.') }}
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $requests->links() }}
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
