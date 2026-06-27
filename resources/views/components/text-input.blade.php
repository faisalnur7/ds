@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'ccims-input px-4 py-3 text-sm shadow-none']) }}>
