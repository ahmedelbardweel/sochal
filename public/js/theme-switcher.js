(function () {
    // Theme Manager
    const ThemeManager = {
        init() {
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },

        set(theme) {
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            localStorage.setItem('theme', theme);
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme } }));
        },

        toggle() {
            const isDark = document.documentElement.classList.toggle('dark');
            const theme = isDark ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme } }));
        }
    };

    // Initialize immediately
    ThemeManager.init();

    // Expose via namespace to avoid collisions
    window.NeuralTheme = {
        set: ThemeManager.set,
        toggle: ThemeManager.toggle
    };
})();
