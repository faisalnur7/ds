<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ccims-button-danger px-4 py-2 text-xs font-semibold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2 focus:ring-offset-slate-950 active:bg-rose-600']) }}>
    {{ $slot }}
</button>
