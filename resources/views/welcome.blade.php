<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|inter:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-100">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(14,165,233,0.18),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(16,185,129,0.12),_transparent_24%),linear-gradient(180deg,_#020617_0%,_#0f172a_100%)]">
            <div class="mx-auto flex min-h-screen max-w-7xl items-center px-4 py-10 sm:px-6 lg:px-8">
                <div class="grid w-full gap-8 lg:grid-cols-[1.08fr_0.92fr]">
                    <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-black/20 backdrop-blur-xl lg:p-12">
                        <div class="inline-flex items-center gap-3 rounded-full border border-white/10 bg-slate-950/50 px-4 py-2 text-sm text-slate-300">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            Laravel 12 admin starter
                        </div>

                        <h1 class="mt-6 max-w-2xl font-[family-name:Space_Grotesk] text-4xl font-bold leading-tight text-white sm:text-6xl">
                            A modern admin panel built with Tailwind CSS.
                        </h1>
                        <p class="mt-5 max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">
                            This workspace now includes Laravel 12, Breeze auth, an admin-only dashboard, a user management screen, and a seeded admin login so you can enter the panel immediately.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('login') }}" class="rounded-full bg-cyan-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-cyan-300">
                                Go to login
                            </a>
                            @auth
                                <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/5">
                                    Open admin panel
                                </a>
                            @endauth
                        </div>

                        <div class="mt-10 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm text-slate-400">Auth</p>
                                <p class="mt-2 text-lg font-semibold text-white">Breeze</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm text-slate-400">Admin route</p>
                                <p class="mt-2 text-lg font-semibold text-white">/admin</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-slate-950/40 p-4">
                                <p class="text-sm text-slate-400">Demo login</p>
                                <p class="mt-2 text-lg font-semibold text-white">admin@example.com</p>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[2rem] border border-white/10 bg-slate-950/80 p-8 shadow-2xl shadow-black/30 backdrop-blur-xl lg:p-10">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400 via-teal-400 to-emerald-500 text-slate-950">
                                <span class="font-[family-name:Space_Grotesk] text-xl font-bold">A</span>
                            </div>
                            <div>
                                <p class="font-[family-name:Space_Grotesk] text-2xl font-bold text-white">Control Center</p>
                                <p class="text-sm text-slate-400">Designed for operations, not just demos.</p>
                            </div>
                        </div>

                        <div class="mt-10 space-y-4">
                            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                                <p class="text-sm text-slate-400">Admin dashboard</p>
                                <p class="mt-2 text-lg font-medium text-white">Summary cards, recent activity, and a responsive layout.</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                                <p class="text-sm text-slate-400">User management</p>
                                <p class="mt-2 text-lg font-medium text-white">A table-driven screen for expanding into CRUD later.</p>
                            </div>
                            <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                                <p class="text-sm text-slate-400">Access control</p>
                                <p class="mt-2 text-lg font-medium text-white">Admin middleware protects the panel from non-admin users.</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </body>
</html>
