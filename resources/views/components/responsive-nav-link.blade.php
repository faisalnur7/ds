@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-amber-300 bg-amber-400/10 py-2 pe-4 ps-3 text-start text-base font-medium text-amber-200 transition duration-150 ease-in-out focus:outline-none focus:bg-amber-400/15 focus:text-amber-100 focus:border-amber-300'
            : 'block w-full border-l-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-medium text-[var(--ccims-text-soft)] transition duration-150 ease-in-out hover:border-amber-200/40 hover:bg-white/5 hover:text-[var(--ccims-text)] focus:outline-none focus:border-amber-300 focus:bg-white/5 focus:text-[var(--ccims-text)]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
