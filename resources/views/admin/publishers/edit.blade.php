@extends('admin.layouts.app')

@section('title', 'تعديل ناشر')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">تعديل ناشر</h1>
    <a href="{{ route('admin.publishers.index') }}" class="text-sm text-gray-600 hover:underline">رجوع</a>
</div>

@if ($errors->any())
    <div class="mb-4 p-3 rounded bg-red-50 text-red-700 text-sm">
        <ul class="list-disc mr-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.publishers.update', $publisher) }}" enctype="multipart/form-data" class="bg-white p-4 rounded border space-y-4">
    @csrf
    @method('PUT')
    @include('admin.publishers._form', ['publisher' => $publisher, 'submitLabel' => 'تحديث'])
</form>
@endsection
