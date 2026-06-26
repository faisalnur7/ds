<div x-data class="inline-flex items-center rounded-full border border-white/10 bg-white/5 p-1 text-slate-200">
    <button
        type="button"
        class="inline-flex h-9 w-9 items-center justify-center rounded-full transition"
        :class="$store.theme.is('light') ? 'bg-amber-400 text-slate-950' : 'text-slate-300 hover:text-white'"
        @click="$store.theme.set('light')"
        aria-label="Use light theme"
        title="Light"
    >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
            <circle cx="12" cy="12" r="4" />
            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke-linecap="round" />
        </svg>
    </button>
    <button
        type="button"
        class="inline-flex h-9 w-9 items-center justify-center rounded-full transition"
        :class="$store.theme.is('dark') ? 'bg-amber-400 text-slate-950' : 'text-slate-300 hover:text-white'"
        @click="$store.theme.set('dark')"
        aria-label="Use dark theme"
        title="Dark"
    >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
            <path d="M21 12.8A8.5 8.5 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </button>
    <button
        type="button"
        class="inline-flex h-9 w-9 items-center justify-center rounded-full transition"
        :class="$store.theme.is('system') ? 'bg-amber-400 text-slate-950' : 'text-slate-300 hover:text-white'"
        @click="$store.theme.set('system')"
        aria-label="Use system theme"
        title="System"
    >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
            <rect x="3" y="5" width="18" height="12" rx="2" />
            <path d="M8 19h8M10 17h4" stroke-linecap="round" />
        </svg>
    </button>
</div>
