<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>سلة التسوق - المتجر الإلكتروني للكتب</title>
    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 antialiased">
    <header class="sticky top-0 z-40 backdrop-blur bg-white/90 dark:bg-gray-900/80 border-b border-black/5 dark:border-white/10">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="font-bold text-xl text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition" data-ripple data-loader>المتجر الإلكتروني للكتب</a>
            <nav class="flex items-center gap-3 text-sm">
                @include('partials.theme-toggle-button')
                <a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400" data-ripple data-loader>الرئيسية</a>
            </nav>
        </div>
    </header>

    {{-- Flash messages --}}
    <x-flash-stack duration="10000" />

    <main class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">سلة التسوق</h1>

        @if(empty($items))
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 p-8 text-center text-gray-600 dark:text-gray-300">
                السلة فارغة حاليًا.
                <a href="{{ route('home') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline" data-ripple data-loader>
                    تابع التسوق
                </a>
            </div>
        @else
            <div class="grid lg:grid-cols-3 gap-6">
                {{-- الجدول --}}
                <div class="lg:col-span-2">
                    <div class="overflow-x-auto bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-700 dark:text-gray-200">
                                <tr class="text-right">
                                    <th class="px-4 py-3">الكتاب</th>
                                    <th class="px-4 py-3 w-28">السعر</th>
                                    <th class="px-4 py-3 w-44">الكمية</th>
                                    <th class="px-4 py-3 w-28">الإجمالي</th>
                                    <th class="px-4 py-3 w-24">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                                @foreach($items as $item)
                                    @php
                                        $title    = $item['title']    ?? '';
                                        $slug     = $item['slug']     ?? '';
                                        $cover    = $item['cover']    ?? null;
                                        $price    = (float)($item['price'] ?? 0);
                                        $qty      = (int)($item['qty']   ?? 1);
                                        $max      = (int)($item['max']   ?? 99);
                                        $currency = $item['currency']   ?? (config('app.currency','USD'));
                                        $lineTotal = $price * $qty;
                                    @endphp
                                    <tr class="hover:bg-gray-50/70 dark:hover:bg-white/5">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <img
                                                    class="w-14 h-20 object-cover rounded border border-gray-200 dark:border-gray-700"
                                                    src="{{ $cover ? asset('storage/'.$cover) : 'https://placehold.co/140x200' }}"
                                                    alt="{{ $title }}">
                                                <div>
                                                    <a class="font-medium text-gray-900 dark:text-gray-100 hover:text-indigo-700 dark:hover:text-indigo-400"
                                                       href="{{ route('books.show', $slug) }}"
                                                       data-ripple data-loader>
                                                        {{ $title }}
                                                    </a>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        @if($max > 0)
                                                            المتوفر: {{ $max }}
                                                        @else
                                                            <span class="px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">غير متاح</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-4 py-3 tabular-nums">{{ number_format($price, 2) }} {{ $currency }}</td>

                                        <td class="px-4 py-3">
                                            <form method="POST" action="{{ route('cart.update', $slug) }}" class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input
                                                    type="number"
                                                    name="qty"
                                                    min="0"
                                                    max="{{ $max }}"
                                                    value="{{ $qty }}"
                                                    class="w-24 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-xl px-2 py-1 text-center"
                                                    @if($max <= 0) disabled @endif
                                                >
                                                <button
                                                    class="px-3 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100 disabled:opacity-60"
                                                    @if($max <= 0) disabled @endif
                                                    data-ripple>
                                                    تحديث
                                                </button>
                                            </form>
                                        </td>

                                        <td class="px-4 py-3 font-medium tabular-nums">
                                            {{ number_format($lineTotal, 2) }} {{ $currency }}
                                        </td>

                                        <td class="px-4 py-3">
                                            <form method="POST" action="{{ route('cart.remove', $slug) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-rose-600 dark:text-rose-400 hover:underline" data-ripple>حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- إفراغ السلة --}}
                    <form method="POST" action="{{ route('cart.clear') }}" class="mt-4">
                        @csrf
                        @method('DELETE')
                        <button class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100" data-ripple>
                            تفريغ السلة
                        </button>
                    </form>
                </div>

                {{-- الملخص --}}
                <aside class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 p-4">
                        <h2 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">ملخص الطلب</h2>

                        <div class="flex items-center justify-between py-2">
                            <span class="text-gray-600 dark:text-gray-400">المجموع</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100 tabular-nums">
                                {{ number_format((float)$subtotal, 2) }} {{ $currency ?? config('app.currency','USD') }}
                            </span>
                        </div>

                        <a href="{{ route('checkout.show') }}"
                           class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700"
                           data-ripple data-loader>
                            متابعة الدفع
                        </a>

                        <a href="{{ route('home') }}"
                           class="mt-2 block text-center text-sm text-indigo-600 dark:text-indigo-400 hover:underline"
                           data-ripple data-loader>
                            العودة للتسوق
                        </a>
                    </div>
                </aside>
            </div>
        @endif
    </main>

    {{-- لازم نضيف اللودر هنا لأن الصفحة لا تستخدم layout --}}
    <x-page-loader />
</body>
</html>
