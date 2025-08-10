@props(['book'])

<a href="{{ route('books.show', $book) }}" class="bg-white rounded shadow p-3 hover:shadow-md block">
  <img
    src="{{ $book->cover_image_path ? asset('storage/'.$book->cover_image_path) : 'https://placehold.co/300x420' }}"
    alt="{{ $book->title }}"
    class="w-full h-64 md:h-72 object-cover rounded">
  <div class="mt-2 font-semibold line-clamp-2">{{ $book->title }}</div>
  <div class="text-sm text-gray-600">
    {{ $book->authors->pluck('name')->take(2)->join('، ') ?: $book->author_main }}
  </div>
  <div class="mt-1 font-bold">{{ number_format($book->price,2) }} {{ $book->currency }}</div>
</a>
