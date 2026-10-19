<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>شكراً لطلبك</title>
  @include('partials.theme-init')
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 antialiased">
<header class="sticky top-0 z-40 backdrop-blur bg-white/90 dark:bg-gray-900/80 border-b border-black/5 dark:border-white/10">
  <div class="container mx-auto px-4 py-3 flex items-center justify-between">
    <a href="{{ route('home') }}" class="font-bold text-xl text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition">المتجر الإلكتروني للكتب</a>
    @include('partials.theme-toggle-button')
  </div>
</header>

<main class="container mx-auto px-4 py-12 text-center">
  <x-flash-stack />
  <div class="max-w-lg mx-auto bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 p-8">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">تم استلام طلبك بنجاح</h1>

    @if(!empty($order_no))
      <p class="text-gray-700 dark:text-gray-300">رقم الطلب: <strong>{{ $order_no }}</strong></p>
    @else
      {{-- في حال تمت إعادة تحميل الصفحة وفقدت قيمة الـ session --}}
      <p class="text-gray-600 dark:text-gray-400">تم إنشاء طلبك بنجاح. يمكنك الاطّلاع عليه من صفحة "مشترياتي" لاحقًا.</p>
    @endif

    <a href="{{ route('home') }}" class="mt-6 inline-block px-5 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition">
      العودة للتسوق
    </a>
  </div>
</main>
</body>
</html>
