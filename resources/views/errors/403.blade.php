@extends('admin.layouts.app')

@section('title','غير مخوّل')

@section('content')
  <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded p-6 text-center">
    <h1 class="text-2xl font-bold mb-2 text-gray-900 dark:text-gray-100">غير مخوّل</h1>
    <p class="text-gray-600 dark:text-gray-400 mb-4">ليس لديك صلاحية للوصول إلى هذه الصفحة.</p>
    <a href="{{ route('home') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">العودة للواجهة</a>
  </div>
@endsection
