<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query()
            ->with(['authors','publisher','category'])
            ->where('status', 'published');

        if ($s = trim((string) $request->get('q'))) {
            $query->where(function ($w) use ($s) {
                $w->where('title', 'like', "%{$s}%")
                  ->orWhere('isbn', 'like', "%{$s}%")
                  ->orWhere('author_main', 'like', "%{$s}%");
            });
        }

        $books = $query->latest('published_at')
            ->paginate(24)
            ->withQueryString();

        return view('home', compact('books'));
    }
}
