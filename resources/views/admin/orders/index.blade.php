@extends('admin.layouts.app')

@section('title', 'الطلبات')

@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-4">الطلبات</h1>

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
        @foreach($orders as $order)
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
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
