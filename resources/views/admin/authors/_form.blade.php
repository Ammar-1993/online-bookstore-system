@php
    /** @var \App\Models\Author $author */
    // كلاس موحّد لكل الحقول: أبيض/أسود + حدود واضحة + فوكس أزرق
    $ctrl = 'w-full rounded-xl bg-white text-black placeholder-gray-500
             ring-1 ring-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
             dark:bg-white dark:text-black';
@endphp

<div class="grid md:grid-cols-2 gap-4">
    {{-- الاسم --}}
    <div>
        <label for="name" class="block text-sm mb-1 text-gray-900 dark:text-white">
            الاسم <span class="text-rose-500">*</span>
        </label>
        <input id="name" type="text" name="name" value="{{ old('name',$author->name) }}"
               class="{{ $ctrl }} text-sm" required>
        @error('name') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- الـ Slug --}}
    <div>
        <label for="slug" class="block text-sm mb-1 text-gray-900 dark:text-white">Slug (اختياري)</label>
        <input id="slug" type="text" name="slug" value="{{ old('slug',$author->slug) }}"
               class="{{ $ctrl }} text-sm" placeholder="يُولّد تلقائيًا إن تُرك فارغًا">
        @error('slug') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- الموقع --}}
    <div class="md:col-span-2">
        <label for="website" class="block text-sm mb-1 text-gray-900 dark:text-white">الموقع (اختياري)</label>
        <input id="website" type="url" name="website" value="{{ old('website',$author->website) }}"
               class="{{ $ctrl }} text-sm" dir="ltr" placeholder="https://example.com">
        @error('website') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- النبذة --}}
    <div class="md:col-span-2">
        <label for="bio" class="block text-sm mb-1 text-gray-900 dark:text-white">نبذة (اختياري)</label>
        <textarea id="bio" name="bio" rows="4"
                  class="{{ $ctrl }} text-sm min-h-[120px]"
                  placeholder="نبذة مختصرة عن المؤلف">{{ old('bio',$author->bio) }}</textarea>
        @error('bio') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- الصورة --}}
    <div class="md:col-span-2">
        <label for="avatar" class="block text-sm mb-1 text-gray-900 dark:text-white">صورة المؤلف (اختياري)</label>

        <input id="avatar" type="file" name="avatar" accept="image/*"
               class="block text-sm {{ $ctrl }}
                      file:me-3 file:px-3 file:py-1.5 file:rounded-lg
                      file:border-0 file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">

        <div class="mt-2 flex items-center gap-3">
            @if($author->avatar_path)
                <img src="{{ asset('storage/'.$author->avatar_path) }}" class="h-16 w-16 rounded-full border" alt="Avatar">
            @endif
            <img id="avatar_preview" class="h-16 w-16 rounded-full border hidden" alt="معاينة">
        </div>
        @error('avatar') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>
</div>

{{-- شريط الأزرار السفلي (مثبّت وأسفل البطاقة) --}}
<div class="mt-6 -mx-5 -mb-5 border-t border-gray-100 dark:border-white/10
            sticky bottom-0 bg-white/90 dark:bg-gray-900/90 backdrop-blur
            rounded-b-2xl px-5 py-4 flex flex-col sm:flex-row items-center gap-2 justify-end">

    <a href="{{ route('admin.authors.index') }}"
       class="w-full sm:w-auto px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200
              dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100"
       data-ripple>
        إلغاء
    </a>

    <button type="submit"
            class="w-full sm:w-auto px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow"
            data-ripple data-loader>
        {{ $submitLabel ?? 'حفظ' }}
    </button>
</div>

{{-- سكرِبتات صغيرة: توليد slug + معاينة الصورة --}}
<script>
(() => {
  // توليد slug تلقائيًا من الاسم إن كان slug فارغًا
  const nameEl = document.getElementById('name');
  const slugEl = document.getElementById('slug');
  const autoSlug = () => {
    if (!nameEl || !slugEl) return;
    if (slugEl.value.trim() !== '') return;
    const s = nameEl.value.normalize('NFKD')
      .replace(/[\u064B-\u065F]/g, '')         // إزالة التشكيل
      .replace(/[^\p{L}\p{N}\s-]/gu, '')       // أحرف/أرقام/مسافات
      .trim().toLowerCase()
      .replace(/\s+/g,'-').replace(/-+/g,'-'); // مسافات → شرطات
    slugEl.value = s || '';
  };
  nameEl?.addEventListener('blur', autoSlug, { passive: true });

  // معاينة الصورة قبل الرفع
  const file = document.getElementById('avatar');
  const preview = document.getElementById('avatar_preview');
  file?.addEventListener('change', () => {
    const f = file.files?.[0];
    if (!f) { preview.classList.add('hidden'); preview.src=''; return; }
    preview.src = URL.createObjectURL(f);
    preview.classList.remove('hidden');
  });
})();
</script>
