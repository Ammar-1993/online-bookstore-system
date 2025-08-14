@extends('admin.layouts.app')

@section('title','المستخدمون')

@section('content')
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">المستخدمون</h1>
    <a href="{{ route('admin.dashboard') }}" class="text-sm underline">رجوع للوحة</a>
  </div>

  <form method="get" class="mb-4 flex gap-2">
    <input type="text" name="q" value="{{ $q }}" placeholder="ابحث بالاسم أو البريد"
           class="border rounded px-3 py-2 w-full max-w-sm">
    <select name="role" class="border rounded px-3 py-2">
      <option value="">كل الأدوار</option>
      @foreach($roles as $r)
        <option value="{{ $r }}" @selected($role===$r)>{{ $r }}</option>
      @endforeach
    </select>
    <button class="px-4 py-2 bg-indigo-600 text-white rounded">بحث</button>
  </form>

  <div class="bg-white rounded shadow overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-right">#</th>
          <th class="px-4 py-2 text-right">الاسم</th>
          <th class="px-4 py-2 text-right">البريد</th>
          <th class="px-4 py-2 text-right">الأدوار</th>
          <th class="px-4 py-2 text-right">كتب البائع</th>
          <th class="px-4 py-2 text-right">إجراءات</th>
        </tr>
      </thead>
      <tbody>
      @forelse($users as $u)
        <tr class="border-t">
          <td class="px-4 py-2">{{ $u->id }}</td>
          <td class="px-4 py-2">{{ $u->name }}</td>
          <td class="px-4 py-2">{{ $u->email }}</td>
          <td class="px-4 py-2">
            {{ $u->roles->pluck('name')->implode(', ') ?: '—' }}
          </td>
          <td class="px-4 py-2">{{ $u->books_count }}</td>
          <td class="px-4 py-2 flex gap-2">
            <a class="text-blue-600 hover:underline" href="{{ route('admin.users.edit',$u) }}">تعديل</a>
            <form method="post" action="{{ route('admin.users.destroy',$u) }}"
                  onsubmit="return confirm('تأكيد الحذف؟');">
              @csrf @method('delete')
              <button class="text-red-600 hover:underline">حذف</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">لا يوجد نتائج.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $users->links() }}</div>
@endsection
