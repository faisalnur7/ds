<x-guest-layout>
    <div class="mb-8">
        <p class="text-sm font-medium uppercase tracking-[0.25em] text-cyan-200/80">Welcome back</p>
        <h2 class="mt-3 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">Log in to continue</h2>
        <p class="mt-2 text-sm leading-6 text-slate-400">
            Demo admin credentials: <span class="font-medium text-slate-200">admin@example.com</span> / <span class="font-medium text-slate-200">password</span>
        </p>
    </div>

    <x-auth-session-status class="mb-4 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-200" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-slate-200" />
            <x-text-input id="email" class="mt-2 block w-full rounded-2xl border-white/10 bg-white/5 px-4 py-3 text-slate-100 shadow-none placeholder:text-slate-500 focus:border-cyan-400 focus:ring-cyan-400" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" class="text-slate-200" />
            <x-text-input id="password" class="mt-2 block w-full rounded-2xl border-white/10 bg-white/5 px-4 py-3 text-slate-100 shadow-none placeholder:text-slate-500 focus:border-cyan-400 focus:ring-cyan-400" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-300">
                <input id="remember_me" type="checkbox" class="rounded border-white/10 bg-white/5 text-cyan-500 shadow-sm focus:ring-cyan-400" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-cyan-200 transition hover:text-cyan-100" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="flex w-full justify-center rounded-2xl bg-cyan-400 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-300">
            {{ __('Log in') }}
        </x-primary-button>
    </form>
</x-guest-layout>
