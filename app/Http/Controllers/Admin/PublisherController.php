<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublisherRequest;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublisherController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $sort = $request->get('sort', 'id');           // id | name | slug | books_count
        $dir = strtolower($request->get('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        // السماح فقط بهذه الحقول منعًا لأي حقن
        $allowedSorts = ['id', 'name', 'slug', 'books_count'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'id';
        }

        $query = Publisher::query()
            ->when($q !== '', function (Builder $b) use ($q) {
                $b->where(function ($x) use ($q) {
                    $x->where('name', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%")
                        ->orWhere('website', 'like', "%{$q}%");
                });
            })
            ->withCount('books');

        // تطبيق الترتيب
        if ($sort === 'books_count') {
            $query->orderBy('books_count', $dir);
        } else {
            $query->orderBy($sort, $dir);
            // (اختياري) ترتيب عربي أدق للاسم/السلَج:
            // if (in_array($sort, ['name','slug'], true)) {
            //     $query->orderByRaw("CONVERT($sort USING utf8mb4) COLLATE utf8mb4_ar_0900_ai_ci {$dir}");
            // }
        }

        $publishers = $query->paginate(12)->withQueryString();

        return view('admin.publishers.index', compact('publishers', 'q', 'sort', 'dir'));
    }


    public function create(): View
    {
        $publisher = new Publisher();
        return view('admin.publishers.create', compact('publisher'));
    }

    public function store(PublisherRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name']);

        if (Schema::hasColumn('publishers', 'logo_path') && $request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('publishers', 'public');
        }

        $publisher = Publisher::create($data);

        return redirect()->route('admin.publishers.index')
            ->with('success', "تم إنشاء الناشر «{$publisher->name}» بنجاح.");
    }

    public function edit(Publisher $publisher): View
    {
        return view('admin.publishers.edit', compact('publisher'));
    }

    public function update(PublisherRequest $request, Publisher $publisher): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $publisher->slug, $data['name'], $publisher->id);

        if (Schema::hasColumn('publishers', 'logo_path') && $request->hasFile('logo')) {
            if ($publisher->logo_path) {
                Storage::disk('public')->delete($publisher->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('publishers', 'public');
        }

        $publisher->update($data);

        return redirect()->route('admin.publishers.index')
            ->with('success', "تم تحديث الناشر «{$publisher->name}» بنجاح.");
    }

    public function destroy(Publisher $publisher): RedirectResponse
    {
        if ($publisher->books()->exists()) {
            return back()->with('error', 'لا يمكن حذف الناشر لوجود كتب مرتبطة به.');
        }

        if (Schema::hasColumn('publishers', 'logo_path') && $publisher->logo_path) {
            Storage::disk('public')->delete($publisher->logo_path);
        }

        $name = $publisher->name;
        $publisher->delete();

        return redirect()->route('admin.publishers.index')
            ->with('success', "تم حذف الناشر «{$name}» بنجاح.");
    }

    private function uniqueSlug(?string $slug, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($slug ?: $name, '-', 'ar') ?: Str::slug($name) ?: 'publisher';
        $candidate = $base;
        $i = 2;

        while (
            Publisher::when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $candidate)->exists()
        ) {
            $candidate = "{$base}-{$i}";
            $i++;
        }
        return $candidate;
    }
}
