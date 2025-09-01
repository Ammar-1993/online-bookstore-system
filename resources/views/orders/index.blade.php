{{-- resources/views/orders/index.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">مشترياتي</h2>
  </x-slot>

  @php
    // التصفية الواردة (بدون use داخل Blade)
    $filters = ($filters ?? []) + [
      'status'         => request('status'),
      'payment_status' => request('payment_status'),
      'from'           => request('from'),
      'to'             => request('to'),
      'q'              => request('q'),
    ];

    // خرائط التسميات
    $labels = [
      'status' => [
        'pending'    => 'قيد الإنشاء',
        'processing' => 'قيد المعالجة',
        'shipped'    => 'تم الشحن',
        'completed'  => 'مكتمل',
        'cancelled'  => 'ملغي',
      ],
      'payment' => [
        'unpaid'   => 'غير مدفوع',
        'paid'     => 'مدفوع',
        'refunded' => 'مسترد',
      ],
    ];

    // ألوان الشارات
    $badge = fn(string $group, string $key) => [
      'status' => [
        'pending'    => 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-700/40 dark:text-gray-200',
        'processing' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-500/10 dark:text-indigo-200',
        'shipped'    => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-200',
        'completed'  => 'bg-sky-50 text-sky-700 ring-sky-600/20 dark:bg-sky-500/10 dark:text-sky-200',
        'cancelled'  => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/10 dark:text-rose-200',
      ],
      'payment' => [
        'paid'     => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-200',
        'refunded' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-200',
        'unpaid'   => 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-700/40 dark:text-gray-200',
      ],
    ][$group][$key] ?? 'bg-gray-100 text-gray-700 ring-gray-600/20 dark:bg-gray-700/40 dark:text-gray-200';

    $label = fn(string $group, ?string $key) => $labels[$group][$key] ?? $key ?? '—';
  @endphp

  <div class="container mx-auto p-4 space-y-4">

    {{-- فلاتر البحث --}}
    <form method="GET" action="{{ route('orders.index') }}"
          class="bg-white dark:bg-gray-900 rounded-2xl shadow p-4 grid md:grid-cols-4 gap-3 ring-1 ring-black/5 dark:ring-white/10">

      <div>
        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">حالة الطلب</label>
        <select name="status" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
          <option value="">الكل</option>
          @foreach($labels['status'] as $k => $v)
            <option value="{{ $k }}" @selected(($filters['status'] ?? '') === $k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">حالة الدفع</label>
        <select name="payment_status" class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
          <option value="">الكل</option>
          @foreach($labels['payment'] as $k => $v)
            <option value="{{ $k }}" @selected(($filters['payment_status'] ?? '') === $k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">من تاريخ</label>
        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
               class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
      </div>

      <div>
        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">إلى تاريخ</label>
        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
               class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
      </div>

      <div class="md:col-span-4 flex flex-wrap items-end justify-between gap-3">
        <div class="flex items-center gap-2">
          <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" dir="ltr"
                 placeholder="رقم الطلب (مثال: ORD-000123)"
                 class="w-72 rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400"
                 pattern="^[A-Za-z0-9#\-]{2,}$" inputmode="text" />
          <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">تصفية</button>
        </div>

        @if(array_filter(\Illuminate\Support\Arr::only($filters, ['status','payment_status','from','to','q'])))
          <a href="{{ route('orders.index') }}"
             class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">مسح الفلاتر</a>
        @endif
      </div>
    </form>

    @if($orders->isEmpty())
      <div class="rounded-2xl bg-white dark:bg-gray-900 shadow ring-1 ring-black/5 dark:ring-white/10 p-6 text-gray-600 dark:text-gray-300">
        لا توجد طلبات حتى الآن.
      </div>
    @else
      <div class="bg-white dark:bg-gray-900 shadow rounded-2xl overflow-x-auto ring-1 ring-black/5 dark:ring-white/10">
        <table class="min-w-full table-fixed text-sm text-gray-900 dark:text-gray-100">
          <colgroup>
            <col style="width:11rem"><col style="width:12rem"><col style="width:12rem">
            <col style="width:10rem"><col style="width:11rem"><col style="width:16rem">
          </colgroup>
          <thead class="bg-gray-50 dark:bg-gray-800/60">
            <tr class="text-left text-gray-700 dark:text-gray-200">
              <th class="p-3 font-medium text-center">رقم الطلب</th>
              <th class="p-3 font-medium text-center">التاريخ</th>
              <th class="p-3 font-medium text-center">الإجمالي</th>
              <th class="p-3 font-medium text-center">الدفع</th>
              <th class="p-3 font-medium text-center">الحالة</th>
              <th class="p-3 font-medium text-center">إجراءات</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($orders as $order)
              @php
                $number  = method_exists($order,'getNumberAttribute') ? $order->number : ('ORD-'.str_pad($order->id, 6, '0', STR_PAD_LEFT));
                $payment = $order->payment_status ?? 'unpaid';
                $status  = $order->status ?? 'pending';

                $total    = method_exists($order,'computedTotal') ? $order->computedTotal() : ($order->items->sum('total_price'));
                $currency = $order->currency ?: config('app.currency','USD');

                $isPayable    = method_exists($order,'isPayable')    ? $order->isPayable()    : ($payment !== 'paid' && $status !== 'cancelled');
                $isCancelable = method_exists($order,'isCancelable') ? $order->isCancelable() : ($status !== 'cancelled');
              @endphp
              <tr class="hover:bg-gray-50/70 dark:hover:bg-white/5">
                <td class="p-3">
                  <div class="flex items-center gap-2">
                    <a class="text-indigo-600 dark:text-indigo-400 hover:underline font-mono tabular-nums" dir="ltr"
                       href="{{ route('orders.show', $order) }}">{{ $number }}</a>
                    <button type="button" class="copy-btn text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
                            data-copy="{{ $number }}" aria-label="نسخ رقم الطلب" title="نسخ رقم الطلب">
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
                        <rect x="5" y="5" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2" opacity="0.6"/>
                      </svg>
                    </button>
                  </div>
                </td>

                <td class="p-3 tabular-nums" dir="ltr" title="{{ $order->created_at->format('Y-m-d H:i') }}">
                  {{ $order->created_at->format('Y-m-d H:i') }}
                </td>

                <td class="p-3 tabular-nums" dir="ltr">
                  {{ number_format($total, 2) }} {{ \Illuminate\Support\Str::upper($currency) }}
                </td>

                <td class="p-3 text-center">
                  <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs ring-1 ring-inset {{ $badge('payment',$payment) }}">
                    {{ $label('payment',$payment) }}
                  </span>
                </td>

                <td class="p-3 text-center">
                  <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs ring-1 ring-inset {{ $badge('status',$status) }}">
                    {{ $label('status',$status) }}
                  </span>
                </td>

                <td class="p-3">
                  <div class="flex flex-wrap items-center gap-2">
                    <a class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700"
                       href="{{ route('orders.show', $order) }}">عرض</a>

                    @if($isPayable)
                      <a class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700"
                         href="{{ route('payments.stripe.pay', $order) }}">ادفع</a>
                    @endif

                    @if($isCancelable)
                      <form method="POST" action="{{ route('orders.cancel', $order) }}" class="inline"
                            onsubmit="return confirm('هل أنت متأكد من إلغاء الطلب؟');">
                        @csrf
                        <button class="px-3 py-1.5 rounded-lg bg-rose-600 text-white hover:bg-rose-700">إلغاء</button>
                      </form>
                    @endif

                    <a href="{{ route('orders.invoice', $order) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">فاتورة</a>
                    <a href="{{ route('orders.invoice.pdf', $order) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-800 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">PDF</a>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-4">{{ $orders->withQueryString()->onEachSide(1)->links() }}</div>
    @endif
  </div>

  {{-- سكربت النسخ + علامة ✔ --}}
  <script>
    (function () {
      const CHECK =
        '<svg class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
      const CLIP =
        '<svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="9" y="9" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2"/><rect x="5" y="5" width="10" height="10" rx="2" stroke="currentColor" stroke-width="2" opacity="0.6"/></svg>';

      async function copyText(t) {
        try { await navigator.clipboard.writeText(t); return true; }
        catch {
          const ta = document.createElement('textarea'); ta.value = t; document.body.appendChild(ta);
          ta.select(); try { document.execCommand('copy'); return true; }
          finally { document.body.removeChild(ta); }
        }
      }

      document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.copy-btn[data-copy]');
        if (!btn) return;
        const txt = btn.getAttribute('data-copy') || '';
        if (await copyText(txt)) {
          btn.dataset.prev = btn.innerHTML;
          btn.innerHTML = CHECK; btn.setAttribute('aria-label','تم النسخ');
          setTimeout(() => { btn.innerHTML = CLIP; btn.setAttribute('aria-label','نسخ'); }, 1200);
        }
      });
    })();
  </script>
</x-app-layout>
