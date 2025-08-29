{{-- resources/views/admin/categories/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'التصنيفات')

@section('content')
@php
  $q    = request('q');
  $sort = request('sort', 'id');     // id | name | books_count
  $dir  = request('dir',  'desc');   // asc | desc

  $sortLink = function(string $field, string $label) use ($sort, $dir) {
      $is = $sort === $field;
      $next = $is && $dir === 'asc' ? 'desc' : 'asc';
      $url = request()->fullUrlWithQuery(['sort' => $field, 'dir' => $next, 'page' => null]);
      $arrow = $is ? ($dir === 'asc'
          ? '<svg class="ms-1 w-3.5 h-3.5 inline" viewBox="0 0 24 24" fill="none"><path d="M8 15l4-4 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>'
          : '<svg class="ms-1 w-3.5 h-3.5 inline" viewBox="0 0 24 24" fill="none"><path d="M16 9l-4 4-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>'
      ) : '';
      return '<a href="'.$url.'" class="inline-flex items-center hover:underline">'.$label.$arrow.'</a>';
  };
@endphp

<div class="mb-6">
  <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
    <h1 class="text-2xl font-display font-semibold text-gray-900 dark:text-black-100">التصنيفات</h1>

    <div class="flex items-center gap-2 w-full lg:w-auto">
      {{-- بحث --}}
      <form method="GET" action="{{ route('admin.categories.index') }}" class="w-full lg:w-80">
        <div class="relative">
          <input name="q" value="{{ $q }}" dir="rtl" autocomplete="off"
                 placeholder="ابحث بالاسم أو الـ slug…"
                 class="w-full h-12 rounded-2xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 ps-3 pe-12 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
          <button type="submit" class="absolute inset-y-0 right-2 my-auto inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          </button>
          @if($q)
            <a href="{{ route('admin.categories.index', ['sort'=>$sort,'dir'=>$dir]) }}"
               class="absolute inset-y-0 left-2 my-auto inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
               title="مسح البحث">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </a>
          @endif
        </div>
        {{-- الحفاظ على الفرز أثناء البحث --}}
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="dir"  value="{{ $dir }}">
      </form>

      <a href="{{ route('admin.categories.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-indigo-600 text-white shadow hover:bg-indigo-700"
         data-ripple data-loader>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        إضافة تصنيف
      </a>
    </div>
  </div>
</div>

<div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10">
  <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
    <thead class="bg-gray-50 dark:bg-gray-800/60 sticky top-0 z-10">
      <tr class="text-right text-gray-700 dark:text-gray-200">
        <th class="px-4 py-3 font-medium">{!! $sortLink('id', '#') !!}</th>
        <th class="px-4 py-3 font-medium">{!! $sortLink('name', 'الاسم') !!}</th>
        <th class="px-4 py-3 font-medium">Slug</th>
        <th class="px-4 py-3 font-medium">{!! $sortLink('books_count', 'الكتب') !!}</th>
        <th class="px-4 py-3 font-medium">إجراءات</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
      @forelse($categories as $cat)
        <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5">
          <td class="px-4 py-3 tabular-nums">{{ $cat->id }}</td>
          <td class="px-4 py-3">
            <a href="{{ route('admin.categories.edit', $cat) }}" class="font-medium text-gray-900 dark:text-gray-100 hover:underline decoration-indigo-500/50 underline-offset-4">
              {{ $cat->name }}
            </a>
            @if($cat->description)
              <div class="text-xs text-gray-600 dark:text-gray-300 line-clamp-1">{{ $cat->description }}</div>
            @endif
          </td>
          <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
            <span dir="ltr" class="break-all">{{ $cat->slug }}</span>
          </td>
          <td class="px-4 py-3 tabular-nums">
            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs ring-1 ring-inset
              {{ $cat->books_count > 0
                  ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300'
                  : 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-700/50 dark:text-gray-300' }}">
              {{ $cat->books_count }}
            </span>
          </td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-1">
              <a href="{{ route('admin.categories.edit', $cat) }}"
                 class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"
                 title="تعديل" data-ripple data-loader>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M4 21h4l11-11a2.828 2.828 0 1 0-4-4L4 17v4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span class="hidden sm:inline">تعديل</span>
              </a>

              <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="inline"
                    onsubmit="return confirm('هل أنت متأكد من الحذف؟ هذا الإجراء نهائي.');">
                @csrf @method('DELETE')
                <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-600 text-white hover:bg-rose-700" type="submit">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M7 6v14a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V6M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                  <span class="hidden sm:inline">حذف</span>
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="px-4 py-8 text-center text-gray-600 dark:text-gray-300">لا توجد تصنيفات.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-6">
  {{ $categories->withQueryString()->links() }}
</div>
@endsection
