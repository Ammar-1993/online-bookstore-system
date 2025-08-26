@extends('admin.layouts.app')
@section('title','الكتب')

@section('content')
@php
  $s    = $s    ?? request('s');
  $sort = $sort ?? request('sort', 'id');
  $dir  = $dir  ?? request('dir',  'desc');

  $sortLink = function (string $field, string $label) use ($sort, $dir) {
      $is   = $sort === $field;
      $next = $is && $dir === 'asc' ? 'desc' : 'asc';
      $url  = request()->fullUrlWithQuery(['sort' => $field, 'dir' => $next, 'page' => null]);
      $arrow = $is
        ? ($dir === 'asc'
            ? '<svg class="ms-1 w-3.5 h-3.5 inline" viewBox="0 0 24 24" fill="none"><path d="M8 15l4-4 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>'
            : '<svg class="ms-1 w-3.5 h-3.5 inline" viewBox="0 0 24 24" fill="none"><path d="M16 9l-4 4-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>')
        : '';
      return '<a href="'.$url.'" class="inline-flex items-center hover:underline">'.$label.$arrow.'</a>';
  };
@endphp

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
  <a href="{{ route('admin.books.create') }}"
     class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-indigo-600 text-white shadow hover:bg-indigo-700"
     data-ripple data-loader>
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    إضافة كتاب
  </a>

  <form method="GET" action="{{ route('admin.books.index') }}" class="w-full md:w-1/2">
    <div class="relative">
      <input name="s" value="{{ $s }}" placeholder="ابحث بالعنوان أو ISBN…"
             class="w-full h-12 rounded-2xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 ps-3 pe-12 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
      <button type="submit"
              class="absolute inset-y-0 right-2 my-auto inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
      @if($s)
        <a href="{{ route('admin.books.index', ['sort' => $sort, 'dir' => $dir]) }}"
           class="absolute inset-y-0 left-2 my-auto inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
           title="مسح البحث">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </a>
      @endif
    </div>
    {{-- الحفاظ على اتجاه/حقل الفرز عند البحث --}}
    <input type="hidden" name="sort" value="{{ $sort }}">
    <input type="hidden" name="dir"  value="{{ $dir }}">
  </form>
</div>

<div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10">
  <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
    <thead class="bg-gray-50 dark:bg-gray-800/60 sticky top-0 z-10">
      <tr class="text-right text-gray-700 dark:text-gray-200">
        <th class="px-3 py-2 w-16">الغلاف</th>
        <th class="px-3 py-2">{!! $sortLink('title', 'العنوان') !!}</th>
        <th class="px-3 py-2 text-nowrap">{!! $sortLink('price', 'السعر') !!}</th>
        <th class="px-3 py-2">{!! $sortLink('stock_qty', 'المخزون') !!}</th>
        <th class="px-3 py-2">{!! $sortLink('category', 'التصنيف') !!}</th>
        <th class="px-3 py-2">المؤلفون</th>
        <th class="px-3 py-2">{!! $sortLink('publisher', 'الناشر') !!}</th>
        <th class="px-3 py-2 text-center">إجراءات</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
    @forelse($books as $book)
      <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5">
        <td class="px-3 py-2">
          <div class="h-12 w-12 overflow-hidden rounded-xl ring-1 ring-black/5 dark:ring-white/10 bg-gray-100 dark:bg-gray-800">
            @if($book->cover_image_path)
              <img src="{{ asset('storage/'.$book->cover_image_path) }}" alt="غلاف {{ $book->title }}"
                   class="h-full w-full object-cover" loading="lazy" decoding="async"
                   onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden')">
              <div class="hidden h-full w-full grid place-items-center text-gray-400">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M4 5a2 2 0 0 1 2-2h8l6 6v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5Z" stroke="currentColor" stroke-width="2"/><path d="M14 3v4a2 2 0 0 0 2 2h4" stroke="currentColor" stroke-width="2"/></svg>
              </div>
            @else
              <div class="h-full w-full grid place-items-center text-gray-400">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M4 5a2 2 0 0 1 2-2h8l6 6v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5Z" stroke="currentColor" stroke-width="2"/><path d="M14 3v4a2 2 0 0 0 2 2h4" stroke="currentColor" stroke-width="2"/></svg>
              </div>
            @endif
          </div>
        </td>

        <td class="px-3 py-2">
          <div class="font-medium text-gray-900 dark:text-gray-100">{{ $book->title }}</div>
          <div class="text-gray-600 dark:text-gray-300 text-xs tabular-nums">ISBN: {{ $book->isbn }}</div>
        </td>

        <td class="px-3 py-2 tabular-nums">
          {{ $book->currency }} {{ number_format($book->price,2) }}
        </td>

        <td class="px-3 py-2 tabular-nums">
          {{ $book->stock_qty }}
        </td>

        <td class="px-3 py-2">
          {{ optional($book->category)->name ?? '—' }}
        </td>

        <td class="px-3 py-2">
          {{ $book->authors->pluck('name')->join(', ') ?: '—' }}
        </td>

        <td class="px-3 py-2">
          {{ optional($book->publisher)->name ?? '—' }}
        </td>

        <td class="px-3 py-2">
          <div class="flex items-center justify-center gap-2">
            @can('update', $book)
              <a href="{{ route('admin.books.edit', $book) }}"
                 class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-amber-500 text-white hover:bg-amber-600"
                 data-ripple data-loader>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M4 21h4l11-11a2.828 2.828 0 1 0-4-4L4 17v4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span class="hidden sm:inline">تعديل</span>
              </a>
            @endcan

            @can('delete', $book)
              <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline"
                    onsubmit="return confirm('حذف الكتاب نهائيًا؟');">
                @csrf @method('DELETE')
                <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-600 text-white hover:bg-rose-700" type="submit" data-ripple>
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M7 6v14a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V6M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                  <span class="hidden sm:inline">حذف</span>
                </button>
              </form>
            @endcan
          </div>
        </td>
      </tr>
    @empty
      <tr><td class="px-3 py-6 text-center text-gray-600 dark:text-gray-300" colspan="8">لا توجد بيانات</td></tr>
    @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $books->withQueryString()->links() }}</div>
@endsection
