{{-- publishers/show.blade.php --}}
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $publisher->name }} - المتجر الإلكتروني للكتب</title>
  @include('partials.theme-init')
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 antialiased">
  <header class="sticky top-0 z-40 backdrop-blur bg-white/90 dark:bg-gray-900/80 border-b border-black/5 dark:border-white/10">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between gap-3">
      <a href="{{ route('home') }}" class="font-bold text-xl text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition">المتجر الإلكتروني للكتب</a>
      <div class="flex items-center gap-2">
        <form method="GET" class="flex gap-2">
          <input name="q" value="{{ request('q') }}" placeholder="ابحث داخل الناشر" class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 rounded-xl px-3 py-2 w-48 sm:w-64">
          <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition">بحث</button>
        </form>
        @include('partials.theme-toggle-button')
      </div>
    </div>
  </header>
  <main class="container mx-auto px-4 py-8">
    <h1 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">الناشر: {{ $publisher->name }}</h1>
    @if($books->count())
    {{-- شبكة البطاقات --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
      @foreach($books as $book)
      <x-book-card :book="$book" />
    @endforeach
    </div>
    <div class="mt-6">
      <!-- {{ $books->links() }} -->
      {{ $books->onEachSide(1)->links('vendor.pagination.tailwind-rtl') }}

    </div>
  @else
    <p class="text-gray-600 dark:text-gray-400">لا توجد كتب لهذا الناشر.</p>
  @endif
  </main>
</body>

</html>