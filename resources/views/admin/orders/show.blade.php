@extends('admin.layouts.app')
@section('title', 'تفاصيل الطلب')

@section('content')
<div class="p-4 space-y-4">
  <div class="bg-white shadow rounded-2xl p-4">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-xl font-bold">الطلب #{{ $order->id }}</h1>
        <div class="text-gray-600 text-sm">{{ $order->user->name ?? '—' }} · {{ $order->user->email ?? '' }}</div>
      </div>
      <div class="flex items-center gap-2">
        <span class="px-2 py-1 rounded-full bg-gray-100">{{ $order->payment_status ?? 'unpaid' }}</span>
        <span class="px-2 py-1 rounded-full bg-gray-100">{{ $order->status }}</span>
      </div>
    </div>

    <div class="mt-3 grid sm:grid-cols-3 gap-3 text-sm">
      <div class="bg-gray-50 rounded-xl p-3">
        <div class="text-gray-500">Payment Intent</div>
        <div class="font-mono break-all">{{ $order->payment_intent_id ?? '—' }}</div>
      </div>
      <div class="bg-gray-50 rounded-xl p-3">
        <div class="text-gray-500">Charge</div>
        <div class="font-mono break-all">{{ $order->charge_id ?? '—' }}</div>
      </div>
      <div class="bg-gray-50 rounded-xl p-3">
        <div class="text-gray-500">Paid at</div>
        <div>{{ optional($order->paid_at)->format('Y-m-d H:i') ?? '—' }}</div>
      </div>
    </div>
  </div>

  <div class="bg-white shadow rounded-2xl p-4">
    <h2 class="font-semibold mb-2">العناصر</h2>
    <div class="divide-y">
      @foreach($order->items as $item)
        <div class="py-3 flex items-center justify-between">
          <div>{{ $item->book->title ?? 'كتاب' }}</div>
          <div class="text-sm text-gray-600">{{ $item->qty }} × {{ number_format($item->unit_price,2) }}</div>
          <div class="font-semibold">{{ number_format($item->total_price,2) }}</div>
        </div>
      @endforeach
    </div>
    <div class="mt-3 text-right font-bold">الإجمالي: {{ number_format($order->items->sum('total_price'), 2) }}</div>
  </div>

  {{-- ✅ قسم الشحن --}}
  <div class="bg-white shadow rounded-2xl p-4 space-y-3">
    <h2 class="font-semibold">الشحن</h2>

    @if($order->tracking_number)
      <div class="rounded-xl border p-3 bg-gray-50 text-sm">
        <div>شركة الشحن: <strong>{{ $order->shipping_carrier ?? '—' }}</strong></div>
        <div>رقم التتبع: 
          <strong>
            @if($order->tracking_url)
              <a class="text-indigo-600 hover:underline" target="_blank" rel="noopener"
                 href="{{ $order->tracking_url }}">{{ $order->tracking_number }}</a>
            @else
              {{ $order->tracking_number }}
            @endif
          </strong>
        </div>
        <div>تاريخ الشحن: <strong>{{ optional($order->shipped_at)->format('Y-m-d H:i') ?? '—' }}</strong></div>
      </div>
    @endif

    {{-- نموذج التحديث/الإضافة --}}
    <form method="POST" action="{{ route('admin.orders.ship', $order) }}" class="grid sm:grid-cols-3 gap-3">
      @csrf
      <div>
        <label class="text-sm">شركة الشحن (اختياري)</label>
        <select name="shipping_carrier" class="w-full rounded-xl border-gray-300">
          <option value="">—</option>
          @foreach(['ups'=>'UPS','fedex'=>'FedEx','dhl'=>'DHL','aramex'=>'Aramex','usps'=>'USPS'] as $val=>$label)
            <option value="{{ $val }}" @selected($order->shipping_carrier === $val)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="text-sm">رقم التتبع <span class="text-red-600">*</span></label>
        <input type="text" name="tracking_number" required value="{{ old('tracking_number', $order->tracking_number) }}"
               class="w-full rounded-xl border-gray-300" placeholder="مثال: 1Z999AA10123456784">
      </div>
      <div>
        <label class="text-sm">رابط التتبع (اختياري)</label>
        <input type="url" name="tracking_url" value="{{ old('tracking_url', $order->tracking_url) }}"
               class="w-full rounded-xl border-gray-300" placeholder="https://...">
      </div>

      <div class="sm:col-span-3">
        <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">
          حفظ معلومات الشحن / تمييز كـ (shipped)
        </button>
      </div>
    </form>
  </div>

  <div class="bg-white shadow rounded-2xl p-4 space-y-3">
    <h2 class="font-semibold">إجراءات</h2>

    @if(($order->payment_status ?? 'unpaid') === 'paid')
      <form method="POST" action="{{ route('admin.orders.refund', $order) }}" onsubmit="return confirm('تأكيد استرجاع المبلغ بالكامل؟');">
        @csrf
        <button class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">استرجاع المبلغ (Refund)</button>
      </form>
    @endif

    <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="flex flex-wrap items-center gap-2">
      @csrf @method('PUT')
      <label class="text-sm">حالة الطلب</label>
      <select name="status" class="rounded-xl border-gray-300">
        @foreach(['pending','processing','shipped','cancelled'] as $s)
          <option value="{{ $s }}" @selected($order->status === $s)>{{ $s }}</option>
        @endforeach
      </select>

      <label class="text-sm">حالة الدفع</label>
      <select name="payment_status" class="rounded-xl border-gray-300">
        @foreach(['unpaid','paid','refunded'] as $ps)
          <option value="{{ $ps }}" @selected(($order->payment_status ?? 'unpaid') === $ps)>{{ $ps }}</option>
        @endforeach
      </select>

      <button class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700">حفظ</button>
    </form>
  </div>
</div>
@endsection
