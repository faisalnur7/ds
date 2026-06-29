<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ccims-button-danger rounded-[1.25rem] px-4 py-3 text-sm font-semibold tracking-wide focus:outline-none focus:ring-2 focus:ring-rose-400 focus:ring-offset-2 focus:ring-offset-slate-950 active:bg-rose-600']) }}>
    {{ $slot }}
</button>
