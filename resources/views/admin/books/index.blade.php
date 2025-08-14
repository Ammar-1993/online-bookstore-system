@extends('admin.layouts.app')
@section('title','الكتب')

<div class="p-4 bg-indigo-100 text-indigo-800 rounded">Tailwind works 🎉</div>


@section('content')
<div class="flex items-center justify-between mb-4">
  <a href="{{ route('admin.books.create') }}" class="px-3 py-2 rounded bg-indigo-600 text-white">+ إضافة كتاب</a>

  <form method="GET" action="{{ route('admin.books.index') }}" class="w-1/2">
    <input name="s" value="{{ request('s') }}" placeholder="ابحث بالعنوان أو ISBN"
           class="w-full rounded border-gray-300">
  </form>
</div>


<div class="overflow-x-auto bg-white rounded shadow">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-50">
      <tr class="text-right">
        <th class="px-3 py-2">الغلاف</th>
        <th class="px-3 py-2">العنوان</th>
        <th class="px-3 py-2">السعر</th>
        <th class="px-3 py-2">المخزون</th>
        <th class="px-3 py-2">التصنيف</th>
        <th class="px-3 py-2">المؤلفون</th>
        <th class="px-3 py-2">الناشر</th>
        <th class="px-3 py-2">إجراءات</th> {{-- <== الجديد --}}
      </tr>
    </thead>
    <tbody>
    @forelse($books as $book)
      <tr class="border-t">
        <td class="px-3 py-2">
          @if($book->cover_image_path)
            <img src="{{ asset('storage/'.$book->cover_image_path) }}" class="h-12 w-12 object-cover rounded">
          @endif
        </td>
        <td class="px-3 py-2">
          <div class="font-medium">{{ $book->title }}</div>
          <div class="text-gray-500 text-xs">{{ $book->isbn }}</div>
        </td>
        <td class="px-3 py-2">{{ $book->currency }} {{ number_format($book->price,2) }}</td>
        <td class="px-3 py-2">{{ $book->stock_qty }}</td>
        <td class="px-3 py-2">{{ optional($book->category)->name }}</td>
        <td class="px-3 py-2">
          {{ $book->authors->pluck('name')->join(', ') }}
        </td>
        <td class="px-3 py-2">{{ optional($book->publisher)->name }}</td>

        <td class="px-3 py-2">
          @can('update', $book)
            <a href="{{ route('admin.books.edit', $book) }}"
               class="px-2 py-1 rounded bg-amber-500 text-blue">تعديل</a>
          @endcan

          @can('delete', $book)
            <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="inline"
                  onsubmit="return confirm('حذف الكتاب نهائيًا؟');">
              @csrf @method('DELETE')
              <button class="px-2 py-1 rounded bg-rose-600 text-red">حذف</button>
            </form>
          @endcan
        </td>
      </tr>
    @empty
      <tr><td class="px-3 py-4 text-center text-gray-500" colspan="8">لا توجد بيانات</td></tr>
    @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $books->withQueryString()->links() }}</div>
@endsection
