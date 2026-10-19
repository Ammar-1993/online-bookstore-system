<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="color-scheme" content="light dark">
  <meta name="theme-color" media="(prefers-color-scheme: light)" content="#ffffff">
  <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#111827">

  <title>{{ config('app.name', 'Online Bookstore') }}</title>

  {{-- Fonts --}}
  <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  {{-- Theme Initialization (FOUC prevention) --}}
  @include('partials.theme-init')

  {{-- Vite / Livewire --}}
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
  <x-banner />

  {{-- شريط علوي موحّد --}}
  @php
    /** @var \App\Models\User|null $u */
    $u = auth()->user();

    // عدّاد السلة إن وُجدت خدمة Cart
    try {
      $cart = app(\App\Support\Cart::class);
      $cartCount = method_exists($cart, 'count') ? (int) $cart->count() : 0;
    } catch (\Throwable $e) {
      $cartCount = 0;
    }

    $isAdmin = $u?->hasRole('Admin');
    $isSeller = $u?->hasRole('Seller');

    // عدّاد المفضلة (للمسجّل) مع كاش خفيف
    $wishlistCount = 0;
    if ($u) {
      try {
        $wishlistCount = \Illuminate\Support\Facades\Cache::remember(
          'wishlist_count:' . $u->id,
          now()->addMinutes(10),
          fn() => method_exists($u, 'wishlistBooks') ? (int) $u->wishlistBooks()->count() : 0
        );
      } catch (\Throwable $e) {
        $wishlistCount = 0;
      }
    }

    // عدّاد المقارنة
    $compareCount = 0;
    if ($u) {
      try {
        $compareCount = \Illuminate\Support\Facades\Cache::remember(
          'compare_count:' . $u->id,
          now()->addMinutes(10),
          fn() => method_exists($u, 'compareBooks') ? (int) $u->compareBooks()->count() : 0
        );
      } catch (\Throwable $e) {
        $compareCount = 0;
      }
    } else {
      $compareCount = count(session('compare.items', []));
    }

  @endphp

  <header
    class="sticky top-0 z-40 backdrop-blur bg-white/90 dark:bg-gray-900/80 border-b border-black/5 dark:border-white/10">
    <div class="max-w-7xl mx-auto px-4">
      <div class="h-14 flex items-center justify-between gap-3">

        {{-- العلامة + العودة للرئيسية --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 group" data-ripple data-loader>
          <span class="inline-grid place-items-center w-8 h-8 rounded-xl bg-indigo-600 text-white shadow-sm">📘</span>
          <span class="font-semibold group-hover:text-indigo-700 dark:group-hover:text-indigo-300 transition">
            {{ config('app.name', 'المتجر الإلكتروني للكتب') }}
          </span>
        </a>

        {{-- البحث (يوجّه الآن لصفحة الكتب الجديدة) --}}
        <form action="{{ route('books.index') }}" method="GET"
          class="hidden md:flex items-center gap-2 flex-1 max-w-xl me-auto" role="search" aria-label="بحث عن الكتب">
          <input name="q" value="{{ request('q') }}" placeholder="ابحث عن كتاب أو ISBN"
            class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm" />
          <button class="px-3 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm">بحث</button>
        </form>

        {{-- إجراءات سريعة --}}
        <div class="flex items-center gap-1">

          {{-- زر الوضع الليلي --}}
          <button id="theme-toggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10"
            aria-label="تبديل السِمة" aria-pressed="false">
            <svg id="icon-sun" class="w-5 h-5 hidden dark:block" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path
                d="M12 4v2m0 12v2M4 12H2m20 0h-2M5 5l1.5 1.5M17.5 17.5L19 19M5 19l1.5-1.5M17.5 6.5L19 5M12 8a4 4 0 1 1 0 8 4 4 0 0 1 0-8Z"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            <svg id="icon-moon" class="w-5 h-5 dark:hidden" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" />
            </svg>
          </button>

          {{-- رابط المفضّلة (للمسجّل يظهر العدّاد) --}}
          <a href="{{ auth()->check() ? route('wishlist.index') : route('login') }}"
            class="relative inline-flex items-center gap-2 px-2 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-white/10"
            data-ripple data-loader aria-label="المفضّلة" title="المفضّلة">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path
                d="M12 21s-6.716-3.686-9.192-7.394C.365 10.28 2.09 6.5 5.6 6.5c2.01 0 3.086 1.13 3.9 2.208C10.314 7.63 11.39 6.5 13.4 6.5c3.51 0 5.235 3.78 2.792 7.106C18.716 17.314 12 21 12 21Z"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            @if($wishlistCount > 0)
              <span
                class="absolute -top-1 -end-1 inline-flex items-center justify-center text-[11px] min-w-[18px] h-[18px] px-1 rounded-full bg-rose-600 text-white tabular-nums">
                {{ $wishlistCount }}
              </span>
            @endif
          </a>

          <a href="{{ route('compare.index') }}"
            class="relative inline-flex items-center gap-2 px-2 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-white/10"
            data-ripple data-loader aria-label="المقارنة" title="المقارنة">
            {{-- أيقونة موازين --}}
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M12 3v18M3 7h18M7 7l3 5H4l3-5Zm10 0l3 5h-6l3-5Z" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" />
            </svg>
            <span id="compare-count"
              class="{{ $compareCount ? 'absolute -top-1 -end-1 inline-flex items-center justify-center text-[11px] min-w-[18px] h-[18px] px-1 rounded-full bg-indigo-600 text-white tabular-nums' : 'hidden' }}">
              {{ $compareCount }}
            </span>
          </a>


          {{-- رابط السلة --}}
          <a href="{{ route('cart.index') }}"
            class="relative inline-flex items-center gap-2 px-2 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-white/10"
            data-ripple data-loader aria-label="السلة" title="السلة">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M6 6h15l-1.5 9h-12L5 3H2" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" />
            </svg>
            @if($cartCount > 0)
              <span
                class="absolute -top-1 -end-1 inline-flex items-center justify-center text-[11px] min-w-[18px] h-[18px] px-1 rounded-full bg-rose-600 text-white tabular-nums">
                {{ $cartCount }}
              </span>
            @endif
          </a>

          {{-- قائمة المستخدم (ضيف/مُسجّل) --}}
          @guest
            <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-white/10 text-sm"
              data-ripple data-loader>دخول</a>
            <a href="{{ route('register') }}"
              class="px-3 py-1.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 text-sm" data-ripple
              data-loader>تسجيل</a>
          @else
            <div class="relative">
              <button id="top-user-btn"
                class="flex items-center gap-2 ps-2 pe-3 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-white/10"
                aria-haspopup="menu" aria-expanded="false">
                <span class="inline-grid place-items-center w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-800 text-sm">
                  {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($u?->name ?? 'U', 0, 1)) }}
                </span>
                <span class="hidden sm:block text-sm">{{ $u?->name }}</span>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
              </button>

              {{-- القائمة المنبثقة --}}
              <div id="top-user-menu"
                class="absolute end-0 mt-2 w-56 rounded-xl overflow-hidden border border-black/5 dark:border-white/10 bg-white dark:bg-gray-900 shadow-lg hidden"
                role="menu" aria-labelledby="top-user-btn">
                <div class="px-3 pt-3 pb-2">
                  <div class="font-medium">{{ $u?->name }}</div>
                  <div class="text-xs text-gray-500 dark:text-gray-400 truncate" dir="ltr">{{ $u?->email }}</div>
                </div>
                <div class="border-t border-black/5 dark:border-white/10"></div>

                {{-- روابط الحساب --}}
                <div class="p-2 text-sm">
                  <a href="{{ route('account.index') }}"
                    class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10" data-ripple
                    role="menuitem">
                    الملف الشخصي / لوحة حسابي
                  </a>
                  <a href="{{ route('orders.index') }}"
                    class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10" data-ripple
                    role="menuitem">
                    طلباتي
                  </a>
                  <a href="{{ route('wishlist.index') }}"
                    class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10" data-ripple
                    role="menuitem">
                    المفضّلة
                  </a>

                  @if($isAdmin || $isSeller)
                    <a href="{{ route('admin.dashboard') }}"
                      class="block px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10" data-ripple data-loader
                      role="menuitem">
                      لوحة التحكم
                    </a>
                  @endif
                </div>

                <div class="border-t border-black/5 dark:border-white/10"></div>

                <form method="POST" action="{{ route('logout') }}" class="p-2">
                  @csrf
                  <button
                    class="w-full text-start px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-rose-700"
                    data-ripple role="menuitem">
                    تسجيل الخروج
                  </button>
                </form>
              </div>
            </div>
          @endguest
        </div>
      </div>

      {{-- بحث للجوال (يستخدم صفحة الكتب أيضاً) --}}
      <form action="{{ route('books.index') }}" method="GET" class="md:hidden pb-3" role="search"
        aria-label="بحث عن الكتب للجوال">
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

  {{-- سكربتات صغيرة: القائمة --}}
  <script>
    // قائمة المستخدم (تحسين a11y: aria-expanded + الإغلاق بالمحيط وEscape)
    (() => {
      const btn = document.getElementById('top-user-btn');
      const menu = document.getElementById('top-user-menu');
      const open = () => { menu?.classList.remove('hidden'); btn?.setAttribute('aria-expanded', 'true'); };
      const close = () => { menu?.classList.add('hidden'); btn?.setAttribute('aria-expanded', 'false'); };
      btn?.addEventListener('click', (e) => {
        e.preventDefault();
        (menu?.classList.contains('hidden') ? open : close)();
      });
      document.addEventListener('click', (e) => {
        if (!btn?.contains(e.target) && !menu?.contains(e.target)) close();
      });
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
    })();
  </script>
</body>

</html>