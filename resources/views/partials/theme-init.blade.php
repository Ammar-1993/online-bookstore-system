{{-- resources/views/partials/theme-init.blade.php --}}
<script>
  (() => {
    const getIsDark = () => {
      try {
        const saved = localStorage.getItem('theme');
        if (saved === 'dark' || saved === 'light') {
          return saved === 'dark';
        }
      } catch (_) {}
      return !!(window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
    };

    const syncButtons = (isDark) => {
      document.querySelectorAll('#theme-toggle, [data-theme-toggle]').forEach((btn) => {
        btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        btn.querySelector('.sun, #icon-sun')?.classList.toggle('hidden', !isDark);
        btn.querySelector('.moon, #icon-moon')?.classList.toggle('hidden', isDark);
      });
    };

    const applyTheme = (isDark) => {
      document.documentElement.classList.toggle('dark', isDark);
      syncButtons(isDark);
    };

    applyTheme(getIsDark());

    if (!window.__themeControllerInit) {
      window.__themeControllerInit = true;
      window.__syncThemeUI = () => syncButtons(document.documentElement.classList.contains('dark'));

      document.addEventListener('DOMContentLoaded', () => {
        window.__syncThemeUI();
      });

      document.addEventListener('click', (e) => {
        const btn = e.target.closest('#theme-toggle, [data-theme-toggle]');
        if (!btn) return;
        const isDark = !document.documentElement.classList.contains('dark');
        applyTheme(isDark);
        try {
          localStorage.setItem('theme', isDark ? 'dark' : 'light');
        } catch (_) {}
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: isDark } }));
      }, true);
    }
  })();
</script>
