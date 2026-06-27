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
    </head>
    <body class="font-sans antialiased ccims-text">
        <div class="min-h-screen ccims-shell-bg">
            <div class="mx-auto flex max-w-7xl justify-end px-4 pt-6 sm:px-6 lg:px-8">
                <x-theme-switcher />
            </div>
            <div class="mx-auto flex min-h-screen max-w-7xl items-center px-4 py-10 sm:px-6 lg:px-8">
                <div class="grid w-full gap-8 lg:grid-cols-[1.05fr_0.95fr]">
                    <section class="flex flex-col justify-between rounded-[2rem] ccims-panel p-8 lg:p-10">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-300 via-teal-400 to-slate-900 text-slate-950 shadow-lg shadow-amber-500/20">
                                <span class="font-[family-name:Space_Grotesk] text-lg font-bold">DS</span>
                            </div>
                            <div>
                                <p class="font-[family-name:Space_Grotesk] text-2xl font-bold tracking-tight">{{ config('app.name', 'Darus Salam CCIMS') }}</p>
                                <p class="text-sm text-slate-400">Capital collection and investment management</p>
                            </div>
                        </div>

                        <div class="mt-10 max-w-xl">
                            <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">Midnight finance suite</p>
                            <h1 class="mt-4 font-[family-name:Space_Grotesk] text-4xl font-bold leading-tight text-white sm:text-5xl">
                                Sign in to Darus Salam CCIMS.
                            </h1>
                            <p class="mt-4 max-w-lg text-base leading-7 text-slate-300">
                                Manage member capital, share collections, loan tracking, profit distribution, and checkout workflows from a single secure workspace.
                            </p>
                        </div>

                        <div class="mt-10 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm text-slate-400">Platform</p>
                                <p class="mt-2 text-lg font-semibold text-white">CCIMS</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm text-slate-400">Theme</p>
                                <p class="mt-2 text-lg font-semibold text-white">Midnight Teal</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm text-slate-400">Access</p>
                                <p class="mt-2 text-lg font-semibold text-white">Role based</p>
                            </div>
                        </div>
                    </section>

                    <section class="flex items-center justify-center">
                        <div class="w-full max-w-md rounded-[2rem] ccims-panel-strong p-8 sm:p-10">
                            {{ $slot }}
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </body>
</html>
