<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>سلة التسوق - متجر الكتب</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900">
    <header class="bg-white border-b">
        <div class="container mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="font-bold text-xl">متجر الكتب</a>
            <nav class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="hover:text-indigo-600">الرئيسية</a>
            </nav>
        </div>
    </header>

    <!-- Flash messages -->
    <x-flash-stack duration="10000" />

    <main class="container mx-auto px-4 py-8">


        <h1 class="text-2xl font-bold mb-6">سلة التسوق</h1>

        @if(empty($items))
            <p class="text-gray-600">سلتك فارغة.</p>
        @else
            <div class="overflow-x-auto bg-white border rounded">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr class="text-right">
                            <th class="px-4 py-3">الكتاب</th>
                            <th class="px-4 py-3 w-28">السعر</th>
                            <th class="px-4 py-3 w-40">الكمية</th>
                            <th class="px-4 py-3 w-28">الإجمالي</th>
                            <th class="px-4 py-3 w-24">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr class="border-t">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img class="w-14 h-20 object-cover rounded border"
                                            src="{{ $item['cover'] ? asset('storage/' . $item['cover']) : 'https://placehold.co/140x200' }}"
                                            alt="{{ $item['title'] }}">
                                        <div>
                                            <a class="font-medium hover:text-indigo-700"
                                                href="{{ route('books.show', $item['slug']) }}">
                                                {{ $item['title'] }}
                                            </a>
                                            <div class="text-xs text-gray-500 mt-1">المتوفر: {{ $item['max'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">{{ number_format($item['price'], 2) }} {{ $item['currency'] }}</td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('cart.update', $item['slug']) }}"
                                        class="flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <input type="number" name="qty" min="0" max="{{ $item['max'] }}"
                                            value="{{ $item['qty'] }}" class="w-20 border rounded px-2 py-1">
                                        <button class="px-3 py-1.5 rounded bg-gray-100 hover:bg-gray-200">تحديث</button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 font-medium">
                                    {{ number_format($item['price'] * $item['qty'], 2) }} {{ $item['currency'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('cart.remove', $item['slug']) }}">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:underline">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf @method('DELETE')
                    <button class="px-4 py-2 bg-gray-100 rounded hover:bg-gray-200">تفريغ السلة</button>
                </form>

                <div class="text-lg font-semibold">
                    المجموع: {{ number_format($subtotal, 2) }} {{ $currency }}
                </div>
            </div>

            <div class="mt-4 text-right">
                {{--  الدفع --}}
                <a href="{{ route('checkout.show') }}"
                    class="mt-3 block text-center px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                    متابعة الدفع
                </a>
                
            </div>
        @endif
    </main>
</body>

</html>