{{-- resources/views/admin/categories/edit.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'تعديل تصنيف')

@section('content')
  <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-4">تعديل: {{ $category->name }}</h1>
  <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data"
        class="bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 p-5">
    @method('PUT')
    @include('admin.categories._form', ['category' => $category])
  </form>
@endsection
