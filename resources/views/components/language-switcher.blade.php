@php
    $currentLocale = app()->getLocale();
    $locales = [
        'bn' => ['label' => 'বাংলা'],
        'en' => ['label' => 'English'],
    ];
@endphp

<div class="inline-flex items-center rounded-full border border-white/10 bg-white/5 p-1 text-slate-200">
    @foreach ($locales as $locale => $meta)
        <form method="POST" action="{{ route('locale.switch', $locale) }}">
            @csrf
            <button
                type="submit"
                class="inline-flex h-9 items-center justify-center rounded-full px-3 text-xs font-semibold uppercase tracking-[0.18em] transition"
                @class([
                    'bg-amber-400 text-slate-950' => $currentLocale === $locale,
                    'text-slate-300 hover:text-white' => $currentLocale !== $locale,
                ])
                aria-label="{{ $meta['label'] }}"
                title="{{ $meta['label'] }}"
            >
                {{ $meta['label'] }}
            </button>
        </form>
    @endforeach
</div>
