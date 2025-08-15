<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $query = Book::query()
            ->with(['authors', 'publisher', 'category'])
            ->where('status', 'published');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('isbn', 'like', "%{$q}%")
                  ->orWhere('author_main', 'like', "%{$q}%");
            })
            ->orWhereHas('authors', fn($a) => $a->where('name', 'like', "%{$q}%"))
            ->orWhereHas('publisher', fn($p) => $p->where('name', 'like', "%{$q}%"))
            ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$q}%"));
        }

        $books = $query
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('home', compact('books', 'q'));
    }
}
