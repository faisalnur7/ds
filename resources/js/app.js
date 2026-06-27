import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

const themeStorageKey = 'ccims-theme';

const resolveTheme = (mode) => {
    if (mode === 'system') {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    return mode;
};

const applyTheme = (mode) => {
    const resolved = resolveTheme(mode);

    document.documentElement.dataset.theme = resolved;
    document.documentElement.dataset.themeMode = mode;
    document.documentElement.style.colorScheme = resolved;
};

Alpine.store('theme', {
    mode: localStorage.getItem(themeStorageKey) || 'system',

    init() {
        this.sync();

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (this.mode === 'system') {
                this.sync();
            }
        });
    },

    sync() {
        applyTheme(this.mode);
    },

    set(mode) {
        this.mode = mode;
        localStorage.setItem(themeStorageKey, mode);
        this.sync();
    },

    is(mode) {
        return this.mode === mode;
    },
});

Alpine.start();
