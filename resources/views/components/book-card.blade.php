@props(['book'])

@php
    $inStock  = (int)($book->stock_qty ?? 0) > 0;
    $currency = $book->currency ?: config('app.currency', 'USD');
@endphp

<div class="bg-white rounded-2xl shadow p-3 hover:shadow-md transition">
  <a href="{{ route('books.show', $book) }}" class="block">
    <img
      src="{{ $book->cover_image_path ? asset('storage/'.$book->cover_image_path) : 'https://placehold.co/300x420' }}"
      alt="{{ $book->title }}"
      class="w-full h-64 md:h-72 object-cover rounded-xl">
    <div class="mt-2 font-semibold line-clamp-2">{{ $book->title }}</div>
  </a>

  <div class="text-sm text-gray-600">
    {{ $book->authors->pluck('name')->take(2)->join('، ') ?: $book->author_main }}
  </div>

  <div class="mt-2 flex items-center justify-between">
    <div class="font-bold">{{ number_format($book->price, 2) }} {{ $currency }}</div>
    @unless($inStock)
      <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">غير متاح</span>
    @endunless
  </div>

  <form method="POST" action="{{ route('cart.add', $book) }}" class="mt-3">
    @csrf
    <input type="hidden" name="qty" value="1">
    <button type="submit"
            class="w-full px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 disabled:opacity-60"
            @if(! $inStock) disabled @endif data-ripple data-loader>
      أضف للسلة
    </button>
  </form>
</div>
