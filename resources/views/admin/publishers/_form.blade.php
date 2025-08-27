@php
    /** @var \App\Models\Publisher $publisher */
    // كلاس موحّد لكل الحقول: أبيض/أسود + حدود واضحة + فوكس أزرق
    $ctrl = 'w-full rounded-xl bg-white text-black placeholder-gray-500
             ring-1 ring-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
             dark:bg-white dark:text-black';
@endphp

<div class="grid md:grid-cols-2 gap-4">
    {{-- الاسم --}}
    <div>
        <label class="block text-sm mb-1 text-gray-900 dark:text-white">
            الاسم <span class="text-rose-500">*</span>
        </label>
        <input type="text" name="name" value="{{ old('name', $publisher->name) }}"
               class="{{ $ctrl }}" required>
        @error('name') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- الـ Slug --}}
    <div>
        <label class="block text-sm mb-1 text-gray-900 dark:text-white">Slug (اختياري)</label>
        <input type="text" name="slug" value="{{ old('slug', $publisher->slug) }}"
               class="{{ $ctrl }}" placeholder="يُولّد تلقائيًا إن تُرك فارغًا">
        @error('slug') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- الموقع --}}
    <div class="md:col-span-2">
        <label class="block text-sm mb-1 text-gray-900 dark:text-white">الموقع (اختياري)</label>
        <input type="url" name="website" value="{{ old('website', $publisher->website) }}"
               class="{{ $ctrl }} dir-ltr" dir="ltr" placeholder="https://example.com">
        @error('website') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- الشعار --}}
    <div class="md:col-span-2">
        <label class="block text-sm mb-1 text-gray-900 dark:text-white">شعار الناشر (اختياري)</label>

        <input type="file" id="logo" name="logo" accept="image/*"
               class="block text-sm {{ $ctrl }} file:me-3 file:px-3 file:py-1.5 file:rounded-lg
                      file:border-0 file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">

        <div class="mt-2 flex items-center gap-3">
            @if(!empty($publisher->logo_path))
                <img src="{{ asset('storage/'.$publisher->logo_path) }}" alt="Logo" class="h-16 rounded border">
            @endif
            <img id="logo_preview" class="h-16 rounded border hidden" alt="معاينة الشعار">
        </div>
        @error('logo') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>
</div>

{{-- شريط الأزرار السفلي (ثابت وأسفل الصفحة) --}}
<div class="mt-6 -mx-5 -mb-5 border-t border-gray-100 dark:border-white/10
            sticky bottom-0 bg-white/90 dark:bg-gray-900/90 backdrop-blur
            rounded-b-2xl px-5 py-4 flex flex-col sm:flex-row items-center gap-2 justify-end">

    <a href="{{ route('admin.publishers.index') }}"
       class="w-full sm:w-auto px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200
              dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100"
       data-ripple>إلغاء</a>

    <button type="submit"
            class="w-full sm:w-auto px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow"
            data-ripple data-loader>
        {{ $submitLabel ?? 'حفظ' }}
    </button>
</div>

{{-- سكرِبتات صغيرة: توليد slug + معاينة الشعار --}}
<script>
(() => {
  // توليد slug تلقائي من الاسم إن كان slug فارغًا
  const nameEl = document.querySelector('input[name="name"]');
  const slugEl = document.querySelector('input[name="slug"]');
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
  nameEl?.addEventListener('blur', autoSlug, { passive:true });

  // معاينة شعار الناشر قبل الرفع
  const file = document.getElementById('logo');
  const preview = document.getElementById('logo_preview');
  file?.addEventListener('change', () => {
    const f = file.files?.[0];
    if (!f) { preview.classList.add('hidden'); preview.src=''; return; }
    preview.src = URL.createObjectURL(f);
    preview.classList.remove('hidden');
  });
})();
</script>
