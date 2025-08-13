@extends('admin.layouts.app')
@section('title','تعديل مؤلف')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">تعديل مؤلف</h1>
    <a href="{{ route('admin.authors.index') }}" class="text-sm text-gray-600 hover:underline">رجوع</a>
</div>

@if ($errors->any())
    <div class="mb-4 p-3 rounded bg-red-50 text-red-700 text-sm">
        <ul class="list-disc mr-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.authors.update',$author) }}" enctype="multipart/form-data"
      class="bg-white p-4 rounded border space-y-4">
    @csrf @method('PUT')
    @include('admin.authors._form',['author'=>$author,'submitLabel'=>'تحديث'])
</form>
@endsection
