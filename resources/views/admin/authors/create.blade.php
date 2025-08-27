@extends('admin.layouts.app')
@section('title','إضافة مؤلف')

@section('content')
<div class="space-y-4">

  {{-- رأس الصفحة --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-display font-semibold text-gray-900 dark:text-black-100">
        إضافة مؤلف
      </h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
        أدخل بيانات المؤلف ثم اضغط «حفظ».
      </p>
    </div>

    <a href="{{ route('admin.authors.index') }}"
       class="inline-flex items-center rounded-xl px-3 py-2 bg-gray-100 hover:bg-gray-200
              dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100"
       data-ripple>
      رجوع
    </a>
  </div>

  {{-- أخطاء التحقق --}}
  @if ($errors->any())
    <div role="alert"
         class="rounded-2xl px-4 py-3 bg-rose-50 text-rose-800 ring-1 ring-rose-600/20
                dark:bg-rose-500/10 dark:text-rose-200">
      <ul class="list-disc me-5 grid gap-1 text-sm">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- بطاقة النموذج (p-5 مهم لمواءمة شريط الأزرار داخل الـ _form) --}}
  <form method="POST" action="{{ route('admin.authors.store') }}" enctype="multipart/form-data"
        class="bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 p-5">
    @csrf

    @include('admin.authors._form', [
      'author'      => $author,
      'submitLabel' => 'حفظ',
    ])
  </form>
</div>
@endsection
