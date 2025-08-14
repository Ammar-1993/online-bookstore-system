@extends('admin.layouts.app')

@section('title', 'المراجعات')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">المراجعات</h1>
    <form method="GET" class="flex items-center gap-2">
      <input type="text" name="q" value="{{ $q }}" class="rounded border-gray-300" placeholder="ابحث بالكتاب أو المستخدم">
      <select name="status" class="rounded border-gray-300">
        <option value="">الكل</option>
        <option value="approved" @selected($filter==='approved')>المقبولة</option>
        <option value="pending"  @selected($filter==='pending')>قيد المراجعة</option>
      </select>
      <button class="px-3 py-1 bg-gray-200 rounded">بحث</button>
    </form>
  </div>

  <div class="overflow-x-auto bg-white rounded border">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-2 text-right">المستخدم</th>
          <th class="px-3 py-2 text-right">الكتاب</th>
          <th class="px-3 py-2 text-right">التقييم</th>
          <th class="px-3 py-2 text-right">الحالة</th>
          <th class="px-3 py-2 text-right">إجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($reviews as $r)
          <tr class="border-t">
            <td class="px-3 py-2">{{ $r->user->name }}</td>
            <td class="px-3 py-2">
              <a class="text-indigo-600 hover:underline" href="{{ route('books.show', $r->book->slug) }}" target="_blank">
                {{ $r->book->title }}
              </a>
            </td>
            <td class="px-3 py-2">
              <div class="flex">
                @for($i=1;$i<=5;$i++)
                  <svg class="w-4 h-4 {{ $i <= $r->rating ? 'text-yellow-400' : 'text-gray-300' }}" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.802 2.036a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.802-2.036a1 1 0 00-1.175 0L6.66 16.283c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L3.025 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.024-3.293z"/></svg>
                @endfor
              </div>
            </td>
            <td class="px-3 py-2">
              @if($r->approved)
                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">مقبولة</span>
              @else
                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded">قيد المراجعة</span>
              @endif
            </td>
            <td class="px-3 py-2 flex gap-2">
              <form method="POST" action="{{ route('admin.reviews.toggle', $r) }}">
                @csrf @method('PATCH')
                <button class="text-indigo-600 hover:underline">{{ $r->approved ? 'تعطيل' : 'اعتماد' }}</button>
              </form>
              <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}">
                @csrf @method('DELETE')
                <button class="text-red-600 hover:underline">حذف</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-3 py-6 text-center text-gray-500">لا توجد مراجعات.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $reviews->links() }}</div>
@endsection
