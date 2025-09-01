{{-- resources/views/admin/orders/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'الطلبات')

@section('content')
  @php
    use Illuminate\Support\Arr;

    // فرز افتراضي
    $sort = $sort ?? request('sort', 'created_at');
    $dir = $dir ?? request('dir', 'desc');

    // خرائط التسميات
    $labels = [
      'status' => [
        'pending' => 'قيد الانتظار',
        'processing' => 'قيد المعالجة',
        'shipped' => 'تم الشحن',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي',
      ],
      'payment' => [
        'unpaid' => 'غير مدفوع',
        'paid' => 'مدفوع',
        'refunded' => 'مسترد',
      ],
    ];

    // خرائط شارات الألوان
    $badgeClasses = [
      'status' => [
        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-200',
        'processing' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-200',
        'shipped' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-200',
        'completed' => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-500/10 dark:text-sky-200',
        'cancelled' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-200',
      ],
      'payment' => [
        'paid' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-200',
        'refunded' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-200',
        'unpaid' => 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-700/50 dark:text-gray-300',
      ],
    ];

    // مساعدين
    $label = fn(string $group, ?string $key, ?string $fallback = null)
      => $labels[$group][$key] ?? $fallback ?? $key ?? '—';

    $badge = fn(string $group, ?string $key)
      => $badgeClasses[$group][$key] ?? 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-700/50 dark:text-gray-300';

    // رابط الفرز
    $sortLink = function (string $field, string $text) use ($sort, $dir) {
      $is = $sort === $field;
      $next = $is && $dir === 'asc' ? 'desc' : 'asc';
      $url = request()->fullUrlWithQuery(['sort' => $field, 'dir' => $next, 'page' => null]);

      $arrowUp = '<svg class="ms-1 w-3.5 h-3.5 inline" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M8 15l4-4 4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
      $arrowDn = '<svg class="ms-1 w-3.5 h-3.5 inline" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M16 9l-4 4-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
      $arrow = $is ? ($dir === 'asc' ? $arrowUp : $arrowDn) : '';

      return '<a href="' . $url . '" class="inline-flex items-center hover:underline">' . $text . $arrow . '</a>';
    };

    $filters = $filters ?? [];
    $qs = request()->query();
  @endphp

  <div class="space-y-5">
    {{-- العنوان + العدّاد --}}
    <div class="flex items-center justify-between">
      <div class="flex flex-col gap-1">
        <h1 class="text-2xl font-display font-semibold text-gray-900 dark:text-black-100">الطلبات</h1>
      </div>
      <div class="text-sm text-gray-700 dark:text-black-300">
        إجمالي النتائج:
        <span
          class="tabular-nums font-semibold px-2 py-0.5 rounded-md bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-100">
          {{ number_format($orders->total()) }}
        </span>
      </div>
    </div>

    {{-- نموذج الفلاتر --}}
    <form method="GET" action="{{ route('admin.orders.index') }}"
      class="bg-white dark:bg-gray-900 shadow rounded-2xl p-4 ring-1 ring-black/5 dark:ring-white/10">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
        {{-- حالة الطلب --}}
        <div>
          <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">حالة الطلب</label>
          <div class="relative">
            <select name="status"
              class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 pe-8">
              <option value="">الكل</option>
              @foreach($labels['status'] as $val => $text)
                <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ $text }}</option>
              @endforeach
            </select>
            <span class="pointer-events-none absolute inset-y-0 left-2 flex items-center text-gray-400">▾</span>
          </div>
        </div>

        {{-- حالة الدفع --}}
        <div>
          <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">حالة الدفع</label>
          <div class="relative">
            <select name="payment_status"
              class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 pe-8">
              <option value="">الكل</option>
              @foreach($labels['payment'] as $val => $text)
                <option value="{{ $val }}" @selected(($filters['payment_status'] ?? '') === $val)>{{ $text }}</option>
              @endforeach
            </select>
            <span class="pointer-events-none absolute inset-y-0 left-2 flex items-center text-gray-400">▾</span>
          </div>
        </div>

        {{-- البريد الإلكتروني --}}
        <div class="sm:col-span-2">
          <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">البريد الإلكتروني</label>
          <input type="text" name="email" value="{{ $filters['email'] ?? '' }}"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400"
            placeholder="example@email.com" dir="ltr" inputmode="email">
        </div>

        {{-- من تاريخ --}}
        <div>
          <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">من تاريخ</label>
          <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
            placeholder="dd/mm/yyyy">
        </div>

        {{-- إلى تاريخ --}}
        <div>
          <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">إلى تاريخ</label>
          <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"
            placeholder="dd/mm/yyyy">
        </div>
      </div>

      {{-- الحفاظ على الفرز أثناء تطبيق الفلاتر --}}
      <input type="hidden" name="sort" value="{{ $sort }}">
      <input type="hidden" name="dir" value="{{ $dir }}">

      <div class="mt-3 flex flex-wrap items-center gap-2">
        <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm" data-ripple>تطبيق
          الفلاتر</button>
        <a href="{{ route('admin.orders.index', ['sort' => $sort, 'dir' => $dir]) }}"
          class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">إعادة
          الضبط</a>
      </div>
    </form>

    {{-- بادجات الفلاتر النشطة --}}
    @if(($filters['status'] ?? null) || ($filters['payment_status'] ?? null) || ($filters['email'] ?? null) || ($filters['from'] ?? null) || ($filters['to'] ?? null))
      <div class="flex flex-wrap items-center gap-2">
        <span class="text-sm text-gray-600 dark:text-gray-300">فلاتر مفعّلة:</span>

        @if(!empty($filters['status']))
          <a href="{{ route('admin.orders.index', Arr::except($qs, ['status'])) }}"
            class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">
            حالة: {{ $label('status', $filters['status']) }} <span class="text-gray-500">×</span>
          </a>
        @endif

        @if(!empty($filters['payment_status']))
          <a href="{{ route('admin.orders.index', Arr::except($qs, ['payment_status'])) }}"
            class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">
            الدفع: {{ $label('payment', $filters['payment_status']) }} <span class="text-gray-500">×</span>
          </a>
        @endif

        @if(!empty($filters['email']))
          <a href="{{ route('admin.orders.index', Arr::except($qs, ['email'])) }}"
            class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">
            البريد: {{ $filters['email'] }} <span class="text-gray-500">×</span>
          </a>
        @endif

        @if(!empty($filters['from']))
          <a href="{{ route('admin.orders.index', Arr::except($qs, ['from'])) }}"
            class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">
            من: {{ $filters['from'] }} <span class="text-gray-500">×</span>
          </a>
        @endif

        @if(!empty($filters['to']))
          <a href="{{ route('admin.orders.index', Arr::except($qs, ['to'])) }}"
            class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">
            إلى: {{ $filters['to'] }} <span class="text-gray-500">×</span>
          </a>
        @endif
      </div>
    @endif

    {{-- جدول (ديسكتوب) --}}
    <div
      class="hidden md:block bg-white dark:bg-gray-900 shadow rounded-2xl overflow-x-auto ring-1 ring-black/5 dark:ring-white/10">
      <table class="min-w-full table-fixed text-sm text-gray-900 dark:text-gray-100">
        {{-- تثبيت أعمدة الجدول لضمان التطابق بين thead وtbody --}}
        <colgroup>
          <col style="width:10rem"> {{-- رقم الطلب --}}
          <col style="width:12rem"> {{-- العميل --}}
          <col style="width:18rem"> {{-- البريد الإلكتروني --}}
          <col style="width:9rem"> {{-- الدفع --}}
          <col style="width:10rem"> {{-- حالة الطلب --}}
          <col style="width:10rem"> {{-- التاريخ --}}
          <col style="width:8rem"> {{-- إجراءات --}}
        </colgroup>

        <thead class="bg-gray-50 dark:bg-gray-800/60 sticky top-0 z-10">
          <tr class="text-right text-gray-700 dark:text-gray-200">
            <th class="p-3 font-medium">{!! $sortLink('id', 'رقم الطلب') !!}</th>
            <th class="p-3 font-medium">{!! $sortLink('user', 'العميل') !!}</th>
            <th class="p-3 font-medium">{!! $sortLink('email', 'البريد الإلكتروني') !!}</th>
            <th class="p-3 font-medium text-center">{!! $sortLink('payment_status', 'الدفع') !!}</th>
            <th class="p-3 font-medium text-center">{!! $sortLink('status', 'حالة الطلب') !!}</th>
            <th class="p-3 font-medium text-right">{!! $sortLink('created_at', 'التاريخ') !!}</th>
            <th class="p-3 font-medium text-right">إجراءات</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse($orders as $order)
            @php
              $number = method_exists($order, 'getNumberAttribute') ? $order->number : ('ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT));
              $payment = $order->payment_status ?? 'unpaid';
              $email = $order->user?->email;
            @endphp
            <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5">
              {{-- 1) رقم الطلب + نسخ بعلامة ✔ --}}
              <td class="p-3">
                <div class="flex items-center gap-2">
                  <a class="text-indigo-600 dark:text-indigo-400 hover:underline font-mono tabular-nums" dir="ltr"
                    href="{{ route('admin.orders.show', $order) }}">{{ $number }}</a>
                  <button type="button" class="copy-btn text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                    data-copy="{{ $number }}" aria-label="نسخ رقم الطلب" title="نسخ رقم الطلب">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                      <rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2" />
                      <rect x="5" y="5" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"
                        opacity="0.6" />
                    </svg>
                  </button>
                </div>
              </td>

              {{-- 2) العميل --}}
              <td class="p-3">
                <div class="min-w-0 font-medium truncate">{{ $order->user->name ?? '—' }}</div>
              </td>

              {{-- 3) البريد الإلكتروني (المحتوى LTR ومحاذاة يسار داخل الخلية فقط) --}}
              <td class="p-3 text-left" dir="rtl">
                @if($email)
                  <div class="flex items-center gap-2 overflow-hidden">
                    <a href="mailto:{{ $email }}"
                      class="truncate text-[12.5px] text-gray-700 dark:text-gray-200 hover:underline"
                      title="{{ $email }}">{{ $email }}</a>
                    <button type="button" class="copy-btn text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                      data-copy="{{ $email }}" aria-label="نسخ البريد" title="نسخ البريد">
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2" />
                        <rect x="5" y="5" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"
                          opacity="0.6" />
                      </svg>
                    </button>
                  </div>
                @else
                  —
                @endif
              </td>

              {{-- 4) الدفع --}}
              <td class="p-3 text-center">
                <span
                  class="inline-flex items-center rounded-full px-2 py-0.5 text-xs ring-1 ring-inset {{ $badge('payment', $payment) }}">
                  {{ $label('payment', $payment) }}
                </span>
              </td>

              {{-- 5) الحالة --}}
              <td class="p-3 text-center">
                <span
                  class="inline-flex items-center rounded-full px-2 py-0.5 text-xs ring-1 ring-inset {{ $badge('status', $order->status) }}">
                  {{ $label('status', $order->status) }}
                </span>
              </td>

              {{-- 6) التاريخ --}}
              <td class="p-3 text-right text-gray-700 dark:text-gray-300 whitespace-nowrap"
                title="{{ $order->created_at->format('Y-m-d H:i') }}">
                {{ $order->created_at->diffForHumans() }}
              </td>

              {{-- 7) الإجراءات --}}
              <td class="p-3">
                <a class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"
                  data-ripple href="{{ route('admin.orders.show', $order) }}">عرض</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="p-6 text-center text-gray-500 dark:text-gray-300">لا توجد نتائج مطابقة للمرشّحات
                الحالية.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>


    {{-- بطاقات (موبايل) --}}
    <div class="md:hidden grid gap-3">
      @forelse($orders as $order)
        @php
          $number = method_exists($order, 'getNumberAttribute')
            ? $order->number
            : ('ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT));
          $payment = $order->payment_status ?? 'unpaid';
        @endphp
        <div class="rounded-2xl bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10 p-4">
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
              <a class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline font-mono tabular-nums" dir="ltr"
                href="{{ route('admin.orders.show', $order) }}">{{ $number }}</a>
              <button type="button" class="copy-btn text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                data-copy="{{ $number }}" aria-label="نسخ رقم الطلب {{ $number }}" title="نسخ رقم الطلب">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2" />
                  <rect x="5" y="5" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2" opacity="0.6" />
                </svg>
              </button>
            </div>
            <div class="flex items-center gap-2">
              <span
                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs ring-1 ring-inset {{ $badge('payment', $payment) }}">
                {{ $label('payment', $payment) }}
              </span>
              <span
                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs ring-1 ring-inset {{ $badge('status', $order->status) }}">
                {{ $label('status', $order->status) }}
              </span>
            </div>
          </div>

          <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
            <div class="font-medium">{{ $order->user->name ?? '—' }}</div>
            @if($order->user?->email)
              <div class="flex items-center gap-2 text-[12px] text-gray-500 dark:text-gray-300" dir="ltr">
                <a href="mailto:{{ $order->user->email }}" class="hover:underline truncate">{{ $order->user->email }}</a>
                <button type="button" class="copy-btn text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                  data-copy="{{ $order->user->email }}" aria-label="نسخ البريد {{ $order->user->email }}" title="نسخ البريد">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2" />
                    <rect x="5" y="5" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2" opacity="0.6" />
                  </svg>
                </button>
              </div>
            @endif
          </div>

          <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            {{ $order->created_at->diffForHumans() }}
          </div>

          <div class="mt-3">
            <a class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"
              data-ripple href="{{ route('admin.orders.show', $order) }}">عرض</a>
          </div>
        </div>
      @empty
        <div
          class="rounded-2xl bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10 p-6 text-center text-gray-500 dark:text-gray-300">
          لا توجد نتائج مطابقة للمرشّحات الحالية.
        </div>
      @endforelse
    </div>

    {{-- ترقيم الصفحات --}}
    <div class="mt-4 flex justify-center">
      {{ $orders->withQueryString()->onEachSide(1)->links() }}
    </div>
  </div>

  {{-- سكربت: نسخ + علامة الصح --}}
  <script>
    (function () {
      const CHECK_SVG =
        '<svg class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

      const CLIP_SVG =
        '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">' +
        '<rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"/>' +
        '<rect x="5" y="5" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2" opacity="0.6"/></svg>';

      function setIcon(btn, svg) { btn.innerHTML = svg; }

      async function copy(text) {
        try {
          await navigator.clipboard.writeText(text);
          return true;
        } catch (e) {
          // fallback
          const ta = document.createElement('textarea');
          ta.value = text; document.body.appendChild(ta);
          ta.select();
          try { document.execCommand('copy'); return true; }
          catch { return false; }
          finally { document.body.removeChild(ta); }
        }
      }

      document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.copy-btn[data-copy]');
        if (!btn) return;

        const toCopy = btn.getAttribute('data-copy') || '';
        const ok = await copy(toCopy);

        if (ok) {
          const prev = btn.innerHTML;
          setIcon(btn, CHECK_SVG);
          btn.setAttribute('aria-label', 'تم النسخ');
          setTimeout(() => { setIcon(btn, CLIP_SVG); btn.setAttribute('aria-label', 'نسخ'); }, 1200);
        }
      });
    })();
  </script>
@endsection