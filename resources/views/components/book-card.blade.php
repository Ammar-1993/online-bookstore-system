
{{-- resources/views/components/book-card.blade.php --}}
@props(['book'])

@php
    $inStock   = (int)($book->stock_qty ?? 0) > 0;
    $currency  = $book->currency ?: config('app.currency', 'USD');
    $price     = (float) ($book->price ?? 0);
    $sale      = $book->sale_price ?? null;
    $hasSale   = is_numeric($sale) && (float)$sale > 0 && (float)$sale < $price;
    $salePrice = $hasSale ? (float)$sale : null;
    $discount  = $hasSale ? max(1, round((1 - ($salePrice / max($price, 0.01))) * 100)) : null;

    $authors = trim(
        (string) ($book->authors->pluck('name')->filter()->take(2)->join('، ') ?: ($book->author_main ?? ''))
    );
@endphp

<div class="group bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 overflow-hidden transition
            hover:-translate-y-0.5 hover:shadow-lg">
  <a href="{{ route('books.show', $book) }}" class="block relative">
    <div class="relative h-64 md:h-72 overflow-hidden">
      <img
        src="{{ $book->cover_image_path ? asset('storage/'.$book->cover_image_path) : 'https://placehold.co/600x800?text=No+Cover' }}"
        alt="غلاف: {{ $book->title }}"
        loading="lazy" decoding="async"
        class="w-full h-full object-cover transition duration-300 group-hover:scale-[1.02]">

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
      <div class="font-semibold text-gray-900 dark:text-gray-100 line-clamp-2">{{ $book->title }}</div>
      @if($authors)
        <div class="mt-1 text-sm text-gray-600 dark:text-gray-300 line-clamp-1">{{ $authors }}</div>
      @endif>
    </div>
  </a>

  <div class="px-3 pb-3">
    <div class="mt-1 flex items-center justify-between">
      <div class="tabular-nums">
        @if($hasSale)
          <div class="flex items-baseline gap-2">
            <span class="font-bold text-emerald-700 dark:text-emerald-300">
              {{ number_format($salePrice, 2) }} {{ $currency }}
            </span>
            <span class="text-xs line-through text-gray-400 dark:text-gray-500">
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
        <span class="hidden sm:inline-flex items-center gap-1 text-[11px] text-emerald-700 dark:text-emerald-300">
          <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500"></span> متاح
        </span>
      @endif
    </div>

    <form method="POST" action="{{ route('cart.add', $book) }}" class="mt-3">
      @csrf
      <input type="hidden" name="qty" value="1">
      <button type="submit" aria-label="أضف {{ $book->title }} إلى السلة"
              class="w-full px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed"
              @if(! $inStock) disabled aria-disabled="true" title="غير متاح حالياً" @endif
              data-ripple data-loader>
        أضف للسلة
      </button>
    </form>
  </div>
</div>
