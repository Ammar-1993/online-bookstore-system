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
                        class="px-2 py-1.5 rounded-lg hover:bg-indigo-50/70 dark:hover:bg-white/5">السلة</a>
                @endif
                @auth
                    @if (Route::has('orders.index'))
                        <a href="{{ route('orders.index') }}"
                            class="px-2 py-1.5 rounded-lg hover:bg-indigo-50/70 dark:hover:bg-white/5">طلباتي</a>
                    @endif
                @endauth
            </nav>

            {{-- أدوات --}}
            <div class="flex items-center gap-2">
                {{-- تبديل السِمة --}}
                @include('partials.theme-toggle-button')

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
</header>