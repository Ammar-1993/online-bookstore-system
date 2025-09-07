@extends('layouts.app')
@section('title', 'المقارنة')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">المقارنة</h1>
    <a href="{{ route('books.index') }}" class="text-sm text-indigo-600 hover:underline" data-ripple>العودة للتصفح</a>
  </div>

  @if($books->isEmpty())
    <div class="p-6 text-center text-slate-600 dark:text-slate-300">
      لا توجد عناصر ضمن المقارنة بعد. أضف كتبًا عبر زر <b>مقارنة</b> على بطاقات الكتب.
    </div>
  @else
    {{-- شبكة أعمدة بحد أقصى 4 --}}
    <div class="grid grid-cols-1 md:grid-cols-{{ min(4, $books->count()) }} gap-4">
      @foreach($books as $book)
      <div class="rounded-2xl bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10 p-3">
        <div class="flex items-start justify-between">
          <div class="font-semibold line-clamp-2">{{ $book->title }}</div>
          <form method="POST" action="{{ route('compare.destroy', $book) }}">
            @csrf @method('DELETE')
            <button class="text-rose-600 text-sm hover:underline">إزالة</button>
          </form>
        </div>

        <div class="mt-3 overflow-hidden rounded-xl" style="aspect-ratio:3/4">
          <img src="{{ $book->cover_image_path ? asset('storage/'.$book->cover_image_path) : 'https://placehold.co/600x800?text=No+Cover' }}"
               alt="غلاف: {{ $book->title }}" class="w-full h-full object-cover" loading="lazy" decoding="async">
        </div>

        {{-- جدول خصائص مختصر --}}
        <dl class="mt-3 space-y-1 text-sm text-slate-700 dark:text-slate-200">
          <div class="flex justify-between gap-2">
            <dt class="text-slate-500">السعر</dt>
            <dd class="tabular-nums">
              {{ number_format(($book->sale_price ?? $book->price), 2) }} {{ $book->currency ?? config('app.currency','USD') }}
            </dd>
          </div>
          <div class="flex justify-between gap-2">
            <dt class="text-slate-500">التصنيف</dt>
            <dd>{{ $book->category->name ?? '—' }}</dd>
          </div>
          <div class="flex justify-between gap-2">
            <dt class="text-slate-500">الناشر</dt>
            <dd>{{ $book->publisher->name ?? '—' }}</dd>
          </div>
          <div class="flex justify-between gap-2">
            <dt class="text-slate-500">المؤلف</dt>
            <dd>{{ $book->authors->pluck('name')->take(2)->join('، ') ?: ($book->author_main ?? '—') }}</dd>
          </div>
          <div class="flex justify-between gap-2">
            <dt class="text-slate-500">المخزون</dt>
            <dd>{{ (int)($book->stock_qty ?? 0) > 0 ? 'متاح' : 'غير متاح' }}</dd>
          </div>
          @if(isset($book->avg_rating))
            <div class="flex justify-between gap-2">
              <dt class="text-slate-500">التقييم</dt>
              <dd>{{ number_format((float)$book->avg_rating,1) }} / 5 ({{ (int)($book->ratings_count ?? 0) }})</dd>
            </div>
          @endif
        </dl>

        <a href="{{ route('books.show', $book) }}"
           class="mt-3 inline-flex items-center justify-center w-full px-3 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700"
           data-ripple data-loader>عرض التفاصيل</a>
      </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
