<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">مشترياتي</h2>
    </x-slot>

    <div class="container mx-auto p-4 space-y-4">

        {{-- فلاتر البحث --}}
        <form method="GET" action="{{ route('orders.index') }}" class="bg-white rounded-2xl shadow p-4 grid md:grid-cols-4 gap-3">
            @php
                $filters = $filters ?? [];
            @endphp

            <div>
                <label class="text-sm text-gray-600">حالة الطلب</label>
                <select name="status" class="w-full rounded border-gray-300">
                    <option value="">الكل</option>
                    @foreach(['pending'=>'قيد الإنشاء','processing'=>'قيد المعالجة','shipped'=>'تم الشحن','cancelled'=>'ملغي'] as $k=>$v)
                        <option value="{{ $k }}" @selected(($filters['status'] ?? '') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm text-gray-600">حالة الدفع</label>
                <select name="payment_status" class="w-full rounded border-gray-300">
                    <option value="">الكل</option>
                    @foreach(['unpaid'=>'غير مدفوع','paid'=>'مدفوع','refunded'=>'مسترد'] as $k=>$v)
                        <option value="{{ $k }}" @selected(($filters['payment_status'] ?? '') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm text-gray-600">من تاريخ</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded border-gray-300">
            </div>

            <div>
                <label class="text-sm text-gray-600">إلى تاريخ</label>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded border-gray-300">
            </div>

            <div class="md:col-span-4 flex items-end justify-between gap-3">
                <div class="flex items-center gap-2">
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="رقم الطلب (مثال: ORD-000123)"
                           class="w-64 rounded border-gray-300">
                    <button class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">تصفية</button>
                </div>
                @if(array_filter($filters ?? []))
                    <a href="{{ route('orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">مسح الفلاتر</a>
                @endif
            </div>
        </form>

        @if($orders->isEmpty())
            <div class="text-gray-600">لا توجد طلبات حتى الآن.</div>
        @else
            <div class="bg-white shadow rounded-2xl overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-right">
                            <th class="p-3">#</th>
                            <th class="p-3">التاريخ</th>
                            <th class="p-3">الإجمالي</th>
                            <th class="p-3">الدفع</th>
                            <th class="p-3">الحالة</th>
                            <th class="p-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            @php
                                $number  = method_exists($order,'getNumberAttribute') ? $order->number : ('#'.$order->id);
                                $payment = $order->payment_status ?? 'unpaid';
                                $status  = $order->status ?? 'pending';

                                $payClass = [
                                    'paid'     => 'bg-emerald-100 text-emerald-700',
                                    'refunded' => 'bg-amber-100 text-amber-700',
                                    'unpaid'   => 'bg-gray-100 text-gray-700',
                                ][$payment] ?? 'bg-gray-100 text-gray-700';

                                $statusClass = [
                                    'pending'    => 'bg-gray-100 text-gray-700',
                                    'processing' => 'bg-blue-100 text-blue-700',
                                    'shipped'    => 'bg-indigo-100 text-indigo-700',
                                    'cancelled'  => 'bg-rose-100 text-rose-700',
                                ][$status] ?? 'bg-gray-100 text-gray-700';

                                $total = method_exists($order,'computedTotal')
                                    ? $order->computedTotal()
                                    : ($order->items->sum('total_price'));
                                $currency = $order->currency ?: config('app.currency','USD');

                                $isPayable = method_exists($order,'isPayable')
                                    ? $order->isPayable()
                                    : ($payment !== 'paid' && $status !== 'cancelled');

                                $isCancelable = method_exists($order,'isCancelable')
                                    ? $order->isCancelable()
                                    : ($status !== 'cancelled');
                            @endphp
                            <tr class="border-t">
                                <td class="p-3 font-medium">{{ $number }}</td>
                                <td class="p-3">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                <td class="p-3">{{ number_format($total, 2) }} {{ $currency }}</td>
                                <td class="p-3"><span class="px-2 py-1 rounded-full {{ $payClass }}">{{ $payment }}</span></td>
                                <td class="p-3"><span class="px-2 py-1 rounded-full {{ $statusClass }}">{{ $status }}</span></td>
                                <td class="p-3 space-x-2 space-x-reverse">
                                    <a class="text-blue-600 hover:underline" href="{{ route('orders.show', $order) }}">عرض</a>

                                    @if($isPayable)
                                        <a href="{{ route('payments.stripe.pay', $order) }}" class="text-indigo-600 hover:underline">ادفع</a>
                                    @endif

                                    @if($isCancelable)
                                        <form method="POST" action="{{ route('orders.cancel', $order) }}" class="inline"
                                              onsubmit="return confirm('هل أنت متأكد من إلغاء الطلب؟');">
                                            @csrf
                                            <button class="text-rose-600 hover:underline">إلغاء</button>
                                        </form>
                                    @endif

                                    <a href="{{ route('orders.invoice', $order) }}" class="text-gray-700 hover:underline">فاتورة</a>
                                    <a href="{{ route('orders.invoice.pdf', $order) }}" class="text-gray-700 hover:underline">PDF</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $orders->links() }}</div>
        @endif
    </div>
</x-app-layout>
