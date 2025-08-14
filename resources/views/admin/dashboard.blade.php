@extends('admin.layouts.app')
@section('title','لوحة التحكم')

@section('content')
<div class="space-y-6">

  {{-- رأس صفحة أنيق + إجراءات سريعة --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">أهلًا بك في لوحة التحكم</h1>
      <p class="text-sm text-gray-500 mt-1">نظرة عامة سريعة على متجرك.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.books.create') }}"
         class="inline-flex items-center rounded-xl px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 shadow">
        + إضافة كتاب
      </a>
      @role('Admin')
        <a href="{{ route('admin.categories.create') }}" class="rounded-xl px-3 py-2 bg-gray-100 hover:bg-gray-200">+ تصنيف</a>
        <a href="{{ route('admin.authors.create') }}" class="rounded-xl px-3 py-2 bg-gray-100 hover:bg-gray-200">+ مؤلف</a>
        <a href="{{ route('admin.publishers.create') }}" class="rounded-xl px-3 py-2 bg-gray-100 hover:bg-gray-200">+ ناشر</a>
      @endrole
    </div>
  </div>

  {{-- شبكة بطاقات الإحصاءات الأساسية --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <x-admin.stat-card label="إجمالي الكتب" value="{{ number_format($metrics['totalBooks']) }}" icon="📚" tone="indigo" />
    <x-admin.stat-card label="منشورة" value="{{ number_format($metrics['publishedBooks']) }}" icon="✅" tone="emerald" />
    <x-admin.stat-card label="مسودات" value="{{ number_format($metrics['draftBooks']) }}" icon="📝" tone="amber" />
    <x-admin.stat-card label="مخزون منخفض" value="{{ number_format($metrics['lowStockCount']) }}" icon="⚠️" tone="rose" />
  </div>

  {{-- شبكة بطاقات ثانوية --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <x-admin.stat-card label="التصنيفات" value="{{ number_format($metrics['categoriesCount']) }}" icon="🏷️" tone="slate" />
    <x-admin.stat-card label="المؤلفون" value="{{ number_format($metrics['authorsCount']) }}" icon="✍️" tone="slate" />
    <x-admin.stat-card label="الناشرون" value="{{ number_format($metrics['publishersCount']) }}" icon="🏢" tone="slate" />
    <x-admin.stat-card label="المستخدمون" value="{{ number_format($metrics['usersCount']) }}" icon="👥" tone="slate" />
  </div>

  {{-- مخطط بسيط (Spark Bars) + أعلى التصنيفات --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="col-span-2 bg-white border rounded-2xl p-5">
      <div class="flex items-center justify-between mb-4">
        <div class="font-semibold">الكتب المضافة (آخر 7 أيام)</div>
        <div class="text-xs text-gray-500">يوميًا</div>
      </div>

      @php
        $labels = $chart['labels'];
        $series = $chart['series'];
        $max    = max($series) ?: 1;
      @endphp

      <div class="flex items-end gap-2 h-40">
        @foreach($series as $i => $v)
          @php
            $h = 16 + intval(($v / $max) * 120); // ارتفاع العمود
          @endphp
          <div class="flex flex-col items-center justify-end">
            <div class="w-8 rounded-t-lg"
                 style="height: {{ $h }}px; background: linear-gradient(180deg,#4F46E5 0%, #8B5CF6 100%);"
                 title="{{ $labels[$i] }}: {{ $v }}"></div>
            <div class="text-[10px] text-gray-500 mt-1">{{ $labels[$i] }}</div>
          </div>
        @endforeach
      </div>
    </div>

    <div class="bg-white border rounded-2xl p-5">
      <div class="font-semibold mb-4">أعلى التصنيفات</div>
      <ul class="space-y-3">
        @forelse($topCategories as $c)
          <li class="flex items-center justify-between">
            <a href="{{ route('admin.categories.edit', $c) }}" class="hover:underline">{{ $c->name }}</a>
            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">كتب: {{ $c->books_count }}</span>
          </li>
        @empty
          <li class="text-sm text-gray-500">لا توجد بيانات بعد.</li>
        @endforelse
      </ul>
    </div>
  </div>

  {{-- أحدث الكتب --}}
  <div class="bg-white border rounded-2xl p-5">
    <div class="flex items-center justify-between mb-4">
      <div class="font-semibold">أحدث الكتب</div>
      <a href="{{ route('admin.books.index') }}" class="text-sm text-indigo-600 hover:underline">عرض كل الكتب</a>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr class="text-right">
            <th class="px-3 py-2">العنوان</th>
            <th class="px-3 py-2">التصنيف</th>
            <th class="px-3 py-2">الناشر</th>
            <th class="px-3 py-2">الحالة</th>
            <th class="px-3 py-2">أُضيف</th>
            <th class="px-3 py-2 text-center">إجراءات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentBooks as $b)
            <tr class="border-t">
              <td class="px-3 py-2 font-medium">{{ $b->title }}</td>
              <td class="px-3 py-2">{{ $b->category->name ?? '—' }}</td>
              <td class="px-3 py-2">{{ $b->publisher->name ?? '—' }}</td>
              <td class="px-3 py-2">
                @if($b->status === 'published')
                  <span class="px-2 py-1 text-xs rounded bg-emerald-100 text-emerald-700">منشور</span>
                @else
                  <span class="px-2 py-1 text-xs rounded bg-amber-100 text-amber-700">مسودة</span>
                @endif
              </td>
              <td class="px-3 py-2 text-gray-600">{{ $b->created_at->diffForHumans() }}</td>
              <td class="px-3 py-2 text-center">
                <a class="text-indigo-600 hover:underline mx-1" href="{{ route('admin.books.edit', $b) }}">تعديل</a>
                <a class="text-gray-600 hover:underline mx-1"  href="{{ route('books.show', $b) }}" target="_blank">عرض</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">لا توجد كتب بعد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection
