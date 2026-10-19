{{-- resources/views/partials/theme-toggle-button.blade.php --}}
<button id="theme-toggle" type="button" data-theme-toggle data-no-loader
        class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-white/10 text-gray-700 dark:text-gray-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition-colors"
        title="تبديل السمة" aria-label="تبديل السمة" aria-pressed="false" data-ripple>
    <svg id="icon-sun" class="w-5 h-5 sun hidden dark:block" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M12 4v2m0 12v2M4 12H2m20 0h-2M5 5l1.5 1.5M17.5 17.5L19 19M5 19l1.5-1.5M17.5 6.5L19 5M12 8a4 4 0 1 1 0 8 4 4 0 0 1 0-8Z"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" />
    </svg>
    <svg id="icon-moon" class="w-5 h-5 moon dark:hidden" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" />
    </svg>
</button>
