{{-- resources/views/components/book-card.blade.php --}}
@props([
  'book',
  'wishlistIds' => [],
  'compareIds'  => [],
])

@php
  $inStock   = (int) ($book->stock_qty ?? 0) > 0;
  $currency  = $book->currency ?: config('app.currency', 'USD');
  $price     = (float) ($book->price ?? 0);
  $sale      = $book->sale_price ?? null;
  $hasSale   = is_numeric($sale) && (float) $sale > 0 && (float) $sale < $price;
  $salePrice = $hasSale ? (float) $sale : null;
  $discount  = $hasSale ? max(1, round((1 - ($salePrice / max($price, 0.01))) * 100)) : null;

  $authors = trim(
    (string) ($book->authors->pluck('name')->filter()->take(2)->join('، ') ?: ($book->author_main ?? ''))
  );

  $coverUrl = $book->cover_image_path
    ? asset('storage/' . $book->cover_image_path)
    : 'https://placehold.co/600x800?text=No+Cover';

  $titleId = 'book-title-' . $book->getKey();

  // حالة المفضّلة: عبر IDs الممرّرة أو عبر pivot من علاقة wishlist
  $pivotHasWishlist = isset($book->pivot) && method_exists($book->pivot, 'getTable') && $book->pivot->getTable() === 'wishlists';
  $isFav = auth()->check() && (in_array($book->id, $wishlistIds, true) || $pivotHasWishlist);

  // حالة المقارنة: IDs ممرّرة أو pivot من علاقة compares أو من Session للضيف
  $pivotHasCompare = isset($book->pivot) && method_exists($book->pivot, 'getTable') && $book->pivot->getTable() === 'compares';
  $sessionCompare  = (array) session('compare.items', []);
  $isCompared = in_array($book->id, $compareIds, true) || $pivotHasCompare || in_array($book->id, $sessionCompare, true);
@endphp

<div class="group bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 overflow-hidden transition hover:-translate-y-0.5 hover:shadow-lg">
  <div class="relative">
    {{-- زر المفضّلة (للمستخدم المسجّل فقط) --}}
    @auth
      <button
        type="button"
        class="absolute top-2 left-2 rtl:left-auto rtl:right-2 z-10 wishlist-toggle rounded-full bg-white/90 dark:bg-black/50 backdrop-blur px-2.5 py-1.5 shadow
               text-sm leading-none border border-slate-200 dark:border-slate-600 hover:bg-white dark:hover:bg-black focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500"
        data-url="{{ route('wishlist.toggle', $book) }}"
        aria-pressed="{{ $isFav ? 'true' : 'false' }}"
        title="{{ $isFav ? 'إزالة من المفضّلة' : 'إضافة إلى المفضّلة' }}">
        <span class="inline-flex items-center gap-1">
          <svg viewBox="0 0 24 24" class="w-4 h-4 {{ $isFav ? 'fill-rose-600' : 'fill-transparent stroke-rose-600' }}" stroke-width="1.8" aria-hidden="true">
            <path d="M12 21s-6.716-3.686-9.192-7.394C.365 10.28 2.09 6.5 5.6 6.5c2.01 0 3.086 1.13 3.9 2.208C10.314 7.63 11.39 6.5 13.4 6.5c3.51 0 5.235 3.78 2.792 7.106C18.716 17.314 12 21 12 21Z"/>
          </svg>
          <span class="sr-only">{{ $isFav ? 'إزالة من المفضّلة' : 'إضافة إلى المفضّلة' }}</span>
        </span>
      </button>
    @endauth

    {{-- زر المقارنة (متاح للجميع؛ الضيف يُخزّن بالجلسة) --}}
    <button
      type="button"
      class="absolute top-10 left-2 rtl:left-auto rtl:right-2 z-10 compare-toggle rounded-full bg-white/90 dark:bg-black/50 backdrop-blur px-2.5 py-1.5 shadow
             text-sm leading-none border border-slate-200 dark:border-slate-600 hover:bg-white dark:hover:bg-black focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
      data-url="{{ route('compare.toggle', $book) }}"
      aria-pressed="{{ $isCompared ? 'true' : 'false' }}"
      title="{{ $isCompared ? 'إزالة من المقارنة' : 'إضافة إلى المقارنة' }}">
      <span class="inline-flex items-center gap-1">
        {{-- أيقونة الموازين --}}
        <svg viewBox="0 0 24 24" class="w-4 h-4 {{ $isCompared ? 'fill-indigo-600' : 'fill-transparent' }} stroke-indigo-600" stroke-width="1.8" aria-hidden="true">
          <path d="M12 3v18M3 7h18M7 7l3 5H4l3-5Zm10 0l3 5h-6l3-5Z"/>
        </svg>
        <span class="sr-only">{{ $isCompared ? 'إزالة من المقارنة' : 'إضافة إلى المقارنة' }}</span>
      </span>
    </button>

    <a href="{{ route('books.show', $book) }}"
       class="block relative focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900 rounded-2xl"
       aria-labelledby="{{ $titleId }}" aria-label="عرض تفاصيل: {{ $book->title }}">
      <div class="relative overflow-hidden" style="aspect-ratio: 3 / 4;">
        <img
          src="{{ $coverUrl }}"
          alt="غلاف: {{ $book->title }}"
          loading="lazy" decoding="async" fetchpriority="low"
          width="480" height="640"
          class="w-full h-full object-cover select-none transition duration-300 group-hover:scale-[1.02]">

        {{-- شارة الخصم/النفاد --}}
        @if($hasSale)
          <span class="absolute top-2 left-2 rtl:left-auto rtl:right-2 rounded-full bg-rose-600 text-white text-[11px] px-2 py-0.5 shadow">
            %{{ $discount }} خصم
          </span>
        @endif
        @unless($inStock)
          <span class="absolute top-2 right-2 rtl:right-auto rtl:left-2 rounded-full bg-gray-900/80 text-white text-[11px] px-2 py-0.5">
            غير متاح
          </span>
        @endunless

        {{-- تظليل خفيف عند التحويم --}}
        <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/20 via-black/0 to-transparent opacity-0 group-hover:opacity-100 transition"></span>
      </div>

      <div class="p-3">
        <div id="{{ $titleId }}" class="font-semibold text-gray-900 dark:text-gray-100 line-clamp-2">
          {{ $book->title }}
        </div>
        @if($authors)
          <div class="mt-1 text-sm text-gray-600 dark:text-gray-300 line-clamp-1">
            {{ $authors }}
          </div>
        @endif
      </div>
    </a>
  </div>

  <div class="px-3 pb-3">
    <div class="mt-1 flex items-center justify-between">
      <div class="tabular-nums" aria-label="السعر">
        @if($hasSale)
          <div class="flex items-baseline gap-2">
            <span class="font-bold text-emerald-700 dark:text-emerald-300">
              {{ number_format($salePrice, 2) }} {{ $currency }}
            </span>
            <span class="text-xs line-through text-gray-400 dark:text-gray-500" aria-label="السعر قبل الخصم">
              {{ number_format($price, 2) }} {{ $currency }}
            </span>
          </div>
        @else
          <span class="font-bold text-gray-900 dark:text-gray-100">
            {{ number_format($price, 2) }} {{ $currency }}
          </span>
        @endif
      </div>

      @if($inStock)
        <span class="hidden sm:inline-flex items-center gap-1 text-[11px] text-emerald-700 dark:text-emerald-300" aria-live="polite">
          <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span> متاح
        </span>
      @endif
    </div>

    <form method="POST" action="{{ route('cart.add', $book) }}" class="mt-3">
      @csrf
      <input type="hidden" name="qty" value="1">
      <button
        type="submit"
        aria-label="أضف {{ $book->title }} إلى السلة"
        class="w-full px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900 transition"
        @if(!$inStock) disabled aria-disabled="true" title="غير متاح حالياً" @endif
        data-ripple data-loader>
        أضف للسلة
      </button>
    </form>
  </div>
</div>
