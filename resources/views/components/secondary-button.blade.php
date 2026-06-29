<button {{ $attributes->merge(['type' => 'button', 'class' => 'ccims-button-secondary rounded-[1.25rem] px-4 py-3 text-sm font-semibold tracking-wide focus:outline-none focus:ring-2 focus:ring-amber-300 focus:ring-offset-2 focus:ring-offset-slate-950 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
