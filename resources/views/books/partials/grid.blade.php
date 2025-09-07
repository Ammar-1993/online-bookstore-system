{{-- جزء النتائج --}}
@if ($books->count())
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    @foreach($books as $book)
      <x-book-card :book="$book" />
    @endforeach
  </div>

  <div class="mt-4">
    {{ $books->onEachSide(1)->links('vendor.pagination.tailwind-rtl') }}
  </div>
@else
  <div class="p-6 text-center text-slate-600 dark:text-slate-300">
    لا توجد نتائج مطابقة لبحثك حالياً.
  </div>
@endif
