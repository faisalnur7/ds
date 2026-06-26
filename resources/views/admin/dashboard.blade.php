@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header', 'Admin Dashboard')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 lg:grid-cols-4">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/20 backdrop-blur">
                <p class="text-sm text-slate-400">Total users</p>
                <p class="mt-3 font-[family-name:Space_Grotesk] text-4xl font-bold text-white">{{ number_format($stats['totalUsers']) }}</p>
                <p class="mt-3 text-sm text-cyan-200">All registered accounts in the system</p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/20 backdrop-blur">
                <p class="text-sm text-slate-400">Admin users</p>
                <p class="mt-3 font-[family-name:Space_Grotesk] text-4xl font-bold text-white">{{ number_format($stats['adminUsers']) }}</p>
                <p class="mt-3 text-sm text-emerald-200">Accounts with admin access</p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/20 backdrop-blur">
                <p class="text-sm text-slate-400">Created today</p>
                <p class="mt-3 font-[family-name:Space_Grotesk] text-4xl font-bold text-white">{{ number_format($stats['newToday']) }}</p>
                <p class="mt-3 text-sm text-amber-200">New signups over the last 24 hours</p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/20 backdrop-blur">
                <p class="text-sm text-slate-400">Verified users</p>
                <p class="mt-3 font-[family-name:Space_Grotesk] text-4xl font-bold text-white">{{ number_format($stats['verifiedUsers']) }}</p>
                <p class="mt-3 text-sm text-fuchsia-200">Accounts ready for access</p>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.35fr_0.9fr]">
            <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl shadow-black/20 backdrop-blur">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">Recent users</h2>
                        <p class="mt-1 text-sm text-slate-400">A quick snapshot of the latest activity.</p>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/5">
                        View all
                    </a>
                </div>

                <div class="mt-6 overflow-hidden rounded-2xl border border-white/10">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead class="bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">User</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10 bg-slate-950/40">
                            @foreach ($recentUsers as $user)
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="font-medium text-white">{{ $user->name }}</div>
                                        <div class="text-sm text-slate-400">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span @class([
                                            'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                            'bg-cyan-400/10 text-cyan-200' => $user->is_admin,
                                            'bg-white/5 text-slate-300' => ! $user->is_admin,
                                        ])>
                                            {{ $user->is_admin ? 'Admin' : 'Member' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-200">
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-slate-300">
                                        {{ $user->created_at->format('M j, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-cyan-400/15 via-teal-400/10 to-emerald-400/10 p-6 shadow-2xl shadow-black/20 backdrop-blur">
                    <p class="text-sm font-medium uppercase tracking-[0.25em] text-cyan-200/80">Quick actions</p>
                    <h2 class="mt-3 font-[family-name:Space_Grotesk] text-2xl font-bold text-white">Manage the control center</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-300">
                        The admin shell is wired for expansion. Add user management, reports, settings, or API monitoring here without changing the layout.
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('admin.users.index') }}" class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-cyan-100">
                            User directory
                        </a>
                        <a href="{{ route('profile.edit') }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/5">
                            Edit profile
                        </a>
                    </div>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/20 backdrop-blur">
                    <p class="text-sm font-medium uppercase tracking-[0.25em] text-slate-400">Session</p>
                    <div class="mt-4 space-y-3 text-sm text-slate-300">
                        <div class="flex items-center justify-between">
                            <span>Current user</span>
                            <span class="font-medium text-white">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Access level</span>
                            <span class="font-medium text-white">{{ auth()->user()->is_admin ? 'Administrator' : 'User' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Panel status</span>
                            <span class="font-medium text-emerald-300">Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
