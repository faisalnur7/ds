<x-guest-layout>
    <div class="mb-4 text-sm text-slate-400">
        {{ __('Thanks for signing up. Before getting started, verify your email address using the link we just sent.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 text-sm font-medium text-emerald-300">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="rounded-md text-sm text-amber-200 underline decoration-amber-300/40 underline-offset-4 focus:outline-none focus:ring-2 focus:ring-amber-300 focus:ring-offset-2 focus:ring-offset-slate-950">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
