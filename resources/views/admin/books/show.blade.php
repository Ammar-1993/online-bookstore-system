@extends('admin.layouts.app')
@section('title', 'عرض كتاب')

@section('content')
<a href="{{ route('admin.books.index') }}" class="px-3 py-2 rounded bg-gray-200">← رجوع</a>

<div class="mt-4 grid md:grid-cols-3 gap-6">
  <div>
    @if($book->cover_image_path)
      <img src="{{ asset('storage/'.$book->cover_image_path) }}" class="rounded shadow">
    @endif
  </div>
  <div class="md:col-span-2 space-y-2">
    <h1 class="text-xl font-bold">{{ $book->title }}</h1>
    <div class="text-gray-600">ISBN: {{ $book->isbn }}</div>
    <div>{{ $book->currency }} {{ number_format($book->price,2) }}</div>
    <div>المخزون: {{ $book->stock_qty }}</div>
    <div>التصنيف: {{ optional($book->category)->name }}</div>
    <div>الناشر: {{ optional($book->publisher)->name }}</div>
    <div>المؤلفون: {{ $book->authors->pluck('name')->join(', ') }}</div>
    <div class="text-gray-700 leading-7 whitespace-pre-line">{{ $book->description }}</div>

    <div class="pt-4">
      @can('update',$book)
        <a class="px-3 py-2 bg-amber-500 text-white rounded" href="{{ route('admin.books.edit',$book) }}">تعديل</a>
      @endcan
      @can('delete',$book)
        <form action="{{ route('admin.books.destroy',$book) }}" method="POST" class="inline"
              onsubmit="return confirm('حذف الكتاب؟');">@csrf @method('DELETE')
          <button class="px-3 py-2 bg-rose-600 text-white rounded">حذف</button>
        </form>
      @endcan
    </div>
  </div>
</div>
@endsection
