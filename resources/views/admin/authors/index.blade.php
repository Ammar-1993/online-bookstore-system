@extends('admin.layouts.app')
@section('title','المؤلفون')

@section('content')
@php
  $q    = $q    ?? request('q');
  $sort = $sort ?? request('sort', 'id');
  $dir  = $dir  ?? request('dir',  'desc');

  // مولّد رابط الفرز مع تبديل الاتجاه
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
  <h1 class="text-2xl font-display font-semibold text-gray-900 dark:text-black-100">المؤلفون</h1>

  <a href="{{ route('admin.authors.create') }}"
     class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-indigo-600 text-white shadow hover:bg-indigo-700"
     data-ripple data-loader>
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    إضافة مؤلف
  </a>
</div>

{{-- بحث يحافظ على sort/dir --}}
<form method="GET" action="{{ route('admin.authors.index') }}" class="mb-4">
  <div class="relative max-w-xl">
    <input type="text" name="q" value="{{ $q }}" dir="rtl" autocomplete="off"
           placeholder="ابحث بالاسم أو الـ slug…"
           class="w-full h-12 rounded-2xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 ps-3 pe-12 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    <button type="submit"
            class="absolute inset-y-0 right-2 my-auto inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    </button>

    @if($q)
      <a href="{{ route('admin.authors.index', ['sort' => $sort, 'dir' => $dir]) }}"
         class="absolute inset-y-0 left-2 my-auto inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
         title="مسح البحث">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      </a>
    @endif
  </div>

  <input type="hidden" name="sort" value="{{ $sort }}">
  <input type="hidden" name="dir"  value="{{ $dir }}">
</form>

<div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10">
  <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
    <thead class="bg-gray-50 dark:bg-gray-800/60 sticky top-0 z-10">
      <tr class="text-right text-gray-700 dark:text-gray-200">
        <th class="px-3 py-3 w-12 font-medium">{!! $sortLink('id', '#') !!}</th>
        <th class="px-3 py-3 font-medium">{!! $sortLink('name', 'الاسم') !!}</th>
        <th class="px-3 py-3 font-medium">{!! $sortLink('slug', 'Slug') !!}</th>
        <th class="px-3 py-3 font-medium text-center">{!! $sortLink('books_count', 'الكتب') !!}</th>
        <th class="px-3 py-3 font-medium text-center">إجراءات</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
      @forelse($authors as $author)
        <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5">
          <td class="px-3 py-2 tabular-nums text-gray-700 dark:text-gray-300">{{ $author->id }}</td>

          <td class="px-3 py-2 font-medium">
            <div class="flex items-center gap-2 min-w-0">
              @if($author->avatar_path)
                <img src="{{ asset('storage/'.$author->avatar_path) }}" class="h-8 w-8 rounded-full ring-1 ring-black/5 dark:ring-white/10 object-cover" alt="صورة {{ $author->name }}">
              @else
                <div class="h-8 w-8 rounded-full ring-1 ring-black/5 dark:ring-white/10 bg-gray-100 dark:bg-gray-800 grid place-items-center text-gray-400">✍️</div>
              @endif
              <a href="{{ route('admin.authors.edit',$author) }}"
                 class="hover:underline decoration-indigo-500/50 underline-offset-4 text-gray-900 dark:text-gray-100 truncate">
                {{ $author->name }}
              </a>
            </div>
          </td>

          <td class="px-3 py-2 text-gray-700 dark:text-gray-300">
            <span dir="ltr" class="break-all">{{ $author->slug }}</span>
          </td>

          <td class="px-3 py-2 text-center">
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs ring-1 ring-inset
              {{ ($author->books_count ?? 0) > 0
                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300'
                    : 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-700/50 dark:text-gray-300' }}">
              {{ $author->books_count ?? 0 }}
            </span>
          </td>

          <td class="px-3 py-2">
            <div class="flex items-center justify-center gap-2">
              <a href="{{ route('admin.authors.edit',$author) }}"
                 class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-amber-500 text-white hover:bg-amber-600"
                 data-ripple data-loader title="تعديل">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M4 21h4l11-11a2.828 2.828 0 1 0-4-4L4 17v4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span class="hidden sm:inline">تعديل</span>
              </a>

              <form method="POST" action="{{ route('admin.authors.destroy',$author) }}"
                    onsubmit="return confirm('تأكيد حذف «{{ $author->name }}»؟');" class="inline">
                @csrf @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-600 text-white hover:bg-rose-700"
                        data-ripple title="حذف">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M7 6v14a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V6M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                  <span class="hidden sm:inline">حذف</span>
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="px-3 py-8 text-center text-gray-600 dark:text-gray-300">لا توجد نتائج مطابقة.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-6">
  {{ $authors->withQueryString()->links() }}
</div>
@endsection
