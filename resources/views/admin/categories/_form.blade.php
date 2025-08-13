@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm mb-1">الاسم *</label>
        <input type="text" name="name" value="{{ old('name', $category->name) }}"
               class="w-full rounded-md border-gray-300" required>
        @error('name') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block text-sm mb-1">slug</label>
        <input type="text" name="slug" value="{{ old('slug', $category->slug) }}"
               class="w-full rounded-md border-gray-300" placeholder="اتركه فارغًا لتوليد تلقائي">
        @error('slug') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm mb-1">الوصف</label>
        <textarea name="description" rows="4" class="w-full rounded-md border-gray-300"
                  placeholder="اختياري">{{ old('description', $category->description) }}</textarea>
        @error('description') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm mb-1">صورة (اختياري)</label>
        <input type="file" name="image" class="block w-full text-sm">
        @if($category->image_path)
            <div class="mt-2">
                <img src="{{ asset('storage/'.$category->image_path) }}" class="h-20 rounded border">
            </div>
        @endif
        @error('image') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mt-6 flex items-center gap-2">
    <button class="px-4 py-2 bg-indigo-600 text-white rounded-md">حفظ</button>
    <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-200 rounded-md">إلغاء</a>
</div>
