<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publisher;

class PublisherController extends Controller
{
    public function show(Publisher $publisher, Request $request)
    {
        $q = $publisher->books()->with(['authors','category'])
            ->where('status','published');

        if ($s = trim((string)$request->q)) {
            $q->where('title','like',"%{$s}%");
        }

        $books = $q->latest('published_at')->paginate(10)->withQueryString();

        return view('publishers.show', compact('publisher','books'));
    }
}
