<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddToCartRequest;
use App\Models\Book;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Cart $cart): View
    {
        return view('cart.index', [
            'items'    => $cart->all(),                       // بنية العناصر كما تُعيدها خدمة السلة لديك
            'currency' => $cart->currency() ?: config('app.currency','USD'),
            'subtotal' => (float) $cart->subtotal(),
        ]);
    }

    public function add(AddToCartRequest $request, Book $book, Cart $cart): RedirectResponse
    {
        $qty = (int) $request->input('qty', 1);

        // تحقق من التوفر
        $available = (int) ($book->stock_qty ?? 0);
        if ($available <= 0) {
            return back()->with('warning', 'عذرًا، هذا الكتاب غير متاح حاليًا.');
        }

        // لا نتجاوز المخزون المتاح
        $reduced = false;
        if ($qty > $available) {
            $qty     = $available;
            $reduced = true;
        }

        // add(book_id, qty) — وفق توقيع خدمتك
        $cart->add($book->id, $qty);

        $msg = $reduced
            ? 'تمت إضافة الحدّ المتاح إلى السلة.'
            : 'تمت إضافة الكتاب إلى السلة.';

        return redirect()->route('cart.index')->with('success', $msg);
    }

    public function update(Request $request, Book $book, Cart $cart): RedirectResponse
    {
        $request->validate([
            'qty' => ['required','integer','min:0','max:99'],
        ]);

        $qty = (int) $request->qty;

        // 0 = إزالة
        if ($qty === 0) {
            $cart->remove($book);
            return back()->with('success', 'تم حذف العنصر من السلة.');
        }

        // لا نتجاوز المخزون
        $available = (int) ($book->stock_qty ?? 0);
        if ($available <= 0) {
            // نفترض نفاد المخزون، نحذف السطر
            $cart->remove($book);
            return back()->with('warning', 'هذا الكتاب أصبح غير متاح وتمت إزالته من السلة.');
        }

        if ($qty > $available) {
            $qty = $available;
            $cart->update($book, $qty);
            return back()->with('warning', 'تم ضبط الكمية إلى الحدّ المتاح.');
        }

        $cart->update($book, $qty);
        return back()->with('success', 'تم تحديث السلة.');
    }

    public function remove(Book $book, Cart $cart): RedirectResponse
    {
        $cart->remove($book);
        return back()->with('success', 'تم حذف العنصر من السلة.');
    }

    public function clear(Cart $cart): RedirectResponse
    {
        $cart->clear();
        return back()->with('success', 'تم تفريغ السلة.');
    }
}
