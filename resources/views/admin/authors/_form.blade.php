@php /** @var \App\Models\Author $author */ @endphp

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm mb-1">الاسم <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name',$author->name) }}"
               class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
    </div>

    <div>
        <label class="block text-sm mb-1">Slug (اختياري)</label>
        <input type="text" name="slug" value="{{ old('slug',$author->slug) }}"
               class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
               placeholder="يُولّد تلقائياً إن تُرك فارغاً">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm mb-1">الموقع (اختياري)</label>
        <input type="url" name="website" value="{{ old('website',$author->website) }}"
               class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
               placeholder="https://example.com">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm mb-1">نبذة (اختياري)</label>
        <textarea name="bio" rows="4"
                  class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                  placeholder="نبذة مختصرة عن المؤلف">{{ old('bio',$author->bio) }}</textarea>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm mb-1">صورة المؤلف (اختياري)</label>
        <input type="file" name="avatar" accept="image/*"
               class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        @if($author->avatar_path)
            <div class="mt-2">
                <img src="{{ asset('storage/'.$author->avatar_path) }}" class="h-16 w-16 rounded-full border" alt="">
            </div>
        @endif
    </div>
</div>

<div class="flex items-center justify-end gap-2 pt-2">
    <a href="{{ route('admin.authors.index') }}" class="px-3 py-2 rounded border text-sm hover:bg-gray-50">إلغاء</a>
    <button type="submit" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
        {{ $submitLabel ?? 'حفظ' }}
    </button>
</div>
