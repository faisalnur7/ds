@extends('layouts.admin')

@section('title', 'Operations Dashboard')
@section('header', 'Operations Dashboard')

@section('content')
    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Total accounts', 'value' => $stats['totalUsers'], 'copy' => 'All registered accounts'],
                ['label' => 'Members', 'value' => $stats['members'], 'copy' => 'Membership records'],
                ['label' => 'Payments', 'value' => $stats['payments'], 'copy' => 'Share collection records'],
                ['label' => 'Loans', 'value' => $stats['loans'], 'copy' => 'Approved or pending loans'],
            ] as $card)
                <div class="rounded-3xl ccims-panel p-6">
                    <p class="text-sm text-slate-400">{{ $card['label'] }}</p>
                    <p class="mt-3 font-[family-name:Space_Grotesk] text-4xl font-bold text-white">{{ number_format($card['value']) }}</p>
                    <p class="mt-3 text-sm text-amber-200">{{ $card['copy'] }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['label' => 'Projects', 'value' => $stats['projects']],
                ['label' => 'Profit distributions', 'value' => $stats['profits']],
                ['label' => 'Checkout requests', 'value' => $stats['checkouts']],
                ['label' => 'Settings rows', 'value' => $stats['settings']],
            ] as $card)
                <div class="rounded-3xl ccims-panel p-6">
                    <p class="text-sm text-slate-400">{{ $card['label'] }}</p>
                    <p class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">{{ number_format($card['value']) }}</p>
                </div>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.35fr_0.9fr]">
            <div class="rounded-3xl ccims-panel p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h2 class="font-[family-name:Space_Grotesk] text-xl font-bold text-white">Recent accounts</h2>
                        <p class="mt-1 text-sm text-slate-400">A quick snapshot of the latest activity.</p>
                    </div>
                    <a href="{{ route('admin.members.index') }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/5">
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
                                            'bg-amber-400/10 text-amber-200' => $user->isAdmin(),
                                            'bg-white/5 text-slate-300' => ! $user->isAdmin(),
                                        ])>
                                            {{ $user->isAdmin() ? 'Admin' : 'Member' }}
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
                <div class="rounded-3xl ccims-panel p-6">
                    <p class="text-sm font-medium uppercase tracking-[0.25em] text-amber-200/80">Quick actions</p>
                    <h2 class="mt-3 font-[family-name:Space_Grotesk] text-2xl font-bold text-white">Manage the operations center</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-300">
                        The hub is linked to every core module so admins can move directly into members, payments, projects, loans, checkout, audit, and settings.
                    </p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <a href="{{ route('admin.members.index') }}" class="rounded-full bg-white px-4 py-2 text-center text-sm font-semibold text-slate-950 transition hover:bg-amber-100">
                            Member directory
                        </a>
                        <a href="{{ route('profile.edit') }}" class="rounded-full border border-white/10 px-4 py-2 text-center text-sm font-semibold text-white transition hover:bg-white/5">
                            Edit profile
                        </a>
                    </div>
                </div>

                <div class="rounded-3xl ccims-panel p-6">
                    <p class="text-sm font-medium uppercase tracking-[0.25em] text-slate-400">Session</p>
                    <div class="mt-4 space-y-3 text-sm text-slate-300">
                        <div class="flex items-center justify-between">
                            <span>Current user</span>
                            <span class="font-medium text-white">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Access level</span>
                            <span class="font-medium text-white">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Member' }}</span>
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
