<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Darus Salam CCIMS'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,700|inter:400,500,600" rel="stylesheet" />

        <x-theme-bootstrap />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="ccims-text overflow-hidden antialiased bg-[var(--ccims-bg)]">
        <x-flash-toast />
        <div
            x-data="{ sidebarOpen: false, userMenuOpen: false }"
            class="min-h-screen overflow-hidden ccims-shell-bg"
        >
            <div class="flex h-dvh overflow-hidden">
                <aside
                    class="ccims-admin-sidebar fixed inset-y-0 left-0 z-40 flex h-dvh w-[min(19rem,calc(100vw-1.5rem))] -translate-x-full flex-col overflow-hidden px-4 py-4 transition-transform duration-300 sm:px-6 sm:py-6 lg:sticky lg:top-0 lg:h-dvh lg:w-72 lg:translate-x-0"
                    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-300 via-teal-400 to-slate-900 text-slate-950 shadow-lg shadow-amber-500/20">
                                <span class="text-lg font-bold">DS</span>
                            </div>
                            <div class="min-w-0">
                                <p class="truncate font-[family-name:Space_Grotesk] text-lg font-bold tracking-tight">{{ config('app.name', 'Darus Salam CCIMS') }}</p>
                                <p class="text-sm text-slate-400">{{ __('Operations center') }}</p>
                            </div>
                        </div>

                        <button
                            type="button"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-slate-200 transition hover:bg-white/10 lg:hidden"
                            @click="sidebarOpen = false"
                            aria-label="Close sidebar"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 6l12 12M18 6l-12 12" />
                            </svg>
                        </button>
                    </div>

                    <nav class="ccims-scrollbar-none mt-6 flex min-h-0 flex-1 flex-col space-y-1.5 overflow-y-auto sm:mt-8 sm:space-y-2">
                        <x-admin-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-admin-nav-link>
                        @if (auth()->user()?->hasPermission('view_members'))
                            <x-admin-nav-link :href="route('admin.members.index')" :active="request()->routeIs('admin.members.*')">
                                {{ __('Members') }}
                            </x-admin-nav-link>
                        @endif
                        @if (auth()->user()?->hasPermission('view_payments'))
                            <x-admin-nav-link :href="route('admin.payments.index')" :active="request()->routeIs('admin.payments.*')">
                                {{ __('Payments') }}
                            </x-admin-nav-link>
                        @endif
                        @if (auth()->user()?->isAdmin())
                            <x-admin-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                                {{ __('Reports') }}
                            </x-admin-nav-link>
                        @endif
                        @if (
                            auth()->user()?->hasPermission('view_expense_menu')
                            || auth()->user()?->hasPermission('view_expense_categories')
                            || auth()->user()?->hasPermission('view_expenses')
                        )
                            <p class="px-3 pt-4 text-xs uppercase tracking-[0.25em] text-slate-500">{{ __('Operational Expenses') }}</p>
                            @if (auth()->user()?->hasPermission('view_expense_categories'))
                                <x-admin-nav-link :href="route('admin.expense-categories.index')" :active="request()->routeIs('admin.expense-categories.*')">
                                    {{ __('Expense Categories') }}
                                </x-admin-nav-link>
                            @endif
                            @if (auth()->user()?->hasPermission('view_expenses'))
                                <x-admin-nav-link :href="route('admin.expenses.index')" :active="request()->routeIs('admin.expenses.*')">
                                    {{ __('Expenses') }}
                                </x-admin-nav-link>
                            @endif
                        @endif
                        @if (auth()->user()?->hasPermission('view_projects'))
                            <x-admin-nav-link :href="route('admin.projects.index')" :active="request()->routeIs('admin.projects.*')">
                                {{ __('Projects') }}
                            </x-admin-nav-link>
                        @endif
                        @if (auth()->user()?->hasPermission('view_checkout_requests'))
                            <x-admin-nav-link :href="route('admin.checkout-requests.index')" :active="request()->routeIs('admin.checkout-requests.*')">
                                {{ __('Checkout') }}
                            </x-admin-nav-link>
                        @endif
                        @if (auth()->user()?->hasPermission('view_share_settings'))
                            <x-admin-nav-link :href="route('admin.share-settings.index')" :active="request()->routeIs('admin.share-settings.*')">
                                {{ __('Share Settings') }}
                            </x-admin-nav-link>
                        @endif
                        @if (auth()->user()?->hasPermission('view_settings'))
                            <x-admin-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')">
                                {{ __('Settings') }}
                            </x-admin-nav-link>
                        @endif
                        @if (auth()->user()?->hasPermission('view_audit_logs'))
                            <x-admin-nav-link :href="route('admin.audit.index')" :active="request()->routeIs('admin.audit.*')">
                                {{ __('Audit') }}
                            </x-admin-nav-link>
                        @endif
                        <x-admin-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                            {{ __('Profile') }}
                        </x-admin-nav-link>
                        @if (auth()->user()?->hasPermission('view_users'))
                            <x-admin-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                {{ __('Users') }}
                            </x-admin-nav-link>
                        @endif
                        @if (auth()->user()?->hasPermission('view_roles'))
                            <x-admin-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                                {{ __('Roles') }}
                            </x-admin-nav-link>
                        @endif
                        @if (auth()->user()?->hasPermission('view_permissions'))
                            <x-admin-nav-link :href="route('admin.permissions.index')" :active="request()->routeIs('admin.permissions.*')">
                                {{ __('Permissions') }}
                            </x-admin-nav-link>
                        @endif
                    </nav>

                </aside>

                <div class="flex min-w-0 flex-1 flex-col overflow-hidden lg:ml-0">
                    <header class="sticky top-0 z-30 border-b border-white/10 bg-slate-950/80 backdrop-blur-xl">
                        <div class="mx-auto flex w-full max-w-[480px] items-start justify-between gap-3 px-4 py-4 sm:px-5 lg:max-w-none lg:px-8">
                            <div class="flex min-w-0 flex-1 items-start gap-3 sm:items-center">
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
                                    <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ __('Overview') }}</p>
                                    <h1 class="max-w-[16rem] font-[family-name:Space_Grotesk] text-xl font-bold leading-tight text-white sm:max-w-none sm:text-2xl">
                                        @yield('header', __('Operations Dashboard'))
                                    </h1>
                                </div>
                            </div>

                            <div class="relative flex shrink-0 items-center justify-end gap-3">
                                <div class="hidden xl:flex xl:items-center xl:gap-3">
                                    <x-language-switcher />
                                    <x-theme-switcher />
                                </div>

                                <button
                                    type="button"
                                    class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/5 transition hover:bg-white/10"
                                    aria-label="{{ __('Open user menu') }}"
                                    @click="userMenuOpen = ! userMenuOpen"
                                >
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-amber-300 to-teal-400 text-sm font-bold text-slate-950">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                </button>

                                <div
                                    x-show="userMenuOpen"
                                    x-transition
                                    @click.outside="userMenuOpen = false"
                                    class="ccims-admin-dropdown absolute right-0 top-full z-50 mt-3 w-[min(16rem,calc(100vw-2rem))] rounded-3xl p-2 sm:w-56"
                                    style="display: none;"
                                >
                                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                        <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                                        <p class="mt-1 text-xs text-slate-400">{{ auth()->user()->isAdmin() ? __('Administrator') : __('Member') }}</p>
                                    </div>
                                    <div class="my-2 border-t border-white/10"></div>
                                    <a href="{{ route('profile.edit') }}" class="flex items-center rounded-2xl px-4 py-3 text-sm text-slate-200 transition hover:bg-white/5">
                                        {{ __('Profile settings') }}
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center rounded-2xl px-4 py-3 text-sm text-rose-300 transition hover:bg-rose-500/10">
                                            {{ __('Log out') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </header>

                    <main class="min-h-0 flex-1 overflow-y-auto px-4 py-5 pb-28 sm:px-5 lg:px-8">
                        @yield('content')
                    </main>
                </div>
            </div>

            <div
                x-show="sidebarOpen"
                class="ccims-admin-overlay fixed inset-0 z-30 lg:hidden"
                @click="sidebarOpen = false"
                style="display: none;"
            ></div>

            <div class="fixed inset-x-0 bottom-0 z-50 lg:hidden">
                <div class="mx-auto w-full max-w-[480px] px-4 pb-4">
                    <div class="ccims-mobile-tabbar rounded-[1.6rem] px-3 py-2">
                        <div class="grid grid-cols-4 gap-2 text-xs font-semibold">
                            <a href="{{ route('admin.dashboard') }}" @class([
                                'rounded-[1.2rem] px-2 py-3 text-center transition',
                                'bg-amber-400 text-slate-950 shadow-lg shadow-amber-500/20' => request()->routeIs('admin.dashboard'),
                                'text-slate-200 hover:bg-white/5' => ! request()->routeIs('admin.dashboard'),
                            ])>
                                {{ __('Home') }}
                            </a>
                            <a href="{{ route('profile.edit') }}" @class([
                                'rounded-[1.2rem] px-2 py-3 text-center transition',
                                'bg-amber-400 text-slate-950 shadow-lg shadow-amber-500/20' => request()->routeIs('profile.*'),
                                'text-slate-200 hover:bg-white/5' => ! request()->routeIs('profile.*'),
                            ])>
                                {{ __('Profile') }}
                            </a>
                            <button
                                type="button"
                                class="rounded-[1.2rem] px-2 py-3 text-center text-slate-200 transition hover:bg-white/5"
                                @click="sidebarOpen = true"
                            >
                                {{ __('Menu') }}
                            </button>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full rounded-[1.2rem] px-2 py-3 text-center text-rose-200 transition hover:bg-rose-400/10"
                                >
                                    {{ __('Exit') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
