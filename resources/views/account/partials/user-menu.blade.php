@php
  /** @var \App\Models\User|null $u */
  $u = auth()->user();
  $isAdmin  = $u?->hasRole('Admin') || $u?->hasRole('Seller');
@endphp

<div class="relative" x-data="{open:false}" @keydown.escape.window="open=false">
  <button @click="open=!open" class="flex items-center gap-2 ps-2 pe-3 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-white/10">
    <span class="inline-grid place-items-center w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-800 text-sm">
      {{ mb_strtoupper(mb_substr($u?->name ?? $u?->email ?? 'U', 0, 1)) }}
    </span>
    <span class="hidden sm:block text-sm">{{ $u?->name ?? '—' }}</span>
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M7 10l5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>
  </button>

  <div x-cloak x-show="open" @click.outside="open=false"
       class="absolute end-0 mt-2 w-56 rounded-xl overflow-hidden border border-black/5 dark:border-white/10 bg-white dark:bg-gray-900 shadow-lg"
       role="menu" aria-hidden="true">
    {{-- رأس: الاسم + الإيميل --}}
    <div class="px-3 py-3 flex items-center gap-3">
      <div class="h-9 w-9 rounded-full bg-indigo-600 text-white grid place-items-center text-sm">
        {{ mb_substr($u?->name ?? $u?->email ?? 'U', 0, 1) }}
      </div>
      <div class="min-w-0">
        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $u?->name ?? '—' }}</div>
        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $u?->email }}</div>
      </div>
    </div>

    <div class="border-t border-black/5 dark:border-white/10"></div>

    {{-- روابط سريعة --}}
    @if (Route::has('account.dashboard'))
      <a href="{{ route('account.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 text-gray-800 dark:text-gray-100" role="menuitem" data-ripple>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M3 12l9-8 9 8v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-8Z" stroke="currentColor" stroke-width="2"/></svg>
        لوحة حسابي
      </a>
    @endif

    @if ($isAdmin && Route::has('admin.dashboard'))
      <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg:white/10 text-gray-800 dark:text-gray-100" role="menuitem" data-ripple>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M3 13h8V3H3v10Zm10 8h8V3h-8v18ZM3 21h8v-6H3v6Z" stroke="currentColor" stroke-width="2"/></svg>
        لوحة الإدارة
      </a>
    @endif

    @if (Route::has('orders.index'))
      <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg:white/10 text-gray-800 dark:text-gray-100" role="menuitem" data-ripple>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M6 6l1.5 12A2 2 0 0 0 9.5 20h5a2 2 0 0 0 2-1.7L18 6M10 10v6M14 10v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        طلباتي
      </a>
    @endif

    <div class="border-t border-black/5 dark:border-white/10"></div>

    {{-- خروج --}}
    <form method="POST" action="{{ route('logout') }}" class="p-2" role="none">
      @csrf
      <button type="submit" class="w-full text-start flex items-center gap-2 px-3 py-2 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg:white/10 text-rose-600" role="menuitem" data-ripple data-loader>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M15 17l5-5-5-5M20 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h6" stroke="currentColor" stroke-width="2"/></svg>
        تسجيل الخروج
      </button>
    </form>
  </div>
</div>
