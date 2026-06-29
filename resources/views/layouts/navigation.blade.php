@php
    $isAdmin = Auth::user()?->isAdmin();
    $hasPaymentHistory = Auth::user()?->hasPermission('view_payment_history');
    $hasCheckoutRequests = (bool) Auth::user()?->member;
    $fullWidthPage = $fullWidthPage ?? false;
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-40 bg-transparent sm:border-b sm:border-white/10 sm:bg-slate-950/75 sm:backdrop-blur-xl">
    <div class="sm:hidden">
        <div class="mx-auto w-full max-w-[480px] px-3 pt-3">
            <div class="ccims-mobile-topbar flex items-center gap-3 rounded-[1.4rem] px-4 py-3">
                <a href="{{ route('dashboard') }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 text-white shadow-sm shadow-emerald-950/20">
                    <x-application-logo class="h-8 w-8 fill-current text-white" />
                </a>

                <div class="min-w-0 flex-1 text-center">
                    <p class="truncate font-[family-name:Space_Grotesk] text-[0.95rem] font-bold tracking-[0.16em] text-white">
                        {{ strtoupper(config('app.name', 'Darus Salam CCIMS')) }}
                    </p>
                </div>

                <button
                    type="button"
                    class="relative inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/12 text-white shadow-sm shadow-emerald-950/20"
                    aria-label="{{ __('Notifications') }}"
                >
                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4a2 2 0 0 1-.6-1.4V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 18a2 2 0 0 0 4 0" />
                    </svg>
                    <span class="absolute right-0.5 top-0.5 h-2.5 w-2.5 rounded-full border-2 border-emerald-600 bg-rose-400"></span>
                </button>

                <button
                    type="button"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white text-sm font-bold text-emerald-700 shadow-sm shadow-emerald-950/15"
                    @click="open = ! open"
                    aria-label="{{ __('Open navigation') }}"
                >
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </button>
            </div>
        </div>
    </div>

    <div @class([
        'mx-auto hidden w-full items-center justify-between gap-2 px-3 py-2.5 sm:flex sm:gap-3 sm:px-4 sm:py-3',
        'max-w-none' => $fullWidthPage,
        'max-w-[480px]' => ! $fullWidthPage,
    ])>
        <a href="{{ route('dashboard') }}" class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
            <x-application-logo class="block h-9 w-9 shrink-0 fill-current text-white sm:h-10 sm:w-10" />
            <div class="min-w-0">
                <p class="truncate font-[family-name:Space_Grotesk] text-sm font-bold tracking-tight text-white sm:text-base">
                    {{ config('app.name', 'Darus Salam CCIMS') }}
                </p>
                <p class="truncate text-[10px] text-slate-400 sm:text-[11px]">{{ __('Member capital operations') }}</p>
            </div>
        </a>

        <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
            <x-language-switcher />
            <x-theme-switcher />
            <button
                type="button"
                class="inline-flex h-9 w-9 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-200 transition hover:bg-white/10 sm:hidden"
                @click="open = ! open"
                aria-label="Open navigation"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <div @class([
        'mx-auto hidden w-full px-4 pb-3 sm:block',
        'max-w-none' => $fullWidthPage,
        'max-w-[480px]' => ! $fullWidthPage,
    ])>
        <div class="flex items-center gap-2 overflow-x-auto rounded-[1.5rem] border border-white/10 bg-white/5 p-2 ccims-scrollbar-none">
            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-nav-link>
            @if ($hasPaymentHistory)
                <x-nav-link :href="route('payment-history.index')" :active="request()->routeIs('payment-history.*')">
                    {{ __('Payments') }}
                </x-nav-link>
            @endif
            @if ($hasCheckoutRequests)
                <x-nav-link :href="route('checkout-requests.index')" :active="request()->routeIs('checkout-requests.*')">
                    {{ __('Checkout') }}
                </x-nav-link>
            @endif
            @if ($isAdmin)
                <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                    {{ __('Admin') }}
                </x-nav-link>
            @endif
            <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                {{ __('Profile') }}
            </x-nav-link>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div @class([
            'mx-auto w-full border-t border-white/10 px-3 pb-4 pt-3 sm:px-4',
            'max-w-none' => $fullWidthPage,
            'max-w-[480px]' => ! $fullWidthPage,
        ])>
            <div class="ccims-mobile-menu rounded-[1.75rem] p-3">
                <div class="flex items-center justify-between gap-3 px-2 pb-3">
                    <div class="min-w-0">
                        <div class="truncate text-sm font-medium text-white">{{ Auth::user()->name }}</div>
                        <div class="truncate text-xs text-slate-400">{{ Auth::user()->email }}</div>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-200"
                        @click="open = false"
                        aria-label="Close menu"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6l-12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    @if ($hasPaymentHistory)
                        <x-responsive-nav-link :href="route('payment-history.index')" :active="request()->routeIs('payment-history.*')">
                            {{ __('Payment History') }}
                        </x-responsive-nav-link>
                    @endif
                    @if ($hasCheckoutRequests)
                        <x-responsive-nav-link :href="route('checkout-requests.index')" :active="request()->routeIs('checkout-requests.*')">
                            {{ __('Checkout Requests') }}
                        </x-responsive-nav-link>
                    @endif
                    @if ($isAdmin)
                        <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                            {{ __('Admin') }}
                        </x-responsive-nav-link>
                    @endif
                    <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                </div>

                <div class="mt-3 flex items-center gap-3 px-2">
                    <x-language-switcher />
                    <x-theme-switcher />
                </div>

                <form method="POST" action="{{ route('logout') }}" class="mt-3 px-2">
                    @csrf
                    <button type="submit" class="w-full rounded-2xl border border-rose-300/20 bg-rose-400/10 px-4 py-3 text-sm font-semibold text-rose-200 transition hover:bg-rose-400/15">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="fixed inset-x-0 bottom-0 z-50 sm:hidden">
        <div @class([
            'mx-auto w-full px-3 pb-3 sm:px-4 sm:pb-4',
            'max-w-none' => $fullWidthPage,
            'max-w-[480px]' => ! $fullWidthPage,
        ])>
            <div class="ccims-mobile-tabbar-light rounded-[1.6rem] px-2 py-2">
                <div class="grid grid-cols-5 gap-1">
                    <a href="{{ route('dashboard') }}" @class([
                        'flex flex-col items-center justify-center gap-1 rounded-[1.25rem] py-2 transition',
                        'bg-white text-emerald-700 shadow-lg shadow-emerald-500/20' => request()->routeIs('dashboard'),
                        'text-emerald-100/90 hover:bg-white/10' => ! request()->routeIs('dashboard'),
                    ]) aria-label="{{ __('Dashboard') }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 10.5V20h13V10.5" />
                        </svg>
                        <span class="text-[0.62rem] font-semibold tracking-wide">{{ __('Dashboard') }}</span>
                    </a>
                    <a href="{{ $hasPaymentHistory ? route('payment-history.index') : route('profile.edit') }}" @class([
                        'flex flex-col items-center justify-center gap-1 rounded-[1.25rem] py-2 transition',
                        'bg-white text-emerald-700 shadow-lg shadow-emerald-500/20' => request()->routeIs('payment-history.*'),
                        'text-emerald-100/90 hover:bg-white/10' => ! request()->routeIs('payment-history.*'),
                    ]) aria-label="{{ __('Passbook') }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 5h12a3 3 0 0 1 3 3v11H7a3 3 0 0 0-3 3V5Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 9h8M7 13h6" />
                        </svg>
                        <span class="text-[0.62rem] font-semibold tracking-wide">{{ __('Passbook') }}</span>
                    </a>
                    <button
                        type="button"
                        class="flex flex-col items-center justify-center gap-1 rounded-[1.25rem] py-2 text-emerald-100/90 transition hover:bg-white/10"
                        @click="open = true"
                        aria-label="{{ __('Withdraw') }}"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 17 17 7" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 7h7v7" />
                        </svg>
                        <span class="text-[0.62rem] font-semibold tracking-wide">{{ __('Withdraw') }}</span>
                    </button>
                    <a href="{{ route('profile.edit') }}" @class([
                        'flex flex-col items-center justify-center gap-1 rounded-[1.25rem] py-2 transition',
                        'bg-white text-emerald-700 shadow-lg shadow-emerald-500/20' => request()->routeIs('profile.*'),
                        'text-emerald-100/90 hover:bg-white/10' => ! request()->routeIs('profile.*'),
                    ]) aria-label="{{ __('Profile') }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 21a8 8 0 1 0-16 0" />
                            <circle cx="12" cy="8" r="3.25" />
                        </svg>
                        <span class="text-[0.62rem] font-semibold tracking-wide">{{ __('Profile') }}</span>
                    </a>
                    <button
                        type="button"
                        class="flex flex-col items-center justify-center gap-1 rounded-[1.25rem] py-2 text-emerald-100/90 transition hover:bg-white/10"
                        @click="open = true"
                        aria-label="{{ $isAdmin ? __('Investment') : __('More') }}"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m7 15 4-4 3 3 5-6" />
                        </svg>
                        <span class="text-[0.62rem] font-semibold tracking-wide">{{ $isAdmin ? __('Investment') : __('More') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>
