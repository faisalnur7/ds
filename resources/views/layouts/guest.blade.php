<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|inter:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-100">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.18),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(16,185,129,0.12),_transparent_24%),linear-gradient(180deg,_#020617_0%,_#0f172a_100%)]">
            <div class="mx-auto flex min-h-screen max-w-7xl items-center px-4 py-10 sm:px-6 lg:px-8">
                <div class="grid w-full gap-8 lg:grid-cols-[1.05fr_0.95fr]">
                    <section class="flex flex-col justify-between rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-black/20 backdrop-blur-xl lg:p-10">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400 via-teal-400 to-emerald-500 text-slate-950 shadow-lg shadow-cyan-500/20">
                                <span class="font-[family-name:Space_Grotesk] text-lg font-bold">A</span>
                            </div>
                            <div>
                                <p class="font-[family-name:Space_Grotesk] text-2xl font-bold tracking-tight">{{ config('app.name', 'Laravel') }}</p>
                                <p class="text-sm text-slate-400">Admin access gateway</p>
                            </div>
                        </div>

                        <div class="mt-10 max-w-xl">
                            <p class="text-sm font-medium uppercase tracking-[0.3em] text-cyan-200/80">Modern control center</p>
                            <h1 class="mt-4 font-[family-name:Space_Grotesk] text-4xl font-bold leading-tight text-white sm:text-5xl">
                                Sign in to the Laravel 12 admin panel.
                            </h1>
                            <p class="mt-4 max-w-lg text-base leading-7 text-slate-300">
                                Tailwind-powered dashboard, admin-only routes, and a clean system shell for expanding into users, reports, settings, and operations.
                            </p>
                        </div>

                        <div class="mt-10 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm text-slate-400">Framework</p>
                                <p class="mt-2 text-lg font-semibold text-white">Laravel 12</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm text-slate-400">UI</p>
                                <p class="mt-2 text-lg font-semibold text-white">Tailwind CSS</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm text-slate-400">Access</p>
                                <p class="mt-2 text-lg font-semibold text-white">Admin only</p>
                            </div>
                        </div>
                    </section>

                    <section class="flex items-center justify-center">
                        <div class="w-full max-w-md rounded-[2rem] border border-white/10 bg-slate-950/80 p-8 shadow-2xl shadow-black/30 backdrop-blur-xl sm:p-10">
                            {{ $slot }}
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </body>
</html>
