@extends('admin.layouts.app')
@section('title','تعديل مستخدم')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">تعديل مستخدم</h1>
    <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:underline">رجوع</a>
</div>

@if ($errors->any())
    <div class="mb-4 p-3 rounded bg-red-50 text-red-700 text-sm">
        <ul class="list-disc mr-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.users.update',$user) }}" class="bg-white p-4 rounded border space-y-4">
    @csrf @method('PUT')
    @include('admin.users._form', ['user' => $user, 'allRoles' => $allRoles, 'userRoleNames' => $userRoleNames])
    <div class="flex items-center justify-end gap-2">
        <a href="{{ route('admin.users.index') }}" class="px-3 py-2 rounded border text-sm hover:bg-gray-50">إلغاء</a>
        <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700 text-sm">تحديث</button>
    </div>
</form>
@endsection
