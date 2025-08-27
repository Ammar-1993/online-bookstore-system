@extends('admin.layouts.app')
@section('title', 'إضافة تصنيف')

@section('content')
  <h1 class="text-2xl font-semibold mb-4">إضافة تصنيف</h1>

  <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data"
        class="bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 p-5">
    @include('admin.categories._form', ['category' => $category])
  </form>
@endsection
