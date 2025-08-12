<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('role', 'Admin'); // تأكيد عبر الميدلوير أيضًا
        $s = $request->get('s');
        $categories = Category::when($s, fn($q)=>$q->where('name','like',"%{$s}%"))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100','unique:categories,name'],
            'slug' => ['nullable','string','max:150','unique:categories,slug'],
        ]);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'تمت الإضافة');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100','unique:categories,name,'.$category->id],
            'slug' => ['nullable','string','max:150','unique:categories,slug,'.$category->id],
        ]);
        if (!empty($data['slug'])) $data['slug'] = Str::slug($data['slug']);
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'تم التحديث');
    }

    public function destroy(Category $category)
    {
        abort_unless(auth()->user()->hasRole('Admin'), 403);
        $category->delete();
        return back()->with('success', 'تم الحذف');
    }
}
