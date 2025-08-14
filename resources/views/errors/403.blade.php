@extends('admin.layouts.app')

@section('title','غير مخوّل')

@section('content')
  <div class="bg-white border rounded p-6 text-center">
    <h1 class="text-2xl font-bold mb-2">غير مخوّل</h1>
    <p class="text-gray-600 mb-4">ليس لديك صلاحية للوصول إلى هذه الصفحة.</p>
    <a href="{{ route('home') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">العودة للواجهة</a>
  </div>
@endsection
