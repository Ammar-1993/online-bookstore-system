@extends('layouts.app')

@section('title', 'المفضّلة')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
  <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">المفضّلة</h1>

  @if ($books->count())
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      @foreach($books as $book)
        <x-book-card :book="$book" :wishlist-ids="$wishlistIds ?? []" />
      @endforeach
    </div>

    <div class="mt-4">
      {{ $books->onEachSide(1)->links('vendor.pagination.tailwind-rtl') }}
    </div>
  @else
    <div class="p-6 text-center text-slate-600 dark:text-slate-300">
      لا توجد عناصر ضمن المفضّلة بعد.
    </div>
  @endif
</div>
@endsection
