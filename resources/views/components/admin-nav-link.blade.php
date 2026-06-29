@props(['href', 'active' => false])

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => 'group flex items-center gap-3 rounded-2xl px-3.5 py-3 text-sm font-medium transition',
    ]) }}
    @class([
        'bg-amber-400/10 text-amber-100 ring-1 ring-inset ring-amber-300/20 shadow-[inset_0_1px_0_rgba(255,255,255,0.04)]' => $active,
        'text-slate-300 hover:bg-white/5 hover:text-white' => ! $active,
    ])
>
    <span @class([
        'h-2.5 w-2.5 rounded-full bg-amber-300 shadow-[0_0_0_4px_rgba(251,191,36,0.15)]' => $active,
        'h-2.5 w-2.5 rounded-full bg-slate-500 group-hover:bg-slate-300' => ! $active,
    ])></span>
    <span class="truncate">{{ $slot }}</span>
</a>
