@extends('admin.layouts.app')

@section('title', 'المراجعات')

@section('content')
@php
  $q      = $q      ?? request('q');
  $filter = $filter ?? request('status');
  $sort   = $sort   ?? request('sort', 'created_at');
  $dir    = $dir    ?? request('dir',  'desc');

  // مولّد رابط الفرز مع تبديل الاتجاه والحفاظ على الاستعلام
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
  <h1 class="text-2xl font-display font-semibold text-gray-900 dark:text-gray-100">المراجعات</h1>

  {{-- بحث + فلتر الحالة (يحافظان على sort/dir) --}}
  <form method="GET" class="flex flex-col sm:flex-row gap-2 sm:items-center">
    <div class="relative w-full sm:w-72">
      <input type="text" name="q" value="{{ $q }}" class="w-full h-12 rounded-2xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 ps-3 pe-12 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="ابحث بالكتاب أو المستخدم…">
      <button class="absolute inset-y-0 right-2 my-auto inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" type="submit">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
      @if($q)
        <a href="{{ route('admin.reviews.index', ['status'=>$filter, 'sort'=>$sort, 'dir'=>$dir]) }}"
           class="absolute inset-y-0 left-2 my-auto inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
           title="مسح البحث">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </a>
      @endif
    </div>

    <select name="status" class="h-12 rounded-2xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
      <option value="">الكل</option>
      <option value="approved" @selected($filter==='approved')>المقبولة</option>
      <option value="pending"  @selected($filter==='pending')>قيد المراجعة</option>
    </select>

    {{-- الحفاظ على اتجاه/حقل الفرز --}}
    <input type="hidden" name="sort" value="{{ $sort }}">
    <input type="hidden" name="dir"  value="{{ $dir }}">

    <button class="px-4 py-2 rounded-2xl bg-indigo-600 text-white hover:bg-indigo-700" data-ripple data-loader>بحث</button>
  </form>
</div>

<div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10">
  <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
    <thead class="bg-gray-50 dark:bg-gray-800/60 sticky top-0 z-10">
      <tr class="text-right text-gray-700 dark:text-gray-200">
        <th class="px-3 py-3 font-medium">{!! $sortLink('user', 'المستخدم') !!}</th>
        <th class="px-3 py-3 font-medium">{!! $sortLink('book', 'الكتاب') !!}</th>
        <th class="px-3 py-3 font-medium text-center">{!! $sortLink('rating', 'التقييم') !!}</th>
        <th class="px-3 py-3 font-medium text-center">{!! $sortLink('approved', 'الحالة') !!}</th>
        <th class="px-3 py-3 font-medium text-right">{!! $sortLink('created_at', 'أُضيف') !!}</th>
        <th class="px-3 py-3 font-medium text-right">إجراءات</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
      @forelse($reviews as $r)
        <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5">
          {{-- المستخدم --}}
          <td class="px-3 py-2">
            <div class="flex items-center gap-2 min-w-0">
              <div class="h-8 w-8 rounded-full ring-1 ring-black/5 dark:ring-white/10 bg-gray-100 dark:bg-gray-800 grid place-items-center text-gray-500 text-xs shrink-0">
                {{ mb_substr($r->user->name ?? '—', 0, 1) }}
              </div>
              <div class="min-w-0">
                <div class="font-medium truncate">{{ $r->user->name ?? '—' }}</div>
                <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate" dir="ltr">{{ $r->user->email ?? '' }}</div>
              </div>
            </div>
          </td>

          {{-- الكتاب --}}
          <td class="px-3 py-2">
            <a class="text-indigo-600 dark:text-indigo-400 hover:underline" href="{{ route('books.show', $r->book->slug) }}" target="_blank" rel="noopener">
              {{ $r->book->title }}
            </a>
          </td>

          {{-- التقييم --}}
          <td class="px-3 py-2 text-center">
            <div class="inline-flex items-center gap-1" title="{{ $r->rating }}/5">
              @for($i=1;$i<=5;$i++)
                <svg class="w-4 h-4 {{ $i <= $r->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.036a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.036a1 1 0 00-1.175 0L6.66 16.283c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L3.025 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.024-3.293z"/>
                </svg>
              @endfor
              <span class="ms-1 text-xs tabular-nums text-gray-600 dark:text-gray-400">({{ $r->rating }}/5)</span>
            </div>
          </td>

          {{-- الحالة --}}
          <td class="px-3 py-2 text-center">
            @if($r->approved)
              <span class="px-2 py-1 text-xs rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300">مقبولة</span>
            @else
              <span class="px-2 py-1 text-xs rounded-full bg-amber-50 text-amber-700 ring-1 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-300">قيد المراجعة</span>
            @endif
          </td>

          {{-- أضيف --}}
          <td class="px-3 py-2 text-right text-gray-700 dark:text-gray-300 whitespace-nowrap" title="{{ $r->created_at->format('Y-m-d H:i') }}">
            {{ $r->created_at->diffForHumans() }}
          </td>

          {{-- إجراءات --}}
          <td class="px-3 py-2">
            <div class="flex items-center justify-end gap-2">
              <form method="POST" action="{{ route('admin.reviews.toggle', $r) }}">
                @csrf @method('PATCH')
                <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700" data-ripple>
                  {{ $r->approved ? 'تعطيل' : 'اعتماد' }}
                </button>
              </form>
              <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" onsubmit="return confirm('تأكيد الحذف؟');">
                @csrf @method('DELETE')
                <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-600 text-white hover:bg-rose-700" data-ripple>
                  حذف
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="px-3 py-8 text-center text-gray-600 dark:text-gray-300">لا توجد مراجعات.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $reviews->withQueryString()->links() }}
</div>
@endsection
