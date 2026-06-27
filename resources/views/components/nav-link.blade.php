@props(['active'])

@php
$classes = ($active ?? false)
            ? 'ccims-nav-link-active inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none'
            : 'ccims-nav-link inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium leading-5 focus:outline-none focus:border-amber-300 focus:text-[var(--ccims-text)]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
