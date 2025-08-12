<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

use App\Models\{Book, Category, Publisher, Author};
use App\Http\Requests\Admin\StoreBookRequest;
use App\Http\Requests\Admin\UpdateBookRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Book::class);

        $query = Book::with(['category', 'publisher', 'authors']) // <-- authors (جمع)
            ->when(
                auth()->user()->hasRole('Seller'),
                fn(Builder $q) =>
                $q->where('seller_id', auth()->id())
            )
            ->when(
                $s = $request->get('s'),
                fn(Builder $q) =>
                $q->where(function (Builder $qq) use ($s) {
                    $qq->where('title', 'like', "%{$s}%")
                        ->orWhere('isbn', 'like', "%{$s}%");
                })
            );

        // لاحظ أسماء الأعمدة حسب المايجريشن (stock_qty, cover_image_path)
        $books = $query->latest()->paginate(6)->withQueryString();

        return view('admin.books.index', compact('books'));
    }


    public function create(): View
    {
        $this->authorize('create', Book::class);
        return view('admin.books.create', [
            'categories' => Category::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
            'authors' => Author::orderBy('name')->get(),
            'book' => new Book(),
        ]);
    }

    public function store(StoreBookRequest $request)
    {
        $data = $request->validated();

        // slug تلقائي إن لم يُمرّر
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']) . '-' . Str::random(6);
        $data['seller_id'] = auth()->id();

        if ($request->hasFile('cover')) {
            $data['cover_image_path'] = $request->file('cover')->store('covers', 'public');
        }

        $book = Book::create($data);
        $book->authors()->sync($data['authors'] ?? []);

        return redirect()->route('admin.books.index')->with('success', 'تمت إضافة الكتاب بنجاح.');
    }

    public function show(Book $book)
    {
        $this->authorize('view', $book);

        $book->load(['category','publisher','authors','seller']);
        return view('admin.books.show', compact('book'));
    }

    public function edit(Book $book): View
    {
        $this->authorize('update', $book);
        return view('admin.books.edit', [
            'book' => $book->load('authors'),
            'categories' => Category::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
            'authors' => Author::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $this->authorize('update', $book);
        $data = $request->validated();

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']) . '-' . $book->id;

        if ($request->hasFile('cover')) {
            if ($book->cover_image_path)
                Storage::disk('public')->delete($book->cover_image_path);
            $data['cover_image_path'] = $request->file('cover')->store('covers', 'public');
        }

        $book->update($data);
        $book->authors()->sync($data['authors'] ?? []);

        return redirect()->route('admin.books.index')->with('success', 'تم تحديث الكتاب بنجاح.');
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        if ($book->cover_image_path)
            Storage::disk('public')->delete($book->cover_image_path);
        $book->authors()->detach();
        $book->delete();

        return back()->with('success', 'تم حذف الكتاب.');
    }
}
