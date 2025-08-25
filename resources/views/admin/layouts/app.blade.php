<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'لوحة التحكم') - Online Bookstore</title>


  
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900">

  {{-- شريط علوي --}}
  <header class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <a href="{{ route('admin.dashboard') }}" class="font-bold">لوحة التحكم</a>



      <nav class="text-sm hidden md:flex gap-4">
        <a href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
        @role('Admin')
        <a href="{{ route('admin.categories.index') }}">التصنيفات</a>
        <a href="{{ route('admin.publishers.index') }}">الناشرون</a>
        <a href="{{ route('admin.authors.index') }}">المؤلفون</a>
        <a href="{{ route('admin.users.index') }}">المستخدمون</a>
        <a href="{{ route('admin.reviews.index') }}">المراجعات</a>
        <a href="{{ route('admin.orders.index') }}">الطلبات</a>

        @role('Seller')
        <a href="{{ route('admin.reviews.index') }}">مراجعات كتبي</a>
        @endrole

        @endrole




        {{-- يظهر للجميع داخل لوحة التحكم لكن يختلف التحكم بالكتاب عبر Policy --}}
        <a href="{{ route('admin.books.index') }}">الكتب</a>
      </nav>


      <div class="flex items-center gap-3">
        <a href="{{ route('home') }}" class="text-sm">الواجهة</a>
        <form method="POST" action="{{ route('logout') }}">@csrf
          <button class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 text-sm">خروج</button>
        </form>
      </div>
    </div>
  </header>

  {{-- محتوى الصفحة --}}
  <main class="max-w-7xl mx-auto px-4 py-6">
    {{-- ⚠️ عنصر فلاش واحد فقط هنا --}}
    <x-flash-stack />

    @yield('content')
  </main>

    {{-- داخل الـ body قبل إغلاقه مثلاً --}}
    <x-page-loader />

</body>

</html>