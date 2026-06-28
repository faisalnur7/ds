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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>[x-cloak]{display:none!important;}</style>
    </head>
    <body class="font-sans antialiased ccims-text">
        <div class="min-h-screen ccims-shell-bg">
            <div class="mx-auto flex max-w-[96rem] justify-end px-4 pt-6 sm:px-6 lg:px-8">
                <x-language-switcher />
                <x-theme-switcher />
            </div>
            <div class="mx-auto min-h-screen max-w-[96rem] px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="space-y-8">
                    <section class="rounded-[2rem] ccims-panel p-6 lg:p-8">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-300 via-teal-400 to-slate-900 text-slate-950 shadow-lg shadow-amber-500/20">
                                <span class="font-[family-name:Space_Grotesk] text-lg font-bold">DS</span>
                            </div>
                            <div>
                                <p class="font-[family-name:Space_Grotesk] text-2xl font-bold tracking-tight">{{ config('app.name', 'Darus Salam CCIMS') }}</p>
                                <p class="text-sm text-slate-400">{{ __('Capital collection and investment management') }}</p>
                            </div>
                        </div>

                        <div class="mt-6 max-w-3xl">
                            <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __('Midnight finance suite') }}</p>
                            <h1 class="mt-4 font-[family-name:Space_Grotesk] text-4xl font-bold leading-tight text-white sm:text-5xl">
                                {{ __('Access Darus Salam CCIMS.') }}
                            </h1>
                            <p class="mt-4 max-w-lg text-base leading-7 text-slate-300">
                                {{ __('Manage member capital, share collections, loan tracking, profit distribution, and checkout workflows from a single secure workspace.') }}
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
