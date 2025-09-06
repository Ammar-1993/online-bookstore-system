{{-- resources/views/account/partials/profile-modal.blade.php --}}
@php
  $user = auth()->user();
@endphp

<div id="profileModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
  {{-- الخلفية --}}
  <div class="absolute inset-0 bg-black/50" data-close-profile></div>

  <div
    class="relative mx-auto my-10 w-[95%] max-w-2xl rounded-2xl bg-white dark:bg-gray-900 shadow-lg ring-1 ring-black/5 dark:ring-white/10">
    {{-- رأس المودال --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-black/5 dark:border-white/10">
      <div class="font-semibold text-gray-900 dark:text-gray-100">تحديث الملف الشخصي</div>
      <button type="button" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-200" data-close-profile aria-label="إغلاق">
        ✕
      </button>
    </div>

    <div class="p-5 space-y-8">
      {{-- 1) معلومات الحساب --}}
      <form method="POST" action="{{ route('user-profile-information.update') }}" class="space-y-4">
        @csrf @method('PUT')

        <div class="grid gap-3 sm:grid-cols-2">
          <div>
            <label class="block text-sm mb-1">الاسم</label>
            <input name="name" value="{{ old('name', $user?->name) }}"
                   class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900" required>
          </div>

          <div>
            <label class="block text-sm mb-1">البريد الإلكتروني</label>
            <input type="email" name="email" value="{{ old('email', $user?->email) }}"
                   class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900" required>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700" data-ripple>
            حفظ التغييرات
          </button>

          <a href="{{ route('account.index') }}"
             class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700">
            لوحة حسابي
          </a>

          <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button class="px-4 py-2 rounded-xl bg-rose-50 text-rose-700 ring-1 ring-rose-600/20 hover:bg-rose-100">
              تسجيل الخروج
            </button>
          </form>
        </div>

        @error('name')   <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
        @error('email')  <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
      </form>

      <hr class="border-black/5 dark:border-white/10">

      {{-- 2) تغيير كلمة المرور --}}
      <form method="POST" action="{{ route('user-password.update') }}" class="space-y-4">
        @csrf @method('PUT')

        <div class="grid gap-3 sm:grid-cols-3">
          <div>
            <label class="block text-sm mb-1">كلمة المرور الحالية</label>
            <input type="password" name="current_password"
                   class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900" autocomplete="current-password" required>
          </div>

          <div>
            <label class="block text-sm mb-1">كلمة المرور الجديدة</label>
            <input type="password" name="password"
                   class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900" autocomplete="new-password" required>
          </div>

          <div>
            <label class="block text-sm mb-1">تأكيد كلمة المرور</label>
            <input type="password" name="password_confirmation"
                   class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900" autocomplete="new-password" required>
          </div>
        </div>

        <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700" data-ripple>
          تغيير كلمة المرور
        </button>

        @error('current_password') <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
        @error('password')         <div class="text-sm text-rose-600">{{ $message }}</div> @enderror
      </form>
    </div>
  </div>
</div>

{{-- سكربت فتح/إغلاق المودال (Vanilla JS) --}}
<script>
  (() => {
    const modal   = document.getElementById('profileModal');
    const openers = document.querySelectorAll('[data-open-profile]');
    const closeAll = () => modal?.classList.add('hidden');

    openers.forEach(btn => btn.addEventListener('click', () => modal?.classList.remove('hidden')));
    modal?.querySelectorAll('[data-close-profile]')?.forEach(x => x.addEventListener('click', closeAll));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAll(); });
  })();
</script>
