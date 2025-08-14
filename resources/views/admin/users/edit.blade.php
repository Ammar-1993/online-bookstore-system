@extends('admin.layouts.app')

@section('title','تعديل مستخدم')

@section('content')
  <h1 class="text-xl font-semibold mb-4">تعديل المستخدم: {{ $user->name }}</h1>

  <form method="post" action="{{ route('admin.users.update',$user) }}" class="bg-white p-4 rounded shadow max-w-2xl">
    @csrf @method('put')

    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm mb-1">الاسم</label>
        <input name="name" value="{{ old('name',$user->name) }}" class="w-full border rounded px-3 py-2">
        @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm mb-1">البريد</label>
        <input name="email" value="{{ old('email',$user->email) }}" class="w-full border rounded px-3 py-2">
        @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm mb-1">كلمة المرور (اختياري)</label>
        <input type="password" name="password" class="w-full border rounded px-3 py-2" autocomplete="new-password">
        @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm mb-1">تأكيد كلمة المرور</label>
        <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2">
      </div>
    </div>

    <div class="mt-4">
      <p class="text-sm mb-2 font-medium">الأدوار:</p>
      <div class="flex gap-4">
        @foreach($roles as $r)
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="roles[]" value="{{ $r }}"
                   @checked(in_array($r, old('roles', $user->roles->pluck('name')->all())))>
            <span>{{ $r }}</span>
          </label>
        @endforeach
      </div>
      @error('roles') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="mt-6 flex gap-2">
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">حفظ</button>
      <a class="px-4 py-2 bg-gray-200 rounded" href="{{ route('admin.users.index') }}">إلغاء</a>
    </div>
  </form>
@endsection
