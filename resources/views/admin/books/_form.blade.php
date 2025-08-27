@php
  $isEdit = $book && $book->exists;
@endphp

<div class="grid md:grid-cols-2 gap-4">
  <div>
    <label class="block mb-1 text-gray-900 dark:text-white">العنوان</label>
    <input name="title" class="w-full rounded border-gray-300" value="{{ old('title', $book->title) }}" required>
  </div>





  <div>
    <label class="block mb-1 text-gray-900 dark:text-white">الـ Slug</label>
    <input type="text" name="slug" class="w-full rounded border-gray-300" value="{{ old('slug', $book->slug) }}"
      placeholder="اتركه فارغًا لتوليد تلقائي">
    @error('slug') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
  </div>
  <div>
    <label class="block mb-1 text-gray-900 dark:text-white">ISBN</label>
    <input name="isbn" class="w-full rounded border-gray-300" value="{{ old('isbn', $book->isbn) }}" required>
  </div>
  <div>
    <label class="block mb-1 text-gray-900 dark:text-white">السعر</label>
    <input type="number" step="0.01" name="price" class="w-full rounded border-gray-300"
      value="{{ old('price', $book->price) }}" required>
  </div>
  <div>
    <label class="block mb-1 text-gray-900 dark:text-white">العملة</label>
    <input name="currency" class="w-full rounded border-gray-300"
      value="{{ old('currency', $book->currency ?? 'USD') }}" required>
  </div>
  <div>
    <label class="block mb-1 text-gray-900 dark:text-white">المخزون</label>
    <input type="number" name="stock_qty" class="w-full rounded border-gray-300"
      value="{{ old('stock_qty', $book->stock_qty) }}" required>
  </div>
  <div>
    <label class="block mb-1 text-gray-900 dark:text-white">التصنيف</label>
    <select name="category_id" class="w-full rounded border-gray-300">
      <option value="">—</option>
      @foreach($categories as $c)
      <option value="{{ $c->id }}" @selected(old('category_id', $book->category_id) == $c->id)>{{ $c->name }}</option>
    @endforeach
    </select>
  </div>
  <div>
    <label class="block mb-1 text-gray-900 dark:text-white">الناشر</label>
    <select name="publisher_id" class="w-full rounded border-gray-300">
      <option value="">—</option>
      @foreach($publishers as $p)
      <option value="{{ $p->id }}" @selected(old('publisher_id', $book->publisher_id) == $p->id)>{{ $p->name }}</option>
    @endforeach
    </select>
  </div>
  <div class="md:col-span-2">
    <label class="block mb-1 text-gray-900 dark:text-white">المؤلفون</label>
    <select name="authors[]" multiple class="w-full rounded border-gray-300">
      @php $selected = old('authors', $book->authors->pluck('id')->all() ?? []); @endphp
      @foreach($authors as $a)
      <option value="{{ $a->id }}" @selected(in_array($a->id, $selected))>{{ $a->name }}</option>
    @endforeach
    </select>
  </div>
  <div class="md:col-span-2">
    <label class="block mb-1 text-gray-900 dark:text-white">الوصف</label>
    <textarea name="description" rows="5"
      class="w-full rounded border-gray-300">{{ old('description', $book->description) }}</textarea>
  </div>
  <div>
    <label class="block mb-1 text-gray-900 dark:text-white">الحالة</label>
    <select name="status" class="w-full rounded border-gray-300">
      @foreach(['draft' => 'مسودة', 'published' => 'منشور'] as $v => $label)
      <option value="{{ $v }}" @selected(old('status', $book->status) == $v)>{{ $label }}</option>
    @endforeach
    </select>
  </div>
  <div>
    <label class="block mb-1 text-gray-900 dark:text-white">تاريخ النشر</label>
    <input type="datetime-local" name="published_at" class="w-full rounded border-gray-300"
      value="{{ old('published_at', optional($book->published_at)->format('Y-m-d\TH:i')) }}">
  </div>
  <div class="md:col-span-2">
    <label class="block mb-1 text-gray-900 dark:text-white">غلاف الكتاب</label>
    <input type="file" name="cover" accept="image/*" class="block">
    @if($isEdit && $book->cover_image_path)
    <img src="{{ asset('storage/' . $book->cover_image_path) }}" class="mt-2 h-24 rounded">
  @endif
  </div>
</div>