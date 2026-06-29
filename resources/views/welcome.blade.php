<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Darus Salam CCIMS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|inter:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-100 bg-[var(--ccims-bg)]">
        <div class="ccims-mobile-shell bg-[radial-gradient(circle_at_top_left,_rgba(251,191,36,0.16),_transparent_28%),radial-gradient(circle_at_top_right,_rgba(20,184,166,0.12),_transparent_24%),linear-gradient(180deg,_#020617_0%,_#0f172a_100%)]">
            <div class="mx-auto flex min-h-screen w-full max-w-[480px] items-center px-4 py-6 sm:px-5">
                <div class="grid w-full gap-5">
                    <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/20 backdrop-blur-xl">
                        <div class="inline-flex items-center gap-3 rounded-full border border-white/10 bg-slate-950/50 px-4 py-2 text-sm text-slate-300">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            {{ config('app.name', 'Darus Salam CCIMS') }}
                        </div>

                        <h1 class="mt-6 max-w-2xl font-[family-name:Space_Grotesk] text-3xl font-bold leading-tight text-white sm:text-4xl">
                            {{ __('Capital collection and member profit management in one place.') }}
                        </h1>
                        <p class="mt-5 max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">
                            {{ __('Darus Salam CCIMS is the operating system for member onboarding, monthly payments, share ownership, checkout, and compliance reporting.') }}
                        </p>

                        <div class="mt-8 flex flex-col gap-3">
                            <a href="{{ route('login') }}" class="rounded-full bg-amber-400 px-5 py-3 text-center text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                                {{ __('Go to login') }}
                            </a>
                            @auth
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-white/10 px-5 py-3 text-center text-sm font-semibold text-white transition hover:bg-white/5">
                                        {{ __('Open admin panel') }}
                                    </a>
                                @endif
                            @endauth
                        </div>

                    </section>

                    <section class="rounded-[2rem] border border-white/10 bg-slate-950/80 p-6 shadow-2xl shadow-black/30 backdrop-blur-xl">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-300 via-teal-400 to-slate-900 text-slate-950">
                                <span class="font-[family-name:Space_Grotesk] text-xl font-bold">DS</span>
                            </div>
                            <div>
                                <p class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">{{ __('CCIMS Control Center') }}</p>
                                <p class="text-sm text-slate-400">{{ __('Designed for finance operations, not just demos.') }}</p>
                            </div>
                        </div>

                        <div class="mt-8 space-y-3">
                            <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                                <p class="text-sm text-slate-400">{{ __('Membership') }}</p>
                                <p class="mt-2 text-base font-medium text-white">{{ __('Member onboarding, KYC, and role-based access.') }}</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                                <p class="text-sm text-slate-400">{{ __('Collections') }}</p>
                                <p class="mt-2 text-base font-medium text-white">{{ __('Monthly payments and partial settlement.') }}</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-white/5 p-4">
                                <p class="text-sm text-slate-400">{{ __('Finance') }}</p>
                                <p class="mt-2 text-base font-medium text-white">{{ __('Profit distribution and checkout.') }}</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </body>
</html>
