<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->get('s');
        $authors = Author::when($s, fn($q)=>$q->where('name','like',"%{$s}%"))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.authors.index', compact('authors'));
    }

    public function create()
    {
        return view('admin.authors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120','unique:authors,name'],
            'slug' => ['nullable','string','max:150','unique:authors,slug'],
            'bio'  => ['nullable','string'],
        ]);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        Author::create($data);

        return redirect()->route('admin.authors.index')->with('success', 'تمت الإضافة');
    }

    public function edit(Author $author)
    {
        return view('admin.authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $data = $request->validate([
            'name' => ['required','string','max:120','unique:authors,name,'.$author->id],
            'slug' => ['nullable','string','max:150','unique:authors,slug,'.$author->id],
            'bio'  => ['nullable','string'],
        ]);
        if (!empty($data['slug'])) $data['slug'] = Str::slug($data['slug']);
        $author->update($data);

        return redirect()->route('admin.authors.index')->with('success', 'تم التحديث');
    }

    public function destroy(Author $author)
    {
        // لو تحب تمنع الحذف في حال مرتبط بكتب:
        // if ($author->books()->exists()) return back()->with('error','مربوط بكتب.');
        $author->delete();
        return back()->with('success', 'تم الحذف');
    }
}
