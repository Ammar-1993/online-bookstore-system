<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Book;

class CategoryController extends Controller
{
    public function show(Category $category, Request $request)
    {
        // يشمل الفئة وأطفالها (إن وجدوا)
        $ids = $category->children()->pluck('id');
        $ids->push($category->id);

        $q = Book::with(['authors','publisher','category'])
            ->where('status','published')
            ->whereIn('category_id', $ids);

        if ($s = trim((string)$request->q)) {
            $q->where(function($w) use ($s) {
                $w->where('title','like',"%{$s}%")
                  ->orWhere('isbn','like',"%{$s}%");
            });
        }

        $books = $q->latest('published_at')->paginate(10)->withQueryString();

        return view('categories.show', compact('category','books'));
    }
}
