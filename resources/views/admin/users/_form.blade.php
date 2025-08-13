@php /** @var \App\Models\User $user */ @endphp

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm mb-1">الاسم <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name',$user->name) }}"
               class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
    </div>

    <div>
        <label class="block text-sm mb-1">البريد <span class="text-red-500">*</span></label>
        <input type="email" name="email" value="{{ old('email',$user->email) }}"
               class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
    </div>

    <div>
        <label class="block text-sm mb-1">كلمة المرور (اختياري)</label>
        <input type="password" name="password"
               class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
               placeholder="اتركها فارغة للإبقاء على الحالية">
    </div>

    <div>
        <label class="block text-sm mb-1">تأكيد كلمة المرور</label>
        <input type="password" name="password_confirmation"
               class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm mb-1">الأدوار</label>
        <div class="flex flex-wrap gap-4">
            @foreach($allRoles as $roleName)
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="roles[]"
                           value="{{ $roleName }}"
                           @checked( in_array($roleName, old('roles',$userRoleNames ?? []), true) )
                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span>{{ $roleName }}</span>
                </label>
            @endforeach
        </div>
        <p class="text-xs text-gray-500 mt-1">تنبيه: لا يمكن إزالة «Admin» من آخر مشرف في النظام.</p>
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="mark_verified" value="1"
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span>اعتبار البريد مُوثّقًا (إن لم يكن كذلك)</span>
        </label>
    </div>
</div>
