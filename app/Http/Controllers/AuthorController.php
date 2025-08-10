<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Author;

class AuthorController extends Controller
{
    public function show(Author $author, Request $request)
    {
        $q = $author->books()->with(['publisher','category'])
            ->where('status','published');

        if ($s = trim((string)$request->q)) {
            $q->where('title','like',"%{$s}%");
        }

        $books = $q->latest('published_at')->paginate(24)->withQueryString();

        return view('authors.show', compact('author','books'));
    }
}
