{{-- resources/views/admin/dashboard.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'لوحة التحكم')

@section('content')
  <div class="space-y-6">

    {{-- رأس صفحة أنيق + إجراءات سريعة --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
      <div>
        <h1 class="text-2xl font-display font-semibold text-gray-900 dark:text-gray-100">أهلًا بك في لوحة التحكم</h1>
        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">نظرة عامة سريعة على متجرك.</p>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.books.create') }}"
          class="inline-flex items-center gap-2 rounded-xl px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-700 shadow"
          data-ripple data-loader>
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
          </svg>
          إضافة كتاب
        </a>
        @role('Admin')
        <a href="{{ route('admin.categories.create') }}"
          class="rounded-xl px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100"
          data-ripple>+ تصنيف</a>
        <a href="{{ route('admin.authors.create') }}"
          class="rounded-xl px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100"
          data-ripple>+ مؤلف</a>
        <a href="{{ route('admin.publishers.create') }}"
          class="rounded-xl px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100"
          data-ripple>+ ناشر</a>
        @endrole
      </div>
    </div>

    {{-- تنبيه ذكي إن كان هناك مخزون منخفض --}}
    @if(($metrics['lowStockCount'] ?? 0) > 0)
      <div
        class="rounded-2xl px-4 py-3 bg-amber-50 dark:bg-amber-500/10 text-amber-800 dark:text-amber-200 ring-1 ring-amber-600/20">
        <div class="flex items-start gap-2">
          <svg class="w-5 h-5 mt-0.5" viewBox="0 0 24 24" fill="none">
            <path
              d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <div>يوجد <span class="font-medium tabular-nums">{{ number_format($metrics['lowStockCount']) }}</span> كتاب بمخزون
            منخفض. <a href="{{ route('admin.books.index', ['status' => null]) }}"
              class="underline underline-offset-4 decoration-amber-400 hover:text-amber-900 dark:hover:text-amber-100">اعرض
              الكتب</a>.</div>
        </div>
      </div>
    @endif

    {{-- الشبكة الرئيسية --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <x-admin.stat-card label="إجمالي الكتب" value="{{ number_format($metrics['totalBooks']) }}" icon="📚" tone="indigo"
        href="{{ route('admin.books.index') }}" />

      <x-admin.stat-card label="منشورة" value="{{ number_format($metrics['publishedBooks']) }}" icon="✅" tone="emerald"
        href="{{ route('admin.books.index', ['status' => 'published']) }}" />

      <x-admin.stat-card label="مسودات" value="{{ number_format($metrics['draftBooks']) }}" icon="📝" tone="amber"
        href="{{ route('admin.books.index', ['status' => 'draft']) }}" />

      {{-- غيّر بارامتر الفلتر حسب ما نفّذته في الكنترولر (مثال: stock=low أو lowStock=1) --}}
      <x-admin.stat-card label="مخزون منخفض" value="{{ number_format($metrics['lowStockCount']) }}" icon="⚠️" tone="rose"
        href="{{ route('admin.books.index', ['stock' => 'low']) }}" />
    </div>

    {{-- الشبكة الثانوية --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <x-admin.stat-card label="التصنيفات" value="{{ number_format($metrics['categoriesCount']) }}" icon="🏷️"
        tone="slate" href="{{ route('admin.categories.index') }}" />

      <x-admin.stat-card label="المؤلفون" value="{{ number_format($metrics['authorsCount']) }}" icon="✍️" tone="slate"
        href="{{ route('admin.authors.index') }}" />

      <x-admin.stat-card label="الناشرون" value="{{ number_format($metrics['publishersCount']) }}" icon="🏢" tone="slate"
        href="{{ route('admin.publishers.index') }}" />

      <x-admin.stat-card label="المستخدمون" value="{{ number_format($metrics['usersCount']) }}" icon="👥" tone="slate"
        href="{{ route('admin.users.index') }}" />
    </div>


    {{-- مخطط بسيط (Spark Bars) + أعلى التصنيفات --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      {{-- المخطط --}}
      <div class="col-span-2 bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 p-5">
        <div class="flex items-center justify-between mb-4">
          <div class="font-display font-semibold text-gray-900 dark:text-gray-100">الكتب المضافة (آخر 7 أيام)</div>
          <div class="text-xs text-gray-600 dark:text-gray-300">يوميًا</div>
        </div>

        @php
          $labels = $chart['labels'] ?? [];
          $series = $chart['series'] ?? [];
          $max = count($series) ? max($series) : 0;
          $total = array_sum($series);
        @endphp

        @if($max > 0)
          <div class="flex items-end gap-3 h-44">
            @foreach($series as $i => $v)
              @php $h = 16 + intval(($v / $max) * 140); @endphp
              <div class="flex flex-col items-center justify-end min-w-[2.25rem]">
                <div class="w-8 rounded-t-xl ring-1 ring-black/5 dark:ring-white/10"
                  style="height: {{ $h }}px; background: linear-gradient(180deg,#4F46E5 0%, #8B5CF6 100%);"
                  title="{{ $labels[$i] ?? '' }}: {{ $v }}"></div>
                <div class="text-[11px] text-gray-600 dark:text-gray-300 mt-1">{{ $labels[$i] ?? '' }}</div>
              </div>
            @endforeach
          </div>
          <div class="mt-3 text-xs text-gray-600 dark:text-gray-300">الإجمالي خلال الفترة: <span
              class="tabular-nums">{{ $total }}</span> كتاب.</div>
        @else
          <div class="h-44 flex items-center justify-center text-sm text-gray-600 dark:text-gray-300">لا توجد بيانات للعرض.
          </div>
        @endif
      </div>

      {{-- أعلى التصنيفات --}}
      <div class="bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 p-5">
        <div class="font-display font-semibold text-gray-900 dark:text-gray-100 mb-4">أعلى التصنيفات</div>
        @php $totalTop = collect($topCategories ?? [])->sum('books_count') ?: 0; @endphp
        <ul class="space-y-3">
          @forelse($topCategories as $c)
            @php $perc = $totalTop ? round(($c->books_count / $totalTop) * 100) : 0; @endphp
            <li>
              <div class="flex items-center justify-between mb-1">
                <a href="{{ route('admin.categories.edit', $c) }}"
                  class="hover:underline text-gray-900 dark:text-gray-100">{{ $c->name }}</a>
                <span class="text-xs text-gray-600 dark:text-gray-300">
                  كتب: <span class="tabular-nums">{{ $c->books_count }}</span> ({{ $perc }}%)
                </span>
              </div>
              <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                <div class="h-full bg-indigo-500 dark:bg-indigo-400" style="width: {{ $perc }}%"></div>
              </div>
            </li>
          @empty
            <li class="text-sm text-gray-600 dark:text-gray-300">لا توجد بيانات بعد.</li>
          @endforelse
        </ul>
      </div>
    </div>

    {{-- أحدث الكتب --}}
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 p-5">
      <div class="flex items-center justify-between mb-4">
        <div class="font-display font-semibold text-gray-900 dark:text-gray-100">أحدث الكتب</div>
        <a href="{{ route('admin.books.index') }}"
          class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">عرض كل الكتب</a>
      </div>

      <div class="overflow-x-auto">
        {{-- اجعل النص الافتراضي واضحاً في الوضع الداكن --}}
        <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
          <thead class="bg-gray-50 dark:bg-gray-800/60 sticky top-0 z-10">
            <tr class="text-right text-gray-700 dark:text-gray-200">
              <th class="px-3 py-2">العنوان</th>
              <th class="px-3 py-2">التصنيف</th>
              <th class="px-3 py-2">الناشر</th>
              <th class="px-3 py-2">الحالة</th>
              <th class="px-3 py-2">أُضيف</th>
              <th class="px-3 py-2 text-center">إجراءات</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($recentBooks as $b)
              <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5">
                <td class="px-3 py-2">
                  <div class="flex items-center gap-3">
                    {{-- صورة الغلاف مع بديل آمن --}}
                    <div
                      class="relative h-10 w-10 overflow-hidden rounded-lg ring-1 ring-black/5 dark:ring-white/10 bg-gray-100 dark:bg-gray-800 shrink-0">
                      @if($b->cover_image_path)
                        <img src="{{ asset('storage/' . $b->cover_image_path) }}" alt="غلاف {{ $b->title }}" loading="lazy"
                          decoding="async" class="absolute inset-0 h-full w-full object-cover"
                          onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden')">
                        {{-- Placeholder يُظهر تلقائيًا إن فشل تحميل الصورة --}}
                        <div class="hidden absolute inset-0 grid place-items-center text-gray-400">
                          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 5a2 2 0 0 1 2-2h8l6 6v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5Z" stroke="currentColor"
                              stroke-width="2" />
                            <path d="M14 3v4a2 2 0 0 0 2 2h4" stroke="currentColor" stroke-width="2" />
                          </svg>
                        </div>
                      @else
                        {{-- لا يوجد مسار غلاف --}}
                        <div class="absolute inset-0 grid place-items-center text-gray-400">
                          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 5a2 2 0 0 1 2-2h8l6 6v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5Z" stroke="currentColor"
                              stroke-width="2" />
                            <path d="M14 3v4a2 2 0 0 0 2 2h4" stroke="currentColor" stroke-width="2" />
                          </svg>
                        </div>
                      @endif
                    </div>

                    {{-- عنوان + ISBN بألوان واضحة --}}
                    <div class="min-w-0">
                      <div class="font-medium text-gray-900 dark:text-gray-100 truncate" title="{{ $b->title }}">
                        {{ $b->title }}
                      </div>
                      <div class="text-[11px] text-gray-600 dark:text-gray-300 tabular-nums">ISBN: {{ $b->isbn }}</div>
                    </div>
                  </div>
                </td>

                <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ $b->category->name ?? '—' }}</td>
                <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ $b->publisher->name ?? '—' }}</td>

                <td class="px-3 py-2">
                  @if($b->status === 'published')
                    <span
                      class="px-2 py-1 text-xs rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20">منشور</span>
                  @else
                    <span
                      class="px-2 py-1 text-xs rounded-full bg-amber-50 text-amber-700 ring-1 ring-amber-600/20">مسودة</span>
                  @endif
                </td>

                <td class="px-3 py-2 text-gray-700 dark:text-gray-300 whitespace-nowrap"
                  title="{{ $b->created_at->format('Y-m-d H:i') }}">
                  {{ $b->created_at->diffForHumans() }}
                </td>

                <td class="px-3 py-2 text-center">
                  <a class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 hover:underline mx-1"
                    href="{{ route('admin.books.edit', $b) }}" data-ripple data-loader>
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                      <path d="M4 21h4l11-11a2.828 2.828 0 1 0-4-4L4 17v4Z" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    تعديل
                  </a>
                  <a class="inline-flex items-center gap-1 text-gray-700 dark:text-gray-300 hover:underline mx-1"
                    href="{{ route('books.show', $b) }}" target="_blank" rel="noopener">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                      <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" stroke="currentColor" stroke-width="2" />
                      <circle cx="12" cy="12" r="3" fill="currentColor" />
                    </svg>
                    عرض
                  </a>
                </td>
              </tr>

            @empty
              <tr>
                <td colspan="6" class="px-3 py-6 text-center text-gray-600 dark:text-gray-300">لا توجد كتب بعد.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
@endsection