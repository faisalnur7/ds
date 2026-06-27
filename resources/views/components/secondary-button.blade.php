<button {{ $attributes->merge(['type' => 'button', 'class' => 'ccims-button-secondary px-4 py-2 text-xs font-semibold uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-amber-300 focus:ring-offset-2 focus:ring-offset-slate-950 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
