<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>الدفع</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
<header class="bg-white border-b">
  <div class="container mx-auto px-4 py-3 flex items-center justify-between">
    <a href="{{ route('home') }}" class="font-bold text-xl">متجر الكتب</a>
    <nav class="flex gap-3">
      <a class="hover:text-indigo-600" href="{{ route('cart.index') }}">السلة</a>
    </nav>
  </div>
</header>

<main class="container mx-auto px-4 py-8">
  <x-flash-stack />

  <div class="grid lg:grid-cols-3 gap-6">
    <section class="lg:col-span-2 bg-white border rounded p-4">
      <h1 class="text-xl font-semibold mb-4">معلومات العميل</h1>
      <form method="POST" action="{{ route('checkout.store') }}" class="space-y-4">
        @csrf
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm mb-1">الاسم الكامل</label>
            <input name="customer_name" value="{{ old('customer_name', $prefill['customer_name']) }}" class="w-full border rounded px-3 py-2">
            @error('customer_name') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
          </div>
          <div>
            <label class="block text-sm mb-1">البريد الإلكتروني</label>
            <input type="email" name="customer_email" value="{{ old('customer_email', $prefill['customer_email']) }}" class="w-full border rounded px-3 py-2">
            @error('customer_email') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
          </div>
          <div>
            <label class="block text-sm mb-1">رقم الهاتف</label>
            <input name="customer_phone" value="{{ old('customer_phone', $prefill['customer_phone']) }}" class="w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm mb-1">الدولة</label>
            <input name="country" value="{{ old('country', $prefill['country']) }}" class="w-full border rounded px-3 py-2">
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm mb-1">العنوان</label>
            <input name="address_line1" value="{{ old('address_line1', $prefill['address_line1']) }}" class="w-full border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm mb-1">المدينة</label>
            <input name="city" value="{{ old('city', $prefill['city']) }}" class="w-full border rounded px-3 py-2">
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm mb-1">ملاحظات</label>
            <textarea name="notes" rows="3" class="w-full border rounded px-3 py-2">{{ old('notes', $prefill['notes']) }}</textarea>
          </div>
        </div>

        <div class="mt-4">
          <button class="px-5 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">تأكيد الطلب</button>
        </div>
      </form>
    </section>

    <aside class="bg-white border rounded p-4 h-fit">
      <h2 class="font-semibold mb-3">ملخص الطلب</h2>
      <div class="space-y-2 text-sm">
        @foreach($items as $it)
          <div class="flex items-center justify-between">
            <span class="truncate">{{ $it->book->title }} × {{ $it->qty }}</span>
            <span>{{ number_format($it->price * $it->qty,2) }} {{ $currency }}</span>
          </div>
        @endforeach
        <hr>
        <div class="flex justify-between">
          <span>المجموع</span>
          <span>{{ number_format($subtotal,2) }} {{ $currency }}</span>
        </div>
        <div class="flex justify-between">
          <span>الشحن</span>
          <span>0.00 {{ $currency }}</span>
        </div>
        <div class="flex justify-between font-semibold">
          <span>الإجمالي</span>
          <span>{{ number_format($total,2) }} {{ $currency }}</span>
        </div>
      </div>
    </aside>
  </div>
</main>
</body>
</html>
