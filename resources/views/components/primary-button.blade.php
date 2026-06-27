<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ccims-button-primary px-5 py-2.5 text-xs font-semibold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-amber-300 focus:ring-offset-2 focus:ring-offset-slate-950 active:bg-amber-500']) }}>
    {{ $slot }}
</button>
