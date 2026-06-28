<x-app-layout>
    <x-slot name="header">
        <h2 class="font-[family-name:Space_Grotesk] text-xl font-semibold leading-tight text-white">
            {{ __('Member Dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <section class="rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-black/20 backdrop-blur-xl">
            <p class="text-sm font-medium uppercase tracking-[0.3em] text-amber-200/80">{{ __('Welcome') }}</p>
            <h3 class="mt-4 font-[family-name:Space_Grotesk] text-3xl font-bold text-white">
                {{ config('app.name', 'Darus Salam CCIMS') }}
            </h3>
            <p class="mt-4 max-w-3xl text-base leading-7 text-slate-300">
                {{ __('You are signed in to the member and operations portal. Use the navigation to review your profile and continue the CCIMS workflow.') }}
            </p>

        </section>
    </div>
</x-app-layout>
