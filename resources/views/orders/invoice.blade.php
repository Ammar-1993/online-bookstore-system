<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>فاتورة الطلب #{{ $order->id }}</title>
    @vite('resources/css/app.css')
    <style>
        @media print {
            .no-print {
                display: none
            }

            body {
                background: white
            }
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">
    <div class="max-w-3xl mx-auto p-6 bg-white shadow mt-6 rounded-2xl">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold">فاتورة</h1>
                <div class="text-sm text-gray-600">رقم الطلب: #{{ $order->id }}</div>
                <div class="text-sm text-gray-600">التاريخ: {{ $order->created_at->format('Y-m-d H:i') }}</div>
            </div>
            <button class="no-print px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200"
                onclick="window.print()">طباعة</button>
        </div>

        <div class="mt-6">
            <h2 class="font-semibold mb-2">العميل</h2>
            <div>{{ $order->user->name ?? 'عميل' }}</div>
            <div class="text-sm text-gray-600">{{ $order->user->email ?? '' }}</div>
        </div>

        <div class="mt-6">
            <h2 class="font-semibold mb-2">العناصر</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-right">
                            <th class="p-2">الكتاب</th>
                            <th class="p-2">السعر</th>
                            <th class="p-2">الكمية</th>
                            <th class="p-2">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr class="border-t">
                                <td class="p-2">{{ $item->book->title ?? 'كتاب' }}</td>
                                <td class="p-2">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="p-2">{{ $item->qty }}</td>
                                <td class="p-2">{{ number_format($item->total_price, 2) }}</td>

                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t font-bold">
                            <td class="p-2" colspan="3">الإجمالي</td>
                            <td class="p-2">{{ number_format($order->computedTotal(), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</body>

</html>