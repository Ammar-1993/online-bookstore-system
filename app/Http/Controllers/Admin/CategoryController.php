<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    // app/Http/Controllers/Admin/CategoryController.php

    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $sort = $request->get('sort', 'id');         // id | name | books_count
        $dir = strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        // السماح فقط بهذه الحقول للفرز (أمانًا ومنعًا لأي حقن)
        $allowedSorts = ['id', 'name', 'books_count'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }

        $query = Category::query()
            ->when($q !== '', function (Builder $builder) use ($q) {
                $builder->where(
                    fn($x) =>
                    $x->where('name', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%")
                );
            })
            ->withCount('books');

        // ترتيب النتائج
        if ($sort === 'books_count') {
            $query->orderBy('books_count', $dir);
        } else {
            $query->orderBy($sort, $dir);
            // ملاحظة: لو واجهت ترتيب غير دقيق بالعربية مع name،
            // يمكنك استخدام محرك/Collation مناسب (اختياري):
            // $query->orderByRaw("CONVERT(name USING utf8mb4) COLLATE utf8mb4_ar_0900_ai_ci {$dir}");
        }

        $categories = $query
            ->paginate(12)
            ->withQueryString(); // يحافظ على q/sort/dir أثناء التنقل بين الصفحات

        return view('admin.categories.index', compact('categories', 'q'));
    }


    public function create(): View
    {
        $category = new Category();

        return view('admin.categories.create', compact('category'));
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // توليد slug فريد إن لم يُرسل أو كان مكررًا
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name']);

        // رفع صورة (اختياري) إذا كان العمود موجودًا
        if (Schema::hasColumn('categories', 'image_path') && $request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "تم إنشاء التصنيف «{$category->name}» بنجاح.");
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();

        // تحديث slug مع ضمان فريدانيته (مع استثناء السجل الحالي)
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $category->slug, $data['name'], $category->id);

        // استبدال الصورة إن أُرسلت (وعمود الصورة موجود)
        if (Schema::hasColumn('categories', 'image_path') && $request->hasFile('image')) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "تم تحديث التصنيف «{$category->name}» بنجاح.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        // منع الحذف إذا عليه كتب
        if ($category->books()->exists()) {
            return back()->with('error', 'لا يمكن حذف التصنيف لوجود كتب مرتبطة به.');
        }

        // ومنع الحذف إن كان لديه تصنيفات فرعية
        if ($category->children()->exists()) {
            return back()->with('error', 'لا يمكن حذف التصنيف لوجود تصنيفات فرعية مرتبطة به.');
        }

        // حذف الصورة إن وُجدت وكان العمود موجودًا
        if (Schema::hasColumn('categories', 'image_path') && $category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $name = $category->name;
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', "تم حذف التصنيف «{$name}» بنجاح.");
    }

    /**
     * توليد slug فريد. إذا أُعطي ignoreId يتم تجاهله عند التحقق (لتحديث السجل الحالي).
     */
    private function uniqueSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        // توليد أساس للـ slug (يدعم العربية قدر الإمكان)، مع fallback
        $base = Str::slug($slug ?: $name, '-', 'ar') ?: Str::slug($name);
        $base = $base ?: 'category';

        $candidate = $base;
        $i = 2;

        while (
            Category::when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $candidate)
                ->exists()
        ) {
            $candidate = "{$base}-{$i}";
            $i++;
        }

        return $candidate;
    }
}
