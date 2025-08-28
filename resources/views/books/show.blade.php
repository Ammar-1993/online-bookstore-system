<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $book->title }} - المتجر الإلكتروني للكتب</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900">
  {{-- Header --}}
  <header class="bg-white border-b">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
      <a href="{{ route('home') }}" class="font-bold text-xl">المتجر الإلكتروني للكتب</a>
      <nav class="flex gap-3 text-sm">
        <a class="hover:text-indigo-600" href="{{ route('home') }}">الرئيسية</a>
      </nav>
    </div>
  </header>

  {{-- Flash messages --}}
  <x-flash-stack duration="10000" />

  <main class="container mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-5">
      <a href="{{ route('home') }}" class="hover:text-indigo-600">الرئيسية</a>
      <span class="mx-2">/</span>
      <span class="text-gray-700">{{ $book->title }}</span>
    </nav>

    @php
    $inStock = (int) ($book->stock_qty ?? 0) > 0;
    $currency = $book->currency ?: config('app.currency', 'USD');
    $avg = isset($avgRating) ? (float) $avgRating : 0.0;
    $avgRounded = (int) round($avg);
    $count = (int) ($ratingsCount ?? 0);
    @endphp

    {{-- Book card (detail) --}}
    <div class="grid md:grid-cols-2 gap-8">
      {{-- Cover --}}
      <div>
        <img
          src="{{ $book->cover_image_path ? asset('storage/' . $book->cover_image_path) : 'https://placehold.co/600x800' }}"
          alt="{{ $book->title }}" class="w-full rounded-xl shadow bg-white object-cover">
      </div>

      {{-- Details --}}
      <div>
        <div class="flex items-center gap-3 mb-2">
          <h1 class="text-2xl font-bold">{{ $book->title }}</h1>

          @if($count > 0)
        <div class="flex items-center gap-1">
        <div class="flex">
          @for($i = 1; $i <= 5; $i++)
        <svg class="w-5 h-5 {{ $i <= $avgRounded ? 'text-yellow-400' : 'text-gray-300' }}" viewBox="0 0 20 20"
        fill="currentColor" aria-hidden="true">
        <path
        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.036a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.036a1 1 0 00-1.175 0L6.66 16.283c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L3.025 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.024-3.293z" />
        </svg>
      @endfor
        </div>
        <span class="text-xs text-gray-600">({{ number_format($avg, 1) }}) • {{ $count }} مراجعة</span>
        </div>
      @endif
        </div>

        {{-- Authors --}}
        <div class="text-gray-700 mb-3">
          المؤلف:
          @if($book->authors->count())
          @foreach($book->authors as $a)
        <a class="text-indigo-600 hover:underline"
        href="{{ route('authors.show', $a) }}">{{ $a->name }}</a>@if(!$loop->last) ، @endif
        @endforeach
      @else
        {{ $book->author_main }}
      @endif
        </div>

        {{-- Chips: category / publisher --}}
        <div class="flex flex-wrap gap-2 mb-4">
          @if($book->category)
        <a class="px-2 py-1 bg-gray-100 rounded hover:bg-gray-200"
        href="{{ route('categories.show', $book->category) }}">#{{ $book->category->name }}</a>
      @endif
          @if($book->publisher)
        <a class="px-2 py-1 bg-gray-100 rounded hover:bg-gray-200"
        href="{{ route('publishers.show', $book->publisher) }}">#{{ $book->publisher->name }}</a>
      @endif
        </div>

        {{-- Price + stock --}}
        <div class="text-xl font-bold mb-2">{{ number_format((float) $book->price, 2) }} {{ $currency }}</div>
        <div class="text-sm text-gray-600 mb-6">
          المخزون: {{ (int) $book->stock_qty }}
          • الحالة: {{ $book->status === 'published' ? 'متاح' : 'مسودة' }}
          @unless($inStock)
        <span class="ml-2 inline-flex items-center px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">غير
        متاح</span>
      @endunless
        </div>

        {{-- Description --}}
        @if($book->description)
      <p class="leading-7 text-gray-800 whitespace-pre-line">{{ $book->description }}</p>
    @endif

        {{-- Add to cart --}}
        <div class="mt-6">
          <form method="POST" action="{{ route('cart.add', $book) }}" class="flex items-center gap-3">
            @csrf
            <label for="qty" class="text-sm text-gray-700">الكمية</label>
            <input id="qty" type="number" name="qty" value="1" min="1" max="{{ (int) $book->stock_qty }}"
              class="w-20 border rounded-xl px-2 py-1 text-center" @unless($inStock) disabled @endunless>
            <button type="submit"
              class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-60"
              @unless($inStock) disabled @endunless>
              أضف إلى العربة
            </button>
          </form>
          @error('qty')
        <div class="mt-2 text-sm text-rose-600">{{ $message }}</div>
      @enderror
        </div>
      </div>
    </div>

    {{-- Reviews --}}
    <div class="mt-10">
      @include('books.partials.reviews', [
      'book' => $book,
      'reviews' => $reviews,
      'avgRating' => $avgRating,
      'ratingsCount' => $ratingsCount,
  ])
    </div>
  
    {{-- Related books using the single reusable card --}}
@if(isset($related) && $related->count())
     <h2 class="mt-12 mb-4 text-lg font-semibold">قد يعجبك أيضًا</h2>
      {{-- شبكة البطاقات --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
      @foreach($related as $r)
       <x-book-card :book="$r" />
      @endforeach
    </div>
@endif
  </main>
</body>
</html>
