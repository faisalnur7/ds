@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-full border border-amber-300/20 bg-amber-400 px-4 py-2 text-sm font-semibold text-slate-950 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-300 focus:ring-offset-2 focus:ring-offset-slate-950'
            : 'inline-flex items-center rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-[var(--ccims-text-soft)] transition duration-150 ease-in-out hover:bg-white/10 hover:text-[var(--ccims-text)] focus:outline-none focus:ring-2 focus:ring-amber-300 focus:ring-offset-2 focus:ring-offset-slate-950';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
