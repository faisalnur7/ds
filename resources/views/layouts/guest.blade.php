<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Darus Salam CCIMS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|inter:400,500,600&display=swap" rel="stylesheet" />

        <x-theme-bootstrap />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak]{display:none!important;}</style>
    </head>
    <body class="font-sans antialiased ccims-text bg-[var(--ccims-bg)]">
        <div class="ccims-mobile-shell ccims-shell-bg">
            <div class="mx-auto flex w-full max-w-[480px] justify-end px-4 pt-4">
                <div class="rounded-full border border-white/10 bg-white/5 p-1 backdrop-blur-xl">
                    <x-language-switcher />
                    <x-theme-switcher />
                </div>
            </div>
            <div class="mx-auto min-h-screen w-full max-w-[480px] px-4 py-6 pb-10 sm:px-5">
                <div class="space-y-6">
                    <section class="rounded-[2rem] ccims-panel p-5 sm:p-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-300 via-teal-400 to-slate-900 text-slate-950 shadow-lg shadow-amber-500/20">
                                <span class="font-[family-name:Space_Grotesk] text-lg font-bold">DS</span>
                            </div>
                            <div>
                                <p class="font-[family-name:Space_Grotesk] text-xl font-bold tracking-tight">{{ config('app.name', 'Darus Salam CCIMS') }}</p>
                                <p class="text-sm text-slate-400">{{ __('Capital collection and investment management') }}</p>
                            </div>
                        </div>

                        <div class="mt-6 max-w-3xl">
                            <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __('Midnight finance suite') }}</p>
                            <h1 class="mt-4 font-[family-name:Space_Grotesk] text-3xl font-bold leading-tight text-white sm:text-4xl">
                                {{ __('Access Darus Salam CCIMS.') }}
                            </h1>
                            <p class="mt-4 max-w-lg text-base leading-7 text-slate-300">
                                {{ __('Manage member capital, share collections, profit distribution, and checkout workflows from a single secure workspace.') }}
                            </p>
                        </div>

                    </section>

                    <section class="w-full">
                        {{ $slot }}
                    </section>
                </div>
            </div>
        </div>
    </body>
</html>
