<!-- @if ($errors->any())
  <div class="mb-3 p-3 bg-rose-50 text-rose-700 rounded">
    <ul class="list-disc pr-5">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif -->


@extends('admin.layouts.app')
@section('title','تعديل كتاب')

@section('content')
  <h1 class="text-xl font-semibold mb-4">تعديل كتاب</h1>

  <form method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
    @csrf @method('PUT')
    @include('admin.books._form', ['book'=>$book,'categories'=>$categories,'publishers'=>$publishers,'authors'=>$authors])
    <div class="mt-4 flex gap-2">
      <button class="px-4 py-2 rounded bg-indigo-600 text-white">تحديث</button>
      <a href="{{ route('admin.books.index') }}" class="px-4 py-2 rounded bg-gray-200">إلغاء</a>
    </div>
  </form>
@endsection
