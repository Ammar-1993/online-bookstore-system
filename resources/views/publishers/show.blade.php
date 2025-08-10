{{-- publishers/show.blade.php --}}
<!DOCTYPE html><html lang="ar" dir="rtl"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $publisher->name }} - متجر الكتب</title>
@vite(['resources/css/app.css','resources/js/app.js'])
</head><body class="bg-gray-50 text-gray-900">
<header class="bg-white border-b"><div class="container mx-auto px-4 py-3 flex items-center justify-between">
  <a href="{{ route('home') }}" class="font-bold text-xl">متجر الكتب</a>
  <form method="GET" class="flex gap-2">
    <input name="q" value="{{ request('q') }}" placeholder="ابحث داخل الناشر" class="border rounded px-3 py-2 w-64">
    <button class="px-4 py-2 bg-indigo-600 text-white rounded">بحث</button>
  </form>
</div></header>
<main class="container mx-auto px-4 py-8">
  <h1 class="text-xl font-semibold mb-4">الناشر: {{ $publisher->name }}</h1>
  @if($books->count())
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
      @foreach($books as $book) <x-book-card :book="$book" /> @endforeach
    </div>
    <div class="mt-6">{{ $books->links() }}</div>
  @else
    <p class="text-gray-600">لا توجد كتب لهذا الناشر.</p>
  @endif
</main></body></html>
