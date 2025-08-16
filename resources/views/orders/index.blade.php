<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">طلباتي</h2>
    </x-slot>

    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">طلباتي</h1>

        @if($orders->isEmpty())
            <div class="text-gray-600">لا توجد طلبات حتى الآن.</div>
        @else
            <div class="bg-white shadow rounded-2xl overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-right">
                            <th class="p-3">#</th>
                            <th class="p-3">التاريخ</th>
                            <th class="p-3">حالة الدفع</th>
                            <th class="p-3">حالة الطلب</th>
                            <th class="p-3">الإجمالي</th>
                            <th class="p-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr class="border-t">
                                <td class="p-3">{{ $order->id }}</td>
                                <td class="p-3">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                <td class="p-3"><span class="px-2 py-1 rounded-full bg-gray-100">{{ $order->payment_status ?? 'unpaid' }}</span></td>
                                <td class="p-3"><span class="px-2 py-1 rounded-full bg-gray-100">{{ $order->status }}</span></td>
                                <td class="p-3">{{ number_format($order->computedTotal(), 2) }}</td>
                                <td class="p-3"><a class="text-blue-600 hover:underline" href="{{ route('orders.show', $order) }}">عرض</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $orders->links() }}</div>
        @endif
    </div>
</x-app-layout>
