<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>شكراً لطلبك</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
<header class="bg-white border-b">
  <div class="container mx-auto px-4 py-3 flex items-center justify-between">
    <a href="{{ route('home') }}" class="font-bold text-xl">المتجر الإلكتروني للكتب</a>
  </div>
</header>

<main class="container mx-auto px-4 py-12 text-center">
  <x-flash-stack />
  <h1 class="text-2xl font-bold mb-2">تم استلام طلبك بنجاح</h1>

  @if(!empty($order_no))
    <p class="text-gray-700">رقم الطلب: <strong>{{ $order_no }}</strong></p>
  @else
    {{-- في حال تمت إعادة تحميل الصفحة وفقدت قيمة الـ session --}}
    <p class="text-gray-600">تم إنشاء طلبك بنجاح. يمكنك الاطّلاع عليه من صفحة "مشترياتي" لاحقًا.</p>
  @endif

  <a href="{{ route('home') }}" class="mt-6 inline-block px-5 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
    العودة للتسوق
  </a>
</main>
</body>
</html>
