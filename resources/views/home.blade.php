<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>المتجر الإلكتروني للكتب</title>
    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 antialiased">
    <header class="sticky top-0 z-40 backdrop-blur bg-white/90 dark:bg-gray-900/80 border-b border-black/5 dark:border-white/10">
        <div class="container mx-auto px-4 py-3 flex items-center gap-4 justify-between">
            <a href="{{ route('home') }}" class="font-bold text-xl text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition">المتجر الإلكتروني للكتب</a>

            {{-- ممكن تبقي الفورم هنا أو تحذفه لأن فيه واحد مشابه داخل المحتوى بالأسفل --}}
            <form action="{{ route('home') }}" method="GET" class="hidden md:flex items-center gap-2">
                <input name="q" value="{{ request('q') }}" placeholder="ابحث عن كتاب أو ISBN"
                    class="border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 rounded-xl px-3 py-2 w-64 focus:border-indigo-500 focus:ring-indigo-500" />
                <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition">بحث</button>
            </form>

            <nav class="flex items-center gap-3">
                @include('partials.theme-toggle-button')
                @auth
                    <a href="{{ url('/dashboard') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">حسابي</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="hover:text-red-600 dark:hover:text-red-400">خروج</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">دخول</a>
                    <a href="{{ route('register') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400">تسجيل</a>
                @endauth
                <a href="{{ route('cart.index') }}" class="relative hover:text-indigo-600 dark:hover:text-indigo-400">
                    السلة
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
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                الكتب المتاحة
                @if($books->total())
                    <span class="text-sm text-gray-500 dark:text-gray-400">({{ $books->total() }})</span>
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
            <p class="text-gray-600 dark:text-gray-400">لا توجد نتائج مطابقة.</p>
        @else
            {{-- شبكة البطاقات --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6" data-loader>
                @foreach($books as $book)
                    <x-book-card :book="$book" />
                @endforeach
            </div>

            {{-- عدّاد + بيجينيشن RTL --}}
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-gray-600 dark:text-gray-400">
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
  <x-page-loader />
</body>

</html>