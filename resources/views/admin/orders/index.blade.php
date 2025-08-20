@extends('admin.layouts.app')

@section('title', 'الطلبات')

@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-4">الطلبات</h1>

  {{-- نموذج الفلاتر --}}
  <form method="GET" action="{{ route('admin.orders.index') }}" class="bg-white shadow rounded-2xl p-4 space-y-3 mb-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
      {{-- حالة الطلب --}}
      <div>
        <label class="block text-sm mb-1">حالة الطلب</label>
        <select name="status" class="w-full rounded-xl border-gray-300">
          <option value="">الكل</option>
          @foreach(['pending'=>'pending','processing'=>'processing','shipped'=>'shipped','cancelled'=>'cancelled'] as $val => $label)
            <option value="{{ $val }}" @selected(($filters['status'] ?? '') === $val)>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      {{-- حالة الدفع --}}
      <div>
        <label class="block text-sm mb-1">حالة الدفع</label>
        <select name="payment_status" class="w-full rounded-xl border-gray-300">
          <option value="">الكل</option>
          @foreach(['unpaid'=>'unpaid','paid'=>'paid','refunded'=>'refunded'] as $val => $label)
            <option value="{{ $val }}" @selected(($filters['payment_status'] ?? '') === $val)>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      {{-- بريد العميل --}}
      <div>
        <label class="block text-sm mb-1">بريد العميل</label>
        <input type="text" name="email" value="{{ $filters['email'] ?? '' }}" class="w-full rounded-xl border-gray-300" placeholder="example@email.com">
      </div>

      {{-- من تاريخ --}}
      <div>
        <label class="block text-sm mb-1">من تاريخ</label>
        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-xl border-gray-300">
      </div>

      {{-- إلى تاريخ --}}
      <div>
        <label class="block text-sm mb-1">إلى تاريخ</label>
        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-xl border-gray-300">
      </div>
    </div>

    <div class="flex items-center gap-2">
      <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700">تطبيق الفلاتر</button>
      <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">إعادة الضبط</a>
    </div>
  </form>

  {{-- الشارات (Badges) للفلاتر المفعّلة --}}
  @php $qs = request()->query(); @endphp
  @if(($filters['status'] ?? null) || ($filters['payment_status'] ?? null) || ($filters['email'] ?? null) || ($filters['from'] ?? null) || ($filters['to'] ?? null))
    <div class="mb-4 flex flex-wrap items-center gap-2">
      <span class="text-sm text-gray-600">فلاتر مفعّلة:</span>

      @if(!empty($filters['status']))
        @php $q = $qs; unset($q['status']); @endphp
        <a href="{{ route('admin.orders.index', $q) }}" class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200">
          حالة: {{ $filters['status'] }}
          <span class="text-gray-500">×</span>
        </a>
      @endif

      @if(!empty($filters['payment_status']))
        @php $q = $qs; unset($q['payment_status']); @endphp
        <a href="{{ route('admin.orders.index', $q) }}" class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200">
          الدفع: {{ $filters['payment_status'] }}
          <span class="text-gray-500">×</span>
        </a>
      @endif

      @if(!empty($filters['email']))
        @php $q = $qs; unset($q['email']); @endphp
        <a href="{{ route('admin.orders.index', $q) }}" class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200">
          البريد: {{ $filters['email'] }}
          <span class="text-gray-500">×</span>
        </a>
      @endif

      @if(!empty($filters['from']))
        @php $q = $qs; unset($q['from']); @endphp
        <a href="{{ route('admin.orders.index', $q) }}" class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200">
          من: {{ $filters['from'] }}
          <span class="text-gray-500">×</span>
        </a>
      @endif

      @if(!empty($filters['to']))
        @php $q = $qs; unset($q['to']); @endphp
        <a href="{{ route('admin.orders.index', $q) }}" class="inline-flex items-center gap-2 text-sm px-3 py-1 rounded-full bg-gray-100 hover:bg-gray-200">
          إلى: {{ $filters['to'] }}
          <span class="text-gray-500">×</span>
        </a>
      @endif
    </div>
  @endif

  <div class="bg-white shadow rounded-2xl overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr class="text-right">
          <th class="p-3">رقم الطلب</th>
          <th class="p-3">العميل</th>
          <th class="p-3">الدفع</th>
          <th class="p-3">حالة الطلب</th>
          <th class="p-3">التاريخ</th>
          <th class="p-3">…</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
          <tr class="border-t">
            <td class="p-3">{{ method_exists($order,'getNumberAttribute') ? $order->number : $order->id }}</td>
            <td class="p-3">{{ $order->user->name ?? '—' }}</td>
            <td class="p-3">
              <span class="px-2 py-1 rounded-full bg-gray-100 text-xs">{{ $order->payment_status ?? 'unpaid' }}</span>
            </td>
            <td class="p-3">
              <span class="px-2 py-1 rounded-full bg-gray-100 text-xs">{{ $order->status }}</span>
            </td>
            <td class="p-3">{{ $order->created_at->format('Y-m-d H:i') }}</td>
            <td class="p-3">
              <a class="text-blue-600 hover:underline" href="{{ route('admin.orders.show', $order) }}">عرض</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="p-6 text-center text-gray-500">لا توجد نتائج مطابقة للمرشّحات الحالية.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
