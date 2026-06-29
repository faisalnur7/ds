<script>
    (() => {
        const storageKey = 'ccims-theme';
        const storedMode = localStorage.getItem(storageKey) || 'system';
        const resolvedTheme = storedMode === 'system'
            ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
            : storedMode;

        document.documentElement.dataset.theme = resolvedTheme;
        document.documentElement.dataset.themeMode = storedMode;
        document.documentElement.style.colorScheme = resolvedTheme;
    })();
</script>
