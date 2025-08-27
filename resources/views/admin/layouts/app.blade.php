<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'المتجر الإلكتروني للكتب') - Online Bookstore</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900">

  {{-- شريط علوي --}}
  <header class="bg-white border-b">
    @php
      $u = auth()->user();
      $isAdmin = $u?->hasRole('Admin');
      $isSeller = $u?->hasRole('Seller');
      $is = fn(string $pat) => request()->routeIs($pat);
      $active = 'text-indigo-700 dark:text-indigo-300 bg-indigo-50/70 dark:bg-white/5 ring-1 ring-inset ring-indigo-600/20';
      $base = 'inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-sm text-gray-700 dark:text-gray-200 hover:text-indigo-700 dark:hover:text-indigo-300 hover:bg-indigo-50/70 dark:hover:bg-white/5 transition';
    @endphp

    <header
      class="sticky top-0 z-40 backdrop-blur bg-white/80 dark:bg-gray-900/70 border-b border-black/5 dark:border-white/10">
      <div class="max-w-7xl mx-auto px-4">
        <div class="h-14 flex items-center justify-between gap-3">
          {{-- Brand + Burger --}}
          <div class="flex items-center gap-3">
            <button id="adm-burger" class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10"
              aria-label="القائمة">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none">
                <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
              </svg>
            </button>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 group" data-ripple data-loader>
              <span
                class="inline-grid place-items-center w-8 h-8 rounded-xl bg-indigo-600 text-white shadow-sm">📘</span>
              <span
                class="font-display font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-700 dark:group-hover:text-indigo-300 transition">
                المتجر الإلكتروني للكتب
              </span>
            </a>
          </div>

          {{-- Nav (desktop) --}}
          <nav class="hidden md:flex items-center gap-1">
            <a class="{{ $base }} {{ $is('admin.dashboard') ? $active : '' }}"
              href="{{ route('admin.dashboard') }}">لوحة التحكم</a>

            @if($isAdmin)
              <a class="{{ $base }} {{ $is('admin.categories.*') ? $active : '' }}"
                href="{{ route('admin.categories.index') }}">التصنيفات</a>
              <a class="{{ $base }} {{ $is('admin.publishers.*') ? $active : '' }}"
                href="{{ route('admin.publishers.index') }}">الناشرون</a>
              <a class="{{ $base }} {{ $is('admin.authors.*') ? $active : '' }}"
                href="{{ route('admin.authors.index') }}">المؤلفون</a>
              <a class="{{ $base }} {{ $is('admin.users.*') ? $active : '' }}"
                href="{{ route('admin.users.index') }}">المستخدمون</a>
              <a class="{{ $base }} {{ $is('admin.reviews.*') ? $active : '' }}"
                href="{{ route('admin.reviews.index') }}">المراجعات</a>
              <a class="{{ $base }} {{ $is('admin.orders.*') ? $active : '' }}"
                href="{{ route('admin.orders.index') }}">الطلبات</a>
            @endif

            @if($isSeller && !$isAdmin)
              <a class="{{ $base }} {{ $is('admin.reviews.*') ? $active : '' }}"
                href="{{ route('admin.reviews.index') }}">مراجعات كتبي</a>
            @endif

            {{-- للجميع --}}
            <a class="{{ $base }} {{ $is('admin.books.*') ? $active : '' }}"
              href="{{ route('admin.books.index') }}">الكتب</a>
          </nav>

          {{-- Actions --}}
          <div class="flex items-center gap-2">
            <a href="{{ route('admin.books.create') }}"
              class="hidden sm:inline-flex items-center gap-2 rounded-xl px-3 py-1.5 bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm"
              data-ripple data-loader>
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
              </svg>
              إضافة كتاب
            </a>

            <a href="{{ route('home') }}"
              class="inline-flex items-center rounded-full px-2.5 py-1 text-xs bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300"
              data-ripple data-loader>
              الرئيسية
            </a>

            {{-- تبديل الوضع --}}
            <button id="theme-toggle" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10"
              aria-label="تبديل السِمة">
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

            {{-- قائمة المستخدم --}}
            <div class="relative">
              <button id="user-menu-btn"
                class="flex items-center gap-2 ps-2 pe-3 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-white/10">
                <span class="inline-grid place-items-center w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-800 text-sm">
                  {{ Str::upper(Str::substr($u?->name ?? 'U', 0, 1)) }}
                </span>
                <span class="hidden sm:block text-sm">{{ $u?->name }}</span>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                  <path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
              </button>
              <div id="user-menu"
                class="absolute end-0 mt-2 w-48 rounded-xl overflow-hidden border border-black/5 dark:border-white/10 bg-white dark:bg-gray-900 shadow-lg hidden">
                <div class="px-3 py-2 text-xs text-gray-500 dark:text-gray-400">مسجّل دخولاً</div>
                <div class="px-3 pb-2 text-sm truncate">{{ $u?->email }}</div>
                <div class="border-t border-black/5 dark:border-white/10"></div>
                <form method="POST" action="{{ route('logout') }}" class="p-2">@csrf
                  <button
                    class="w-full text-start px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-sm"
                    data-ripple>
                    تسجيل الخروج
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>

        {{-- Nav (mobile) --}}
        <div id="adm-mobile" class="md:hidden hidden pb-3">
          <nav class="grid gap-1 pt-2">
            <a class="{{ $base }} {{ $is('admin.dashboard') ? $active : '' }}"
              href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
            @if($isAdmin)
              <a class="{{ $base }} {{ $is('admin.categories.*') ? $active : '' }}"
                href="{{ route('admin.categories.index') }}">التصنيفات</a>
              <a class="{{ $base }} {{ $is('admin.publishers.*') ? $active : '' }}"
                href="{{ route('admin.publishers.index') }}">الناشرون</a>
              <a class="{{ $base }} {{ $is('admin.authors.*') ? $active : '' }}"
                href="{{ route('admin.authors.index') }}">المؤلفون</a>
              <a class="{{ $base }} {{ $is('admin.users.*') ? $active : '' }}"
                href="{{ route('admin.users.index') }}">المستخدمون</a>
              <a class="{{ $base }} {{ $is('admin.reviews.*') ? $active : '' }}"
                href="{{ route('admin.reviews.index') }}">المراجعات</a>
              <a class="{{ $base }} {{ $is('admin.orders.*') ? $active : '' }}"
                href="{{ route('admin.orders.index') }}">الطلبات</a>
            @endif
            @if($isSeller && !$isAdmin)
              <a class="{{ $base }} {{ $is('admin.reviews.*') ? $active : '' }}"
                href="{{ route('admin.reviews.index') }}">مراجعات كتبي</a>
            @endif
            <a class="{{ $base }} {{ $is('admin.books.*') ? $active : '' }}"
              href="{{ route('admin.books.index') }}">الكتب</a>

            <a href="{{ route('admin.books.create') }}"
              class="mt-1 inline-flex items-center gap-2 rounded-xl px-3 py-2 bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm"
              data-ripple data-loader>
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
              </svg>
              إضافة كتاب
            </a>
          </nav>
        </div>
      </div>
    </header>

    {{-- سكربتات صغيرة للتحكم بالواجهات --}}
    <script>
      // burger
      document.getElementById('adm-burger')?.addEventListener('click', () => {
        document.getElementById('adm-mobile')?.classList.toggle('hidden');
      });

      // user menu
      const ubtn = document.getElementById('user-menu-btn');
      const umenu = document.getElementById('user-menu');
      ubtn?.addEventListener('click', () => umenu?.classList.toggle('hidden'));
      document.addEventListener('click', (e) => {
        if (!ubtn?.contains(e.target) && !umenu?.contains(e.target)) umenu?.classList.add('hidden');
      });

      // theme toggle (مع حفظ التفضيل)
      document.getElementById('theme-toggle')?.addEventListener('click', () => {
        const root = document.documentElement;
        const isDark = root.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
      });
    </script>

  </header>

  {{-- محتوى الصفحة --}}
  <main class="max-w-7xl mx-auto px-4 py-6">
    {{-- ⚠️ عنصر فلاش واحد فقط هنا --}}
    <x-flash-stack />

    @yield('content')
  </main>

  {{-- داخل الـ body قبل إغلاقه مثلاً --}}
  <x-page-loader />

</body>

</html>