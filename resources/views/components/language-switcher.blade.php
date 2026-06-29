@php
    $currentLocale = app()->getLocale();
    $locales = [
        'bn' => ['label' => 'বাংলা'],
        'en' => ['label' => 'English'],
    ];
@endphp

<div class="inline-flex items-center rounded-full border border-white/10 bg-white/5 p-0.5 text-slate-200 sm:p-1">
    @foreach ($locales as $locale => $meta)
        <form method="POST" action="{{ route('locale.switch', $locale) }}">
            @csrf
            <button
                type="submit"
                class="inline-flex h-8 items-center justify-center rounded-full px-2 text-[10px] font-semibold uppercase tracking-[0.14em] transition sm:h-9 sm:px-3 sm:text-xs sm:tracking-[0.18em]"
                @class([
                    'bg-amber-400 text-slate-950' => $currentLocale === $locale,
                    'text-slate-300 hover:text-white' => $currentLocale !== $locale,
                ])
                aria-label="{{ $meta['label'] }}"
                title="{{ $meta['label'] }}"
            >
                <span class="sm:hidden">{{ $locale === 'bn' ? 'BN' : 'EN' }}</span>
                <span class="hidden sm:inline">{{ $meta['label'] }}</span>
            </button>
        </form>
    @endforeach
</div>
