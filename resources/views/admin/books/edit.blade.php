{{-- resources/views/admin/books/edit.blade.php --}}
@extends('admin.layouts.app')
@section('title','تعديل كتاب')

@section('content')
<div class="space-y-6">

  {{-- رأس الصفحة --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 grid place-items-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-200/70">
        {{-- edit/pencil icon --}}
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M4 21h4l11-11a2.828 2.828 0 1 0-4-4L4 17v4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div>
        <h1 class="text-2xl font-display font-semibold text-gray-900 dark:text-black-100">تعديل كتاب</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          حدّث بيانات «{{ \Illuminate\Support\Str::limit($book->title, 40, '…') }}».
        </p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.books.index') }}"
         class="inline-flex items-center gap-2 rounded-xl px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100"
         data-ripple>
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        رجوع
      </a>
    </div>
  </div>

  {{-- ملخص الأخطاء --}}
  @if ($errors->any())
    <div class="rounded-xl p-3 bg-rose-50 text-rose-800 ring-1 ring-rose-200/70 dark:bg-rose-500/10 dark:text-rose-200">
      <div class="font-medium mb-1">تأكد من الحقول التالية:</div>
      <ul class="list-disc pr-5 text-sm space-y-0.5">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- النموذج --}}
  <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data"
        class="bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 p-5 md:p-6">
    @csrf
    @method('PUT')

    {{-- تأكد أنك حدّثت labels داخل _form إلى: text-gray-900 dark:text-white --}}
    @include('admin.books._form', [
      'book'       => $book,
      'categories' => $categories,
      'publishers' => $publishers,
      'authors'    => $authors,
    ])

    {{-- شريط إجراءات لاصق ومحاذاته لليسار (RTL = justify-end) --}}
    <div class="mt-6 -mx-5 -mb-5 border-t border-gray-100 dark:border-white/10 sticky bottom-0 bg-white/90 dark:bg-gray-900/90 backdrop-blur rounded-b-2xl px-5 py-4 flex flex-col sm:flex-row items-center gap-2 justify-end">
      <button type="submit"
              class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm"
              data-ripple data-loader>
        <svg class="w-5 h-5 motion-safe:animate-spin opacity-90 hidden" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" opacity=".25"/>
          <path d="M21 12a9 9 0 0 1-9 9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span>تحديث</span>
      </button>

      <a href="{{ route('admin.books.index') }}"
         class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100"
         data-ripple>
        إلغاء
      </a>
    </div>
  </form>

</div>
@endsection
