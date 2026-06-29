<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ccims-button-primary rounded-[1.25rem] px-5 py-3 text-sm font-semibold tracking-wide focus:outline-none focus:ring-2 focus:ring-amber-300 focus:ring-offset-2 focus:ring-offset-slate-950 active:bg-amber-500']) }}>
    {{ $slot }}
</button>
