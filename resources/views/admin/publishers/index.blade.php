@extends('admin.layouts.app')

@section('title', 'الناشرون')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">الناشرون</h1>

    <div class="flex items-center gap-2">
        <a href="{{ route('admin.publishers.create') }}"
           class="px-3 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
            + إضافة ناشر
        </a>
    </div>
</div>

<form method="GET" action="{{ route('admin.publishers.index') }}" class="mb-4">
    <input type="text" name="q" value="{{ $q ?? request('q') }}"
           placeholder="ابحث بالاسم أو الـ slug أو الموقع"
           class="w-full md:w-1/2 rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
</form>

<div class="overflow-x-auto bg-white border rounded">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr class="text-right">
                <th class="px-3 py-2 w-12">#</th>
                <th class="px-3 py-2">الاسم</th>
                <th class="px-3 py-2">slug</th>
                <th class="px-3 py-2">الموقع</th>
                <th class="px-3 py-2 text-center">الكتب</th>
                <th class="px-3 py-2 text-center">إجراءات</th>
            </tr>
        </thead>
        <tbody>
        @forelse($publishers as $publisher)
            <tr class="border-t">
                <td class="px-3 py-2 text-gray-500">{{ $publisher->id }}</td>
                <td class="px-3 py-2 font-medium">{{ $publisher->name }}</td>
                <td class="px-3 py-2 text-gray-700">{{ $publisher->slug }}</td>
                <td class="px-3 py-2">
                    @if($publisher->website)
                        <a href="{{ $publisher->website }}" target="_blank" class="text-indigo-600 hover:underline">
                            {{ Str::limit($publisher->website, 40) }}
                        </a>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-3 py-2 text-center">{{ $publisher->books_count ?? 0 }}</td>
                <td class="px-3 py-2">
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('admin.publishers.edit', $publisher) }}" class="text-blue-600 hover:underline">تعديل</a>
                        <form method="POST" action="{{ route('admin.publishers.destroy', $publisher) }}"
                              onsubmit="return confirm('تأكيد حذف «{{ $publisher->name }}»؟');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">حذف</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-3 py-6 text-center text-gray-500">
                    لا توجد نتائج مطابقة.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $publishers->links() }}
</div>
@endsection
