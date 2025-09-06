<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Online Bookstore') }}</title>

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  {{-- Vite / Livewire --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900">
  <x-banner />

  {{-- شريط علوي موحّد --}}
  @php
    $u = auth()->user();
    // عدّاد السلة إن وُجدت خدمة Cart
    try {
      $cart = app(\App\Support\Cart::class);
      $cartCount = method_exists($cart,'count') ? (int) $cart->count() : 0;
    } catch (\Throwable $e) { $cartCount = 0; }

    $isAdmin  = $u?->hasRole('Admin');
    $isSeller = $u?->hasRole('Seller');
  @endphp

  <header class="sticky top-0 z-40 backdrop-blur bg-white/90 dark:bg-gray-900/80 border-b border-black/5 dark:border-white/10">
    <div class="max-w-7xl mx-auto px-4">
      <div class="h-14 flex items-center justify-between gap-3">

        {{-- العلامة + العودة للرئيسية --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 group" data-ripple data-loader>
          <span class="inline-grid place-items-center w-8 h-8 rounded-xl bg-indigo-600 text-white shadow-sm">📘</span>
          <span class="font-semibold group-hover:text-indigo-700 dark:group-hover:text-indigo-300 transition">
            {{ config('app.name', 'المتجر الإلكتروني للكتب') }}
          </span>
        </a>

        {{-- البحث (يرسل إلى الصفحة الرئيسية مع q) --}}
        <form action="{{ route('home') }}" method="GET" class="hidden md:flex items-center gap-2 flex-1 max-w-xl me-auto">
          <input name="q" value="{{ request('q') }}" placeholder="ابحث عن كتاب أو ISBN"
                 class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm" />
          <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">بحث</button>
        </form>

        {{-- إجراءات سريعة --}}
        <div class="flex items-center gap-1">

          {{-- زر الوضع الليلي --}}
          <button id="theme-toggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10" aria-label="تبديل السِمة">
            <svg id="icon-sun" class="w-5 h-5 hidden dark:block" viewBox="0 0 24 24" fill="none">
              <path d="M12 4v2m0 12v2M4 12H2m20 0h-2M5 5l1.5 1.5M17.5 17.5L19 19M5 19l1.5-1.5M17.5 6.5L19 5M12 8a4 4 0 1 1 0 8 4 4 0 0 1 0-8Z"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            <svg id="icon-moon" class="w-5 h-5 dark:hidden" viewBox="0 0 24 24" fill="none">
              <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
          </button>

          {{-- رابط السلة --}}
          <a href="{{ route('cart.index') }}"
             class="relative inline-flex items-center gap-2 px-2 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-white/10"
             data-ripple data-loader aria-label="السلة">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M6 6h15l-1.5 9h-12L5 3H2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            @if($cartCount > 0)
              <span class="absolute -top-1 -end-1 inline-flex items-center justify-center text-[11px] min-w-[18px] h-[18px] px-1 rounded-full bg-rose-600 text-white tabular-nums">
                {{ $cartCount }}
              </span>
            @endif
          </a>

          {{-- قائمة المستخدم (ضيف/مُسجّل) --}}
          @guest
            <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-white/10 text-sm" data-ripple data-loader>دخول</a>
            <a href="{{ route('register') }}" class="px-3 py-1.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm" data-ripple data-loader>تسجيل</a>
          @else
            <div class="relative">
              <button id="top-user-btn"
                      class="flex items-center gap-2 ps-2 pe-3 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-white/10">
                <span class="inline-grid place-items-center w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-800 text-sm">
                  {{ Str::upper(Str::substr($u?->name ?? 'U', 0, 1)) }}
                </span>
                <span class="hidden sm:block text-sm">{{ $u?->name }}</span>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                  <path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </button>

              {{-- القائمة المنبثقة --}}
              <div id="top-user-menu"
                   class="absolute end-0 mt-2 w-56 rounded-xl overflow-hidden border border-black/5 dark:border-white/10 bg-white dark:bg-gray-900 shadow-lg hidden">
                <div class="px-3 pt-3 pb-2">
                  <div class="font-medium">{{ $u?->name }}</div>
                  <div class="text-xs text-gray-500 dark:text-gray-400 truncate" dir="ltr">{{ $u?->email }}</div>
                </div>
                <div class="border-t border-black/5 dark:border-white/10"></div>

                {{-- روابط الحساب --}}
                <div class="p-2 text-sm">
                  <a href="{{ route('account.index') }}"
                     class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10" data-ripple>
                    الملف الشخصي / لوحة حسابي
                  </a>
                  <a href="{{ route('orders.index') }}"
                     class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10" data-ripple>
                    طلباتي
                  </a>

                  @if($isAdmin || $isSeller)
                    <a href="{{ route('admin.dashboard') }}"
                       class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10" data-ripple data-loader>
                      لوحة التحكم
                    </a>
                  @endif
                </div>

                <div class="border-t border-black/5 dark:border-white/10"></div>

                <form method="POST" action="{{ route('logout') }}" class="p-2">@csrf
                  <button class="w-full text-start px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-rose-700" data-ripple>
                    تسجيل الخروج
                  </button>
                </form>
              </div>
            </div>
          @endguest
        </div>
      </div>

      {{-- بحث للجوال --}}
      <form action="{{ route('home') }}" method="GET" class="md:hidden pb-3">
        <input name="q" value="{{ request('q') }}" placeholder="ابحث عن كتاب أو ISBN"
               class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm" />
      </form>
    </div>
  </header>

  {{-- ترويسة الصفحة الاختيارية من Jetstream --}}
  @isset($header)
    <header class="bg-white dark:bg-gray-900 shadow-sm">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{ $header }}
      </div>
    </header>
  @endisset

  {{-- محتوى الصفحة --}}
  <main class="min-h-[calc(100vh-56px)]">
    <x-flash-stack />
    @isset($slot)
      {{ $slot }}
    @else
      @yield('content')
    @endisset
  </main>

  {{-- مودالات --}}
  @stack('modals')

  @livewireScripts

  {{-- محمّل الصفحة الدائري الموحد --}}
  <x-page-loader />

  {{-- سكربتات صغيرة: القائمة/الوضع --}}
  <script>
    // تفضيل السمة
    (() => {
      const root = document.documentElement;
      const saved = localStorage.getItem('theme');
      if (saved) {
        root.classList.toggle('dark', saved === 'dark');
      } else {
        // افتراضيًا اتبع تفضيل النظام
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        root.classList.toggle('dark', prefersDark);
      }
      const btn = document.getElementById('theme-toggle');
      btn?.addEventListener('click', () => {
        const isDark = root.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
      });
    })();

    // قائمة المستخدم
    (() => {
      const btn = document.getElementById('top-user-btn');
      const menu = document.getElementById('top-user-menu');
      const toggle = () => menu?.classList.toggle('hidden');
      btn?.addEventListener('click', toggle);
      document.addEventListener('click', (e) => {
        if (!btn?.contains(e.target) && !menu?.contains(e.target)) menu?.classList.add('hidden');
      });
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') menu?.classList.add('hidden'); });
    })();
  </script>
</body>
</html>
