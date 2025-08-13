@extends('admin.layouts.app')
@section('title', 'تعديل تصنيف')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">تعديل: {{ $category->name }}</h1>
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('admin.categories._form', ['category' => $category])
    </form>
@endsection
