@extends('admin.layouts.app')
@section('title','التصنيفات')

@section('content')
<div class="flex items-center justify-between mb-4">
  <a href="{{ route('admin.categories.create') }}" class="px-3 py-2 rounded bg-indigo-600 text-white">+ إضافة تصنيف</a>
  <form method="GET" class="w-1/2">
    <input name="s" value="{{ request('s') }}" placeholder="بحث..." class="w-full rounded border-gray-300">
  </form>
</div>

@if(session('success')) <div class="mb-2 p-2 bg-green-50 text-green-700 rounded">{{ session('success') }}</div> @endif

<div class="overflow-x-auto bg-white rounded shadow">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-50"><tr><th class="px-3 py-2">الاسم</th><th class="px-3 py-2">Slug</th><th class="px-3 py-2">إجراءات</th></tr></thead>
    <tbody>
    @foreach($categories as $c)
      <tr class="border-t">
        <td class="px-3 py-2">{{ $c->name }}</td>
        <td class="px-3 py-2 text-gray-600">{{ $c->slug }}</td>
        <td class="px-3 py-2">
          <a class="px-2 py-1 bg-amber-500 text-white rounded" href="{{ route('admin.categories.edit',$c) }}">تعديل</a>
          <form action="{{ route('admin.categories.destroy',$c) }}" method="POST" class="inline" onsubmit="return confirm('حذف؟');">
            @csrf @method('DELETE')
            <button class="px-2 py-1 bg-rose-600 text-white rounded">حذف</button>
          </form>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $categories->withQueryString()->links() }}</div>
@endsection
