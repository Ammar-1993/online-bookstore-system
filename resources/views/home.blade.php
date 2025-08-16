<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>متجر الكتب</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900">
    <header class="bg-white border-b">
        <div class="container mx-auto px-4 py-3 flex items-center gap-4 justify-between">
            <a href="{{ route('home') }}" class="font-bold text-xl">متجر الكتب</a>

            {{-- ممكن تبقي الفورم هنا أو تحذفه لأن فيه واحد مشابه داخل المحتوى بالأسفل --}}
            <form action="{{ route('home') }}" method="GET" class="hidden md:flex items-center gap-2">
                <input name="q" value="{{ request('q') }}" placeholder="ابحث عن كتاب أو ISBN"
                    class="border rounded px-3 py-2 w-64" />
                <button class="px-4 py-2 bg-indigo-600 text-white rounded">بحث</button>
            </form>

            <nav class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="hover:text-indigo-600">حسابي</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="hover:text-red-600">خروج</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-indigo-600">دخول</a>
                    <a href="{{ route('register') }}" class="hover:text-indigo-600">تسجيل</a>
                @endauth
                <a href="{{ route('cart.index') }}" class="relative hover:text-indigo-600">
                    العربة
                    <span
                        class="absolute -top-2 -left-3 text-xs bg-indigo-600 text-white rounded px-1">{{ $cartCount ?? 0 }}</span>
                </a>

            </nav>
        </div>
    </header>

    <!-- Flash messages -->
    <x-flash-stack duration="10000" />

    <main class="container mx-auto px-4 py-8">



        {{-- شريط علوي داخل الصفحة (نفس فكرة صفحة المؤلف) --}}
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mb-6">
            <h2 class="text-lg font-semibold">
                الكتب المتاحة
                @if($books->total())
                    <span class="text-sm text-gray-500">({{ $books->total() }})</span>
                @endif
            </h2>

            <!-- <form  action="{{ route('home') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                <input name="q" value="{{ request('q') }}"
                       placeholder="ابحث داخل المتجر"
                       class="border rounded px-3 py-2 w-full sm:w-72 md:w-80" />
                <button class="px-4 py-2 bg-indigo-600 text-white rounded">بحث</button>
            </form> -->
        </div>

        @if($books->count() === 0)
            <p class="text-gray-600">لا توجد نتائج مطابقة.</p>
        @else
            {{-- شبكة البطاقات --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @foreach($books as $book)
                    <x-book-card :book="$book" />
                @endforeach
            </div>

            {{-- عدّاد + بيجينيشن RTL --}}
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-gray-600">
                <div>
                    @php
                        $from = ($books->currentPage() - 1) * $books->perPage() + 1;
                        $to = min($books->currentPage() * $books->perPage(), $books->total());
                    @endphp
                    عرض {{ $from }}–{{ $to }} من {{ $books->total() }} نتيجة
                </div>

                <div class="w-full sm:w-auto">
                    <!-- {{ $books->links() }} -->
                    {{ $books->onEachSide(1)->links('vendor.pagination.tailwind-rtl') }}
                </div>
            </div>
        @endif
    </main>
</body>

</html>