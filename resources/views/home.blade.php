<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Online Bookstore</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900">
    <header class="bg-white border-b">
        <div class="container mx-auto px-4 py-3 flex items-center gap-4 justify-between">
            <a href="{{ route('home') }}" class="font-bold text-xl">متجر الكتب</a>
            <form action="{{ route('home') }}" method="GET" class="flex items-center gap-2">
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
            </nav>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        @if($books->count() === 0)
            <p class="text-gray-600">لا توجد نتائج مطابقة.</p>
        @else
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($books as $book)
                    <x-book-card :book="$book" />
                @endforeach
            </div>

            <div class="mt-6">
                {{ $books->links() }}
            </div>
        @endif
    </main>
</body>

</html>