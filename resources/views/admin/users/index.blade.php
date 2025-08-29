@extends('admin.layouts.app')

@section('title','المستخدمون')

@section('content')
@php
  $q    = $q    ?? request('q');
  $role = $role ?? request('role');
  $sort = $sort ?? request('sort', 'id');
  $dir  = $dir  ?? request('dir',  'desc');

  // مولّد رابط الفرز مع تبديل الاتجاه والحفاظ على الاستعلامات
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
  <h1 class="text-2xl font-display font-semibold text-gray-900 dark:text-black-100">المستخدمون</h1>
  <a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">رجوع للوحة</a>
</div>

{{-- البحث + فلترة الدور (تحافظ على sort/dir) --}}
<form method="get" action="{{ route('admin.users.index') }}" class="mb-4 flex flex-col sm:flex-row gap-2 sm:items-center">
  <div class="relative w-full sm:max-w-xs">
    <input type="text" name="q" value="{{ $q }}" placeholder="ابحث بالاسم أو البريد…"
           class="w-full h-12 rounded-2xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 ps-3 pe-12 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    <button type="submit"
            class="absolute inset-y-0 right-2 my-auto inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    </button>
    @if($q)
      <a href="{{ route('admin.users.index', ['role' => $role, 'sort' => $sort, 'dir' => $dir]) }}"
         class="absolute inset-y-0 left-2 my-auto inline-flex items-center justify-center w-9 h-9 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
         title="مسح البحث">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
      </a>
    @endif
  </div>

  <select name="role" class="h-12 rounded-2xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 px-3 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
    <option value="">كل الأدوار</option>
    @foreach($roles as $r)
      <option value="{{ $r }}" @selected($role===$r)>{{ $r }}</option>
    @endforeach
  </select>

  {{-- الحفاظ على اتجاه/حقل الفرز --}}
  <input type="hidden" name="sort" value="{{ $sort }}">
  <input type="hidden" name="dir"  value="{{ $dir }}">

  <button class="px-4 py-2 rounded-2xl bg-indigo-600 text-white hover:bg-indigo-700" data-ripple data-loader>بحث</button>
</form>

<div class="bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 overflow-x-auto">
  <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
    <thead class="bg-gray-50 dark:bg-gray-800/60 sticky top-0 z-10">
      <tr class="text-right text-gray-700 dark:text-gray-200">
        <th class="px-4 py-3 w-16 font-medium">{!! $sortLink('id', '#') !!}</th>
        <th class="px-4 py-3 font-medium">{!! $sortLink('name', 'الاسم') !!}</th>
        <th class="px-4 py-3 font-medium">{!! $sortLink('email', 'البريد') !!}</th>
        <th class="px-4 py-3 font-medium">الأدوار</th>
        <th class="px-4 py-3 font-medium text-center">{!! $sortLink('books_count', 'كتب البائع') !!}</th>
        <th class="px-4 py-3 font-medium text-right">{!! $sortLink('created_at', 'أُضيف') !!}</th>
        <th class="px-4 py-3 font-medium text-right">إجراءات</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
    @forelse($users as $u)
      <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5">
        <td class="px-4 py-2 tabular-nums text-gray-700 dark:text-gray-300">{{ $u->id }}</td>

        <td class="px-4 py-2">
          <div class="flex items-center gap-2 min-w-0">
            <div class="h-8 w-8 rounded-full ring-1 ring-black/5 dark:ring-white/10 bg-gray-100 dark:bg-gray-800 grid place-items-center text-gray-500 text-xs shrink-0">
              {{ mb_substr($u->name, 0, 1) }}
            </div>
            <span class="font-medium text-gray-900 dark:text-gray-100 truncate" title="{{ $u->name }}">{{ $u->name }}</span>
          </div>
        </td>

        <td class="px-4 py-2">
          <span dir="ltr" class="break-all text-gray-800 dark:text-gray-200">{{ $u->email }}</span>
        </td>

        <td class="px-4 py-2">
          @php $rnames = $u->roles->pluck('name'); @endphp
          @if($rnames->isEmpty())
            <span class="text-gray-500">—</span>
          @else
            <div class="flex flex-wrap gap-1">
              @foreach($rnames as $r)
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs ring-1 ring-inset
                             {{ $r === 'Admin'
                                  ? 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-300'
                                  : 'bg-slate-50 text-slate-700 ring-slate-600/20 dark:bg-slate-500/10 dark:text-slate-300' }}">
                  {{ $r }}
                </span>
              @endforeach
            </div>
          @endif
        </td>

        <td class="px-4 py-2 text-center tabular-nums">
          <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs ring-1 ring-inset
                {{ ($u->books_count ?? 0) > 0
                      ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-300'
                      : 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-700/50 dark:text-gray-300' }}">
            {{ $u->books_count ?? 0 }}
          </span>
        </td>

        <td class="px-4 py-2 text-right text-gray-700 dark:text-gray-300 whitespace-nowrap" title="{{ $u->created_at->format('Y-m-d H:i') }}">
          {{ $u->created_at->diffForHumans() }}
        </td>

        <td class="px-4 py-2">
          <div class="flex items-center gap-2">
            <a class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"
               href="{{ route('admin.users.edit',$u) }}" data-ripple data-loader>
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M4 21h4l11-11a2.828 2.828 0 1 0-4-4L4 17v4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <span class="hidden sm:inline">تعديل</span>
            </a>
            <form method="post" action="{{ route('admin.users.destroy',$u) }}"
                  onsubmit="return confirm('تأكيد الحذف؟');" class="inline">
              @csrf @method('delete')
              <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-rose-600 text-white hover:bg-rose-700" type="submit" data-ripple>
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M7 6v14a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V6M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                <span class="hidden sm:inline">حذف</span>
              </button>
            </form>
          </div>
        </td>
      </tr>
    @empty
      <tr><td colspan="7" class="px-4 py-6 text-center text-gray-600 dark:text-gray-300">لا يوجد نتائج.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $users->withQueryString()->links() }}
</div>
@endsection
