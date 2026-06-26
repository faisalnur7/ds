<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Darus Salam CCIMS'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|inter:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="ccims-text antialiased">
        <div
            x-data="{ sidebarOpen: false, userMenuOpen: false }"
            class="min-h-screen ccims-shell-bg"
        >
            <div class="flex min-h-screen">
                <aside
                    class="fixed inset-y-0 z-40 w-72 border-r border-white/10 bg-slate-950/90 px-6 py-6 backdrop-blur-xl transition-transform duration-300 lg:static lg:translate-x-0"
                    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                >
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-300 via-teal-400 to-slate-900 text-slate-950 shadow-lg shadow-amber-500/20">
                            <span class="text-lg font-bold">DS</span>
                        </div>
                        <div>
                            <p class="font-[family-name:Space_Grotesk] text-lg font-bold tracking-tight">{{ config('app.name', 'Darus Salam CCIMS') }}</p>
                            <p class="text-sm text-slate-400">Operations center</p>
                        </div>
                    </div>

                    <nav class="mt-10 space-y-2">
                        <x-admin-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            Dashboard
                        </x-admin-nav-link>
                        <x-admin-nav-link :href="route('admin.members.index')" :active="request()->routeIs('admin.members.*')">
                            Members
                        </x-admin-nav-link>
                        <x-admin-nav-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.*')">
                            Payments
                        </x-admin-nav-link>
                        <x-admin-nav-link :href="route('admin.projects.index')" :active="request()->routeIs('admin.projects.*')">
                            Projects
                        </x-admin-nav-link>
                        <x-admin-nav-link :href="route('admin.loans.index')" :active="request()->routeIs('admin.loans.*')">
                            Loans
                        </x-admin-nav-link>
                        <x-admin-nav-link :href="route('admin.checkout.index')" :active="request()->routeIs('admin.checkout.*')">
                            Checkout
                        </x-admin-nav-link>
                        <x-admin-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')">
                            Settings
                        </x-admin-nav-link>
                        <x-admin-nav-link :href="route('admin.audit.index')" :active="request()->routeIs('admin.audit.*')">
                            Audit
                        </x-admin-nav-link>
                        <x-admin-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                            Profile
                        </x-admin-nav-link>
                    </nav>

                    <div class="mt-10 rounded-3xl border border-white/10 bg-white/5 p-5">
                        <p class="text-sm text-slate-400">Signed in as</p>
                        <p class="mt-1 font-medium text-white">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-slate-400">{{ auth()->user()->email }}</p>
                        <div class="mt-4">
                            <span class="inline-flex items-center rounded-full bg-amber-400/15 px-3 py-1 text-xs font-semibold text-amber-200">
                                Operations access
                            </span>
                        </div>
                    </div>
                </aside>

                <div class="flex min-w-0 flex-1 flex-col lg:ml-0">
                    <header class="sticky top-0 z-30 border-b border-white/10 bg-slate-950/70 backdrop-blur-xl">
                        <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-200 transition hover:bg-white/10 lg:hidden"
                                    @click="sidebarOpen = ! sidebarOpen"
                                    aria-label="Toggle sidebar"
                                >
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </button>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.3em] text-slate-500">Overview</p>
                                    <h1 class="font-[family-name:Space_Grotesk] text-2xl font-bold text-white">@yield('header', 'Operations Dashboard')</h1>
                                </div>
                            </div>

                            <div class="relative">
                                <button
                                    type="button"
                                    class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-left transition hover:bg-white/10"
                                    @click="userMenuOpen = ! userMenuOpen"
                                >
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-amber-300 to-teal-400 text-sm font-bold text-slate-950">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                    <div class="hidden sm:block">
                                        <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-slate-400">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Member' }}</p>
                                    </div>
                                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div class="hidden xl:block">
                                    <x-theme-switcher />
                                </div>

                                <div
                                    x-show="userMenuOpen"
                                    x-transition
                                    @click.outside="userMenuOpen = false"
                                    class="absolute right-0 mt-3 w-56 rounded-3xl border border-white/10 bg-slate-900 p-2 shadow-2xl shadow-black/30"
                                    style="display: none;"
                                >
                                    <a href="{{ route('profile.edit') }}" class="flex items-center rounded-2xl px-4 py-3 text-sm text-slate-200 transition hover:bg-white/5">
                                        Profile settings
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center rounded-2xl px-4 py-3 text-sm text-rose-300 transition hover:bg-rose-500/10">
                                            Log out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </header>

                    <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                        @yield('content')
                    </main>
                </div>
            </div>

            <div
                x-show="sidebarOpen"
                class="fixed inset-0 z-30 bg-slate-950/70 backdrop-blur-sm lg:hidden"
                @click="sidebarOpen = false"
                style="display: none;"
            ></div>
        </div>
    </body>
</html>
