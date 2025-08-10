<?php

namespace App\Http\Controllers;

use App\Models\Book;

class BookController extends Controller
{
    public function show(Book $book)
    {
        $book->load(['authors','publisher','category','seller']);

        $related = Book::where('status','published')
            ->where('category_id', $book->category_id)
            ->where('id','!=',$book->id)
            ->latest('published_at')
            ->take(8)->get();

        return view('books.show', compact('book','related'));
    }
}
