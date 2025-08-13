@extends('admin.layouts.app')
@section('title', 'إضافة تصنيف')

@section('content')
    <h1 class="text-2xl font-semibold mb-4">إضافة تصنيف</h1>
    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
        @include('admin.categories._form', ['category' => $category])
    </form>
@endsection
