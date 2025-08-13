@extends('admin.layouts.app')

@section('title', 'التصنيفات')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">التصنيفات</h1>

        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('admin.categories.index') }}">
                <input name="q" value="{{ $q }}"
                       class="rounded-md border-gray-300 text-sm"
                       placeholder="ابحث بالاسم أو الـ slug"/>
            </form>

            <a href="{{ route('admin.categories.create') }}"
               class="px-3 py-2 bg-indigo-600 text-white rounded-md text-sm">+ إضافة تصنيف</a>
        </div>
    </div>

    

    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="px-4 py-2 text-right">#</th>
                    <th class="px-4 py-2 text-right">الاسم</th>
                    <th class="px-4 py-2 text-right">slug</th>
                    <th class="px-4 py-2 text-right">الكتب</th>
                    <th class="px-4 py-2 text-right">إجراءات</th>
                </tr>
            </thead>
            <tbody>
            @forelse($categories as $cat)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $cat->id }}</td>
                    <td class="px-4 py-2">{{ $cat->name }}</td>
                    <td class="px-4 py-2 text-gray-500">{{ $cat->slug }}</td>
                    <td class="px-4 py-2">{{ $cat->books_count }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.categories.edit', $cat) }}"
                           class="px-2 py-1 text-blue-700 hover:underline">تعديل</a>

                        <form action="{{ route('admin.categories.destroy', $cat) }}"
                              method="POST" class="inline"
                              onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                            @csrf @method('DELETE')
                            <button class="px-2 py-1 text-red-700 hover:underline">حذف</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">لا توجد تصنيفات.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
@endsection
