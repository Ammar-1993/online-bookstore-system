@csrf
@php
  // كلاس موحّد لكل الحقول: أبيض/أسود حتى في الوضع الداكن
  $ctrl = 'w-full rounded-xl bg-white text-black placeholder-gray-500 ring-1 ring-gray-300
           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-white dark:text-black';
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div>
    <label class="block text-sm mb-1 text-gray-900 dark:text-white">الاسم <span class="text-rose-500">*</span></label>
    <input type="text" name="name" value="{{ old('name', $category->name) }}" class="{{ $ctrl }}" required>
    @error('name') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
  </div>

  <div>
    <label class="block text-sm mb-1 text-gray-900 dark:text-white">slug</label>
    <input type="text" name="slug" value="{{ old('slug', $category->slug) }}" class="{{ $ctrl }}" placeholder="اتركه فارغًا لتوليد تلقائي">
    @error('slug') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
  </div>

  <div class="md:col-span-2">
    <label class="block text-sm mb-1 text-gray-900 dark:text-white">الوصف</label>
    <textarea name="description" rows="4" class="{{ $ctrl }} placeholder-gray-400" placeholder="اختياري">{{ old('description', $category->description) }}</textarea>
    @error('description') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
  </div>

  <div class="md:col-span-2">
    <label class="block text-sm mb-1 text-gray-900 dark:text-white">صورة (اختياري)</label>
    <input type="file" name="image" class="block w-full text-sm {{ $ctrl }}">
    @if($category->image_path)
      <div class="mt-2">
        <img src="{{ asset('storage/'.$category->image_path) }}" class="h-20 rounded border" alt="صورة التصنيف">
      </div>
    @endif
    @error('image') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
  </div>
</div>

{{-- شريط الأزرار السفلي (محاذاة لليسار في RTL) --}}
<div class="mt-6 -mx-5 -mb-5 border-t border-gray-100 dark:border-white/10
            sticky bottom-0 bg-white/90 dark:bg-gray-900/90 backdrop-blur
            rounded-b-2xl px-5 py-4 flex flex-col sm:flex-row items-center gap-2 justify-end">
  <button class="w-full sm:w-auto px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700" data-ripple data-loader>
    حفظ
  </button>
  <a href="{{ route('admin.categories.index') }}"
     class="w-full sm:w-auto px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-gray-100"
     data-ripple>إلغاء</a>
</div>

{{-- توليد slug تلقائي من الاسم عند تركه فارغًا --}}
<script>
(() => {
  const nameEl = document.querySelector('input[name="name"]');
  const slugEl = document.querySelector('input[name="slug"]');
  const autoSlug = () => {
    if (!nameEl || !slugEl) return;
    if (slugEl.value.trim() !== '') return;
    const s = nameEl.value.normalize('NFKD')
      .replace(/[\u064B-\u065F]/g, '')
      .replace(/[^\p{L}\p{N}\s-]/gu, '')
      .trim().toLowerCase().replace(/\s+/g,'-').replace(/-+/g,'-');
    slugEl.value = s || '';
  };
  nameEl?.addEventListener('blur', autoSlug, { passive:true });
})();
</script>
