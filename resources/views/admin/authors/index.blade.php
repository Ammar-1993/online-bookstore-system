@extends('admin.layouts.app')
@section('title','المؤلفون')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">المؤلفون</h1>
    <a href="{{ route('admin.authors.create') }}"
       class="px-3 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700 text-sm">+ إضافة مؤلف</a>
</div>

<form method="GET" action="{{ route('admin.authors.index') }}" class="mb-4">
    <input type="text" name="q" value="{{ $q ?? request('q') }}"
           placeholder="ابحث بالاسم أو الـ slug"
           class="w-full md:w-1/2 rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
</form>

<div class="overflow-x-auto bg-white border rounded">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr class="text-right">
                <th class="px-3 py-2 w-12">#</th>
                <th class="px-3 py-2">الاسم</th>
                <th class="px-3 py-2">slug</th>
                <th class="px-3 py-2 text-center">الكتب</th>
                <th class="px-3 py-2 text-center">إجراءات</th>
            </tr>
        </thead>
        <tbody>
        @forelse($authors as $author)
            <tr class="border-t">
                <td class="px-3 py-2 text-gray-500">{{ $author->id }}</td>
                <td class="px-3 py-2 font-medium flex items-center gap-2">
                    @if($author->avatar_path)
                        <img src="{{ asset('storage/'.$author->avatar_path) }}" class="h-8 w-8 rounded-full border" alt="">
                    @endif
                    {{ $author->name }}
                </td>
                <td class="px-3 py-2 text-gray-700">{{ $author->slug }}</td>
                <td class="px-3 py-2 text-center">{{ $author->books_count ?? 0 }}</td>
                <td class="px-3 py-2">
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('admin.authors.edit',$author) }}" class="text-blue-600 hover:underline">تعديل</a>
                        <form method="POST" action="{{ route('admin.authors.destroy',$author) }}"
                              onsubmit="return confirm('تأكيد حذف «{{ $author->name }}»؟');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">حذف</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-3 py-6 text-center text-gray-500">لا توجد نتائج مطابقة.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $authors->links() }}
</div>
@endsection
