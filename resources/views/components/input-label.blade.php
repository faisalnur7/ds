@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-[var(--ccims-text-soft)]']) }}>
    {{ $value ?? $slot }}
</label>
