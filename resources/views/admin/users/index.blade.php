@extends('layouts.admin')

@section('title', 'Members')
@section('header', 'Member Registry')

@section('content')
    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-6 shadow-2xl shadow-black/20 backdrop-blur">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="font-[family-name:Space_Grotesk] text-2xl font-bold text-white">Members</h2>
                <p class="mt-1 text-sm text-slate-400">Manage accounts from a clean, responsive registry.</p>
            </div>
            <div class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-300">
                {{ $users->total() }} total records
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-2xl border border-white/10">
            <table class="min-w-full divide-y divide-white/10">
                <thead class="bg-white/5">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10 bg-slate-950/40">
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-4 py-4">
                                <div class="font-medium text-white">{{ $user->name }}</div>
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-300">{{ $user->email }}</td>
                            <td class="px-4 py-4">
                                <span @class([
                                    'inline-flex rounded-full px-3 py-1 text-xs font-semibold',
                                    'bg-amber-400/10 text-amber-200' => $user->isAdmin(),
                                    'bg-white/5 text-slate-300' => ! $user->isAdmin(),
                                ])>
                                    {{ $user->isAdmin() ? 'Admin' : 'Member' }}
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

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
@endsection
