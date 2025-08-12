<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublisherController extends Controller
{
    public function index(Request $request)
    {
        $s = $request->get('s');
        $publishers = Publisher::when($s, fn($q)=>$q->where('name','like',"%{$s}%"))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.publishers.index', compact('publishers'));
    }

    public function create()
    {
        return view('admin.publishers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => ['required','string','max:120','unique:publishers,name'],
            'slug'    => ['nullable','string','max:150','unique:publishers,slug'],
            'website' => ['nullable','url','max:255'],
        ]);
        $data['slug'] = Str::slug($data['slug'] ?: $data['name']);
        Publisher::create($data);

        return redirect()->route('admin.publishers.index')->with('success', 'تمت الإضافة');
    }

    public function edit(Publisher $publisher)
    {
        return view('admin.publishers.edit', compact('publisher'));
    }

    public function update(Request $request, Publisher $publisher)
    {
        $data = $request->validate([
            'name'    => ['required','string','max:120','unique:publishers,name,'.$publisher->id],
            'slug'    => ['nullable','string','max:150','unique:publishers,slug,'.$publisher->id],
            'website' => ['nullable','url','max:255'],
        ]);
        if (!empty($data['slug'])) $data['slug'] = Str::slug($data['slug']);
        $publisher->update($data);

        return redirect()->route('admin.publishers.index')->with('success', 'تم التحديث');
    }

    public function destroy(Publisher $publisher)
    {
        $publisher->delete();
        return back()->with('success', 'تم الحذف');
    }
}
