<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AuthorRequest;
use App\Models\Author;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthorController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q',''));

        $authors = Author::query()
            ->when($q !== '', function (Builder $b) use ($q) {
                $b->where(fn($x) =>
                    $x->where('name','like',"%{$q}%")
                      ->orWhere('slug','like',"%{$q}%")
                );
            })
            ->withCount('books')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin.authors.index', compact('authors','q'));
    }

    public function create(): View
    {
        $author = new Author();
        return view('admin.authors.create', compact('author'));
    }

    public function store(AuthorRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name']);

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('authors', 'public');
        }

        $author = Author::create($data);

        return redirect()
            ->route('admin.authors.index')
            ->with('success', "تم إنشاء المؤلف «{$author->name}» بنجاح.");
    }

    public function edit(Author $author): View
    {
        return view('admin.authors.edit', compact('author'));
    }

    public function update(AuthorRequest $request, Author $author): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $author->slug, $data['name'], $author->id);

        if ($request->hasFile('avatar')) {
            if ($author->avatar_path) {
                Storage::disk('public')->delete($author->avatar_path);
            }
            $data['avatar_path'] = $request->file('avatar')->store('authors', 'public');
        }

        $author->update($data);

        return redirect()
            ->route('admin.authors.index')
            ->with('success', "تم تحديث المؤلف «{$author->name}» بنجاح.");
    }

    public function destroy(Author $author): RedirectResponse
    {
        // منع الحذف إذا لديه كتب
        if ($author->books()->exists()) {
            return back()->with('error', 'لا يمكن حذف المؤلف لوجود كتب مرتبطة به.');
        }

        if ($author->avatar_path) {
            Storage::disk('public')->delete($author->avatar_path);
        }

        $name = $author->name;
        $author->delete();

        return redirect()
            ->route('admin.authors.index')
            ->with('success', "تم حذف المؤلف «{$name}» بنجاح.");
    }

    private function uniqueSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $name, '-', 'ar') ?: Str::slug($name) ?: 'author';
        $candidate = $base; $i = 2;

        while (
            Author::when($ignoreId, fn($q)=>$q->where('id','!=',$ignoreId))
                  ->where('slug',$candidate)->exists()
        ) {
            $candidate = "{$base}-{$i}";
            $i++;
        }
        return $candidate;
    }
}
