@php
    $u = auth()->user();
@endphp

<header
    class="sticky top-0 z-40 backdrop-blur bg-white/80 dark:bg-gray-900/70 border-b border-black/5 dark:border-white/10">
    <div class="max-w-7xl mx-auto px-4">
        <div class="h-14 flex items-center justify-between gap-3">
            {{-- Brand --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 group" data-ripple data-loader>
                <span
                    class="inline-grid place-items-center w-8 h-8 rounded-xl bg-indigo-600 text-white shadow-sm">📘</span>
                <span
                    class="font-display font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-700 dark:group-hover:text-indigo-300 transition">
                    المتجر الإلكتروني للكتب
                </span>
            </a>

            {{-- روابط بسيطة --}}
            <nav class="hidden md:flex items-center gap-2 text-sm">
                <a href="{{ route('home') }}"
                    class="px-2 py-1.5 rounded-lg hover:bg-indigo-50/70 dark:hover:bg-white/5">الرئيسية</a>
                @if (Route::has('cart.index'))
                    <a href="{{ route('cart.index') }}"
                        class="px-2 py-1.5 rounded-lg hover:bg-indigo-50/70 dark:hover:bg:white/5">السلة</a>
                @endif
                @auth
                    @if (Route::has('orders.index'))
                        <a href="{{ route('orders.index') }}"
                            class="px-2 py-1.5 rounded-lg hover:bg-indigo-50/70 dark:hover:bg:white/5">طلباتي</a>
                    @endif
                @endauth
            </nav>

            {{-- أدوات --}}
            <div class="flex items-center gap-2">
                {{-- تبديل السِمة --}}
                <button id="theme-toggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg:white/10"
                    aria-label="تبديل السمة">
                    <svg id="icon-sun" class="w-5 h-5 hidden dark:block" viewBox="0 0 24 24" fill="none">
                        <path
                            d="M12 4v2m0 12v2M4 12H2m20 0h-2M5 5l1.5 1.5M17.5 17.5L19 19M5 19l1.5-1.5M17.5 6.5L19 5M12 8a4 4 0 1 1 0 8 4 4 0 0 1 0-8Z"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    <svg id="icon-moon" class="w-5 h-5 dark:hidden" viewBox="0 0 24 24" fill="none">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" />
                    </svg>
                </button>

                @auth
                    {{-- قائمة المستخدم مثل لوحة الإدارة --}}
                    @include('partials.user-menu')
                @else
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}"
                            class="rounded-xl px-3 py-1.5 bg-indigo-600 text-white hover:bg-indigo-700">تسجيل الدخول</a>
                    @endif
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="hidden sm:inline-flex rounded-xl px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">إنشاء
                            حساب</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <script>
        // Theme toggle
        document.getElementById('theme-toggle')?.addEventListener('click', () => {
            const root = document.documentElement;
            const isDark = root.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
        // Apply saved theme on load
        (function () {
            const pref = localStorage.getItem('theme');
            if (pref) document.documentElement.classList.toggle('dark', pref === 'dark');
        })();
    </script>
</header>