<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Support\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Cart $cart)
    {
        return view('cart.index', [
            'items'    => $cart->all(),
            'currency' => $cart->currency() ?? 'USD',
            'subtotal' => $cart->subtotal(),
        ]);
    }

    public function add(Request $request, Book $book, Cart $cart)
    {
        $request->validate(['qty' => 'nullable|integer|min:1|max:99']);
        $qty = (int) $request->input('qty', 1);

        try {
            $cart->add($book, $qty);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تم إضافة الكتاب إلى السلة.');
    }

    public function update(Request $request, Book $book, Cart $cart)
    {
        $request->validate(['qty' => 'required|integer|min:0|max:99']);
        $cart->update($book, (int) $request->qty);
        return back()->with('success', 'تم تحديث السلة.');
    }

    public function remove(Book $book, Cart $cart)
    {
        $cart->remove($book);
        return back()->with('success', 'تم حذف العنصر من السلة.');
    }

    public function clear(Cart $cart)
    {
        $cart->clear();
        return back()->with('success', 'تم تفريغ السلة.');
    }
}
