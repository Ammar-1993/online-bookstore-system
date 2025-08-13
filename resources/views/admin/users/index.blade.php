@extends('admin.layouts.app')
@section('title','المستخدمون')

@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold">المستخدمون</h1>
</div>

<form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 flex gap-3 items-center">
    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="ابحث بالاسم أو البريد"
           class="w-full md:w-1/2 rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
    <select name="role" class="rounded border-gray-300 text-sm">
        <option value="">كل الأدوار</option>
        @foreach($allRoles as $roleName)
            <option value="{{ $roleName }}" @selected(($roleFilter ?? '') === $roleName)>{{ $roleName }}</option>
        @endforeach
    </select>
    <button class="px-3 py-2 rounded bg-gray-200 hover:bg-gray-300 text-sm">تصفية</button>
</form>

<div class="overflow-x-auto bg-white border rounded">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr class="text-right">
                <th class="px-3 py-2 w-12">#</th>
                <th class="px-3 py-2">الاسم</th>
                <th class="px-3 py-2">البريد</th>
                <th class="px-3 py-2">الحالة</th>
                <th class="px-3 py-2">الأدوار</th>
                <th class="px-3 py-2 text-center">إجراءات</th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $u)
            <tr class="border-t">
                <td class="px-3 py-2 text-gray-500">{{ $u->id }}</td>
                <td class="px-3 py-2 font-medium">{{ $u->name }}</td>
                <td class="px-3 py-2 text-gray-700">{{ $u->email }}</td>
                <td class="px-3 py-2">
                    @if($u->email_verified_at)
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">مُوثّق</span>
                    @else
                        <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">غير موثّق</span>
                    @endif
                </td>
                <td class="px-3 py-2">{{ $u->roles->pluck('name')->join(', ') ?: '—' }}</td>
                <td class="px-3 py-2">
                    <div class="flex items-center justify-center gap-3">
                        <a href="{{ route('admin.users.edit',$u) }}" class="text-blue-600 hover:underline">تعديل</a>
                        <form method="POST" action="{{ route('admin.users.destroy',$u) }}"
                              onsubmit="return confirm('تأكيد حذف «{{ $u->name }}»؟');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">حذف</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">لا توجد نتائج.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
