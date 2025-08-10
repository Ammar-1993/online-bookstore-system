<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $book->title }} - متجر الكتب</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
<header class="bg-white border-b">
  <div class="container mx-auto px-4 py-3 flex items-center justify-between">
    <a href="{{ route('home') }}" class="font-bold text-xl">متجر الكتب</a>
    <nav class="flex gap-3">
      <a class="hover:text-indigo-600" href="{{ route('home') }}">الرئيسية</a>
    </nav>
  </div>
</header>

<main class="container mx-auto px-4 py-8">
  <div class="grid md:grid-cols-2 gap-8">
    <div>
      <img src="{{ $book->cover_image_path ? asset('storage/'.$book->cover_image_path) : 'https://placehold.co/600x800' }}"
           alt="{{ $book->title }}" class="w-full rounded shadow">
    </div>
    <div>
      <h1 class="text-2xl font-bold mb-2">{{ $book->title }}</h1>

      <div class="text-gray-700 mb-3">
        المؤلف:
        @if($book->authors->count())
          @foreach($book->authors as $idx => $a)
            <a class="text-indigo-600 hover:underline" href="{{ route('authors.show', $a) }}">{{ $a->name }}</a>@if(!$loop->last) ، @endif
          @endforeach
        @else
          {{ $book->author_main }}
        @endif
      </div>

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

      <div class="text-xl font-bold mb-2">{{ number_format($book->price,2) }} {{ $book->currency }}</div>
      <div class="text-sm text-gray-600 mb-6">
        المخزون: {{ $book->stock_qty }} • الحالة: {{ $book->status === 'published' ? 'متاح' : 'مسودة' }}
      </div>

      <p class="leading-7 text-gray-800 whitespace-pre-line">{{ $book->description }}</p>

      <div class="mt-6">
        <button class="px-4 py-2 rounded bg-gray-300 text-gray-700 cursor-not-allowed" title="قريبًا">أضف إلى العربة</button>
      </div>
    </div>
  </div>

  @if($related->count())
    <h2 class="mt-12 mb-4 text-lg font-semibold">قد يعجبك أيضًا</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
      @foreach($related as $r)
        <x-book-card :book="$r" />
      @endforeach
    </div>
  @endif
</main>
</body>
</html>
