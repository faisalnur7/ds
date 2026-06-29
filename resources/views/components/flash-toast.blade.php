@php
    $status = session('status');

    $toasts = [
        'created' => [
            'tone' => 'emerald',
            'message' => __('Created successfully.'),
        ],
        'updated' => [
            'tone' => 'emerald',
            'message' => __('Updated successfully.'),
        ],
        'deleted' => [
            'tone' => 'slate',
            'message' => __('Deleted successfully.'),
        ],
        'profile-updated' => [
            'tone' => 'emerald',
            'message' => __('Profile updated successfully.'),
        ],
        'password-updated' => [
            'tone' => 'emerald',
            'message' => __('Password updated successfully.'),
        ],
        'verification-link-sent' => [
            'tone' => 'amber',
            'message' => __('A new verification link has been sent.'),
        ],
    ];

    $toast = $status ? ($toasts[$status] ?? [
        'tone' => 'slate',
        'message' => __($status),
    ]) : null;

    $toneClasses = [
        'emerald' => 'border-emerald-400/20 bg-emerald-500/10 text-emerald-50',
        'amber' => 'border-amber-400/20 bg-amber-500/10 text-amber-50',
        'slate' => 'border-white/10 bg-slate-950/90 text-slate-100',
    ];

    $accentClasses = [
        'emerald' => 'bg-emerald-300',
        'amber' => 'bg-amber-300',
        'slate' => 'bg-slate-300',
    ];
@endphp

@if ($toast)
    <div
        x-data="{ open: true }"
        x-show="open"
        x-transition.opacity.duration.200ms
        x-init="setTimeout(() => open = false, 4000)"
        x-cloak
        class="pointer-events-none fixed left-4 right-4 top-4 z-[70] mx-auto w-[min(28rem,calc(100vw-2rem))]"
        role="status"
        aria-live="polite"
    >
        <div class="pointer-events-auto overflow-hidden rounded-[1.5rem] border shadow-2xl backdrop-blur-xl {{ $toneClasses[$toast['tone']] ?? $toneClasses['slate'] }}">
            <div class="flex items-start gap-3 px-4 py-4 sm:px-5">
                <div class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $accentClasses[$toast['tone']] ?? $accentClasses['slate'] }}">
                    <svg class="h-5 w-5 text-slate-950" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M8.5 13.2 5.8 10.5l-1.1 1.1 3.8 3.8 7-7-1.1-1.1-5.9 5.9Z" fill="currentColor" />
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold">{{ __('Success') }}</p>
                    <p class="mt-1 text-sm leading-6 text-current/90">{{ $toast['message'] }}</p>
                </div>

                <button
                    type="button"
                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-current/70 transition hover:bg-white/10 hover:text-current"
                    @click="open = false"
                    aria-label="{{ __('Dismiss notification') }}"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M5 5l10 10M15 5 5 15" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endif
