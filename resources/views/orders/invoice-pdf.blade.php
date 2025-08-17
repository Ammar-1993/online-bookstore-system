<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8" />
    <style>
        body { font-family: "DejaVu Sans", Arial, Helvetica, sans-serif; direction: rtl; text-align: right; color:#111827; font-size: 12px; margin: 0; }
        .container { width:100%; padding:16px; }
        .title { font-size: 20px; font-weight: bold; margin: 0 0 4px 0; }
        .muted { color:#6b7280; font-size:12px; margin:2px 0; }
        .box { border:1px solid #e5e7eb; border-radius:8px; padding:12px; margin-top:10px; }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:8px; border-top:1px solid #e5e7eb; text-align:right; }
        th { background:#f9fafb; font-weight:bold; }
        .totals td { font-weight: bold; }
        .ltr { direction:ltr; text-align:left; }
        .num { direction:ltr; text-align:right; }
    </style>
</head>
<body>
    <div class="container">
        <div>
            <div class="title">فاتورة</div>
            <div class="muted">رقم الطلب: {{ method_exists($order,'getNumberAttribute') ? $order->number : ('#'.$order->id) }}</div>
            <div class="muted">التاريخ: {{ $order->created_at?->format('Y-m-d H:i') }}</div>
        </div>

        <div class="box">
            <div style="font-weight:bold; margin-bottom:6px;">العميل</div>
            <div>{{ $order->user->name ?? 'عميل' }}</div>
            <div class="muted ltr">{{ $order->user->email ?? '' }}</div>
        </div>

        <div class="box">
            <div style="font-weight:bold; margin-bottom:6px;">العناصر</div>
            <table>
                <thead>
                    <tr>
                        <th>الكتاب</th>
                        <th>السعر</th>
                        <th>الكمية</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->book->title ?? 'كتاب' }}</td>
                            <td class="num">
                                {{ number_format($item->unit_price,2) }}
                                {{ $order->currency ?: config('app.currency','USD') }}
                            </td>
                            <td class="num">{{ $item->qty }}</td>
                            <td class="num">
                                {{ number_format($item->total_price,2) }}
                                {{ $order->currency ?: config('app.currency','USD') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="totals">
                        <td colspan="3">الإجمالي</td>
                        <td class="num">
                            {{ number_format(method_exists($order,'computedTotal') ? $order->computedTotal() : $order->items->sum('total_price'), 2) }}
                            {{ $order->currency ?: config('app.currency','USD') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="muted" style="margin-top:16px;">
            تم إنشاء هذه الفاتورة إلكترونيًا من {{ config('app.name') }}.
        </div>
    </div>
</body>
</html>
