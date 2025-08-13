@php
    /** @var \App\Models\Publisher $publisher */
@endphp

<div class="grid md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm mb-1">الاسم <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $publisher->name) }}"
               class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
    </div>

    <div>
        <label class="block text-sm mb-1">Slug (اختياري)</label>
        <input type="text" name="slug" value="{{ old('slug', $publisher->slug) }}"
               class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
               placeholder="يُولّد تلقائياً إن تُرك فارغاً">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm mb-1">الموقع (اختياري)</label>
        <input type="url" name="website" value="{{ old('website', $publisher->website) }}"
               class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
               placeholder="https://example.com">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm mb-1">شعار الناشر (اختياري)</label>
        <input type="file" name="logo" accept="image/*"
               class="w-full rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        @if(!empty($publisher->logo_path))
            <div class="mt-2">
                <img src="{{ asset('storage/'.$publisher->logo_path) }}" alt="Logo" class="h-16 rounded border">
            </div>
        @endif
    </div>
</div>

<div class="flex items-center justify-end gap-2 pt-2">
    <a href="{{ route('admin.publishers.index') }}"
       class="px-3 py-2 rounded border text-sm hover:bg-gray-50">إلغاء</a>

    <button type="submit"
            class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
        {{ $submitLabel ?? 'حفظ' }}
    </button>
</div>
