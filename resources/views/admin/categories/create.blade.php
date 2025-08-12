@extends('admin.layouts.app')
@section('title','إضافة تصنيف')

@section('content')
<a href="{{ route('admin.categories.index') }}" class="px-3 py-2 rounded bg-gray-200">← رجوع</a>
@if ($errors->any())
  <div class="mb-3 p-3 bg-rose-50 text-rose-700 rounded">
    <ul class="list-disc pr-5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
@endif

<form method="POST" action="{{ route('admin.categories.store') }}" class="mt-4 space-y-3">
  @csrf
  <input name="name" class="w-full rounded border-gray-300" placeholder="الاسم" value="{{ old('name') }}">
  <input name="slug" class="w-full rounded border-gray-300" placeholder="Slug (اختياري)" value="{{ old('slug') }}">
  <button class="px-4 py-2 bg-indigo-600 text-white rounded">حفظ</button>
</form>
@endsection
