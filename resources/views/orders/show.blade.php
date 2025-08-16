<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">تفاصيل الطلب #{{ $order->id }}</h2>
    </x-slot>

    <div class="container mx-auto p-4 space-y-4">
        <div class="bg-white shadow rounded-2xl p-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <h1 class="text-xl font-bold">الطلب #{{ $order->id }}</h1>
                <div class="flex items-center gap-2 text-sm">
                    <span class="px-2 py-1 rounded-full bg-gray-100">{{ $order->payment_status ?? 'unpaid' }}</span>
                    <span class="px-2 py-1 rounded-full bg-gray-100">{{ $order->status }}</span>
                </div>
            </div>
            <div class="text-gray-600 mt-1">بتاريخ: {{ $order->created_at->format('Y-m-d H:i') }}</div>
        </div>

        <div class="bg-white shadow rounded-2xl p-4">
            <h2 class="font-semibold mb-3">العناصر</h2>
            <div class="divide-y">
                @foreach($order->items as $item)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <div class="font-medium">{{ $item->book->title ?? 'كتاب' }}</div>
                            <div class="text-xs text-gray-500">الكمية: {{ $item->qty }} · السعر: {{ number_format($item->unit_price,2) }}</div>
                        </div>
                        <div class="font-semibold">{{ number_format($item->total_price, 2) }}</div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 text-right font-bold">الإجمالي: {{ number_format($order->computedTotal(), 2) }}</div>
        </div>

        <div class="bg-white shadow rounded-2xl p-4 flex flex-wrap gap-2">
            @if($order->isPayable())
                {{-- ✅ اسم الراوت الصحيح من ملف routes عندك: payments.mock.success (جمع) --}}
                <a href="{{ route('payments.mock.success', $order) }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
                    ادفع الآن (تجريبي)
                </a>
            @endif

            @if($order->isCancelable())
                <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('هل أنت متأكد من إلغاء الطلب؟');">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">إلغاء الطلب</button>
                </form>
            @endif

            <a href="{{ route('orders.invoice', $order) }}" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">عرض الفاتورة</a>
        </div>
    </div>
</x-app-layout>
