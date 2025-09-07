@extends('layouts.app')

@section('title', 'الكتب')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
  <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">تصفّح الكتب</h1>

  <!-- نموذج الفلاتر -->
  <form id="filtersForm" class="bg-white dark:bg-gray-900 rounded-2xl shadow ring-1 ring-black/5 dark:ring-white/10 p-4 grid md:grid-cols-12 gap-3"
        method="GET" action="{{ route('books.index') }}">
    <input type="hidden" name="per_page" value="{{ $filters['per_page'] ?? 12 }}">

    <!-- نص البحث -->
    <div class="md:col-span-4">
      <label class="block text-sm mb-1 text-gray-700 dark:text-gray-300">ابحث</label>
      <input type="search" name="q" value="{{ $filters['q'] ?? '' }}"
             placeholder="عنوان، مؤلف، ناشر ..."
             class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100">
    </div>

    <!-- تصنيف -->
    <div class="md:col-span-2">
      <label class="block text-sm mb-1 text-gray-700 dark:text-gray-300">التصنيف</label>
      <select name="category" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800">
        <option value="">الكل</option>
        @foreach($categories as $c)
          <option value="{{ $c->slug }}" @selected(($filters['category'] ?? '') === $c->slug)>{{ $c->name }}</option>
        @endforeach
      </select>
    </div>

    <!-- الناشر -->
    <div class="md:col-span-2">
      <label class="block text-sm mb-1 text-gray-700 dark:text-gray-300">الناشر</label>
      <select name="publisher" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800">
        <option value="">الكل</option>
        @foreach($publishers as $p)
          <option value="{{ $p->slug }}" @selected(($filters['publisher'] ?? '') === $p->slug)>{{ $p->name }}</option>
        @endforeach
      </select>
    </div>

    <!-- مؤلف -->
    <div class="md:col-span-2">
      <label class="block text-sm mb-1 text-gray-700 dark:text-gray-300">المؤلف</label>
      <select name="author" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800">
        <option value="">الكل</option>
        @foreach($authors as $a)
          <option value="{{ $a->slug }}" @selected(($filters['author'] ?? '') === $a->slug)>{{ $a->name }}</option>
        @endforeach
      </select>
    </div>

    <!-- السعر الأدنى/الأقصى -->
    <div class="md:col-span-1">
      <label class="block text-sm mb-1 text-gray-700 dark:text-gray-300">السعر من</label>
      <input type="number" step="0.01" min="0" name="price_min" value="{{ $filters['price_min'] ?? '' }}"
             class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800">
    </div>
    <div class="md:col-span-1">
      <label class="block text-sm mb-1 text-gray-700 dark:text-gray-300">السعر إلى</label>
      <input type="number" step="0.01" min="0" name="price_max" value="{{ $filters['price_max'] ?? '' }}"
             class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800">
    </div>

    <!-- الترتيب -->
    <div class="md:col-span-2">
      <label class="block text-sm mb-1 text-gray-700 dark:text-gray-300">رتّب حسب</label>
      <select name="sort" class="w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800">
        <option value="relevance"  @selected(($filters['sort'] ?? '') === 'relevance')>الملاءمة</option>
        <option value="newest"     @selected(($filters['sort'] ?? '') === 'newest')>الأحدث</option>
        <option value="price_asc"  @selected(($filters['sort'] ?? '') === 'price_asc')>السعر: من الأقل للأعلى</option>
        <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>السعر: من الأعلى للأقل</option>
        <option value="rating_desc"@selected(($filters['sort'] ?? '') === 'rating_desc')>التقييم</option>
      </select>
    </div>

    <!-- أزرار -->
    <div class="md:col-span-12 flex items-center gap-2">
      <button type="submit"
              class="px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700"
              data-loader>تطبيق</button>
      <a href="{{ route('books.index') }}"
         class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600">إعادة ضبط</a>
    </div>
  </form>

  <!-- النتائج -->
  <div id="results" class="mt-4">
    @include('books.partials.grid', ['books' => $books])
  </div>
</div>
@endsection
