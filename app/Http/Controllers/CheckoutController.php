<?php

namespace App\Http\Controllers;

use App\Mail\OrderPlaced;
use App\Models\{Book, Order, OrderItem};
use App\Support\Cart; // <-- استخدم عربة الجلسة
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CheckoutController extends Controller
{
    /** صفحة نموذج الـ Checkout */
    public function show(Cart $cart): View|RedirectResponse
{
    $rawItems = $cart->all(); // عناصر السلة من الجلسة كمصفوفات
    if (empty($rawItems)) {
        return redirect()->route(route: 'cart.index')->with('info', 'سلتك فارغة.');
    }

    // جِب كل الكتب المطلوبة دفعة واحدة
    $ids   = collect($rawItems)->pluck('book_id')->filter()->unique()->all();
    $books = Book::whereIn('id', $ids)->get()->keyBy('id');

    // حوّل كل عنصر إلى كائن واربط له الكتاب (إن وُجد)
    $items = collect($rawItems)->map(function (array $row) use ($books) {
        $row['book'] = $books[$row['book_id']] ?? null; // Book|null
        return (object) $row; // حتى نقدر نستخدم -> في الواجهات
    })->all();

    $prefill = [
        'customer_name'  => auth()->user()->name  ?? '',
        'customer_email' => auth()->user()->email ?? '',
        'customer_phone' => '',
        'address_line1'  => '',
        'city'           => '',
        'country'        => '',
        'notes'          => '',
    ];

    return view('checkout.index', [
        'items'    => $items,
        'currency' => $cart->currency() ?? 'USD',
        'subtotal' => $cart->subtotal(),
        'shipping' => 0.00,
        'total'    => $cart->subtotal(),
        'prefill'  => $prefill,
    ]);
}


    /** إنشاء الطلب من السلة (متوافق مع جداولك الحالية) */
public function store(Request $request, Cart $cart): RedirectResponse
{
    if (! auth()->check()) {
        return redirect()->route('login')->with('info', 'يجب تسجيل الدخول لإكمال الطلب.');
    }

    $data = $request->validate([
        'customer_name'  => ['required','string','max:100'],
        'customer_email' => ['required','email','max:150'],
        'customer_phone' => ['nullable','string','max:50'],
        'address_line1'  => ['nullable','string','max:200'],
        'city'           => ['nullable','string','max:100'],
        'country'        => ['nullable','string','max:100'],
        'notes'          => ['nullable','string','max:2000'],
    ]);

    $items = $cart->all();
    if (empty($items)) {
        return redirect()->route('cart.index')->with('info', 'سلتك فارغة.');
    }

    $currency = $cart->currency() ?? 'USD';

    try {
        DB::transaction(function () use ($items, $currency, $data, $cart) {
            // جهّز كتب السلة دفعة واحدة
            $bookIds = collect($items)->pluck('book_id')->filter()->unique()->all();
            $books   = Book::whereIn('id', $bookIds)->get()->keyBy('id');

            // احسب الإجمالي من الداتابيس وتحقق التوافر (بدون خصم)
            $computedSubtotal = 0.0;
            $normalizedItems  = [];

            foreach ($items as $row) {
                $bookId = (int) ($row['book_id'] ?? 0);
                $qty    = max(1, (int) ($row['qty'] ?? 1));

                /** @var \App\Models\Book|null $book */
                $book = $books[$bookId] ?? null;
                if (! $book || $book->status !== 'published') {
                    throw new \RuntimeException('أحد الكتب غير متاح حالياً.');
                }

                if ($book->stock_qty < $qty) {
                    throw new \RuntimeException("الكمية المطلوبة لكتاب «{$book->title}» غير متاحة.");
                }

                $unit  = (float) $book->price;     // سعر موثوق من قاعدة البيانات
                $line  = $unit * $qty;
                $computedSubtotal += $line;

                $normalizedItems[] = [
                    'book_id'     => $book->id,
                    'qty'         => $qty,
                    'unit_price'  => $unit,
                    'total_price' => $line,
                ];
            }

            // عنوان الشحن كـ JSON
            $shippingJson = [
                'name'    => $data['customer_name'],
                'email'   => $data['customer_email'],
                'phone'   => $data['customer_phone'] ?? null,
                'address' => $data['address_line1'] ?? null,
                'city'    => $data['city'] ?? null,
                'country' => $data['country'] ?? null,
                'notes'   => $data['notes'] ?? null,
            ];

            // أنشئ الطلب (pending / unpaid) — بدون خصم مخزون
            $order = Order::create([
                'user_id'         => auth()->id(),
                'status'          => 'pending',
                'payment_status'  => 'unpaid',
                'total_amount'    => $computedSubtotal,
                'currency'        => $currency,
                'shipping_address'=> $shippingJson,
                'billing_address' => null,
                'placed_at'       => now(), // أو اتركها null وتملأها عند markPaid()
            ]);

            // أنشئ عناصر الطلب
            foreach ($normalizedItems as $n) {
                OrderItem::create([
                    'order_id'    => $order->id,
                    'book_id'     => $n['book_id'],
                    'qty'         => $n['qty'],
                    'unit_price'  => $n['unit_price'],
                    'total_price' => $n['total_price'],
                ]);
            }

            // إرسال بريد تأكيد الإنشاء (بلا خصم)
            Mail::to($data['customer_email'])->send(new OrderPlaced($order->fresh('items')));

            // إفراغ السلة
            $cart->clear();

            // رقم الطلب للعرض مرة واحدة
            session()->flash('order_no', sprintf('ORD-%06d', $order->id));
        });
    } catch (\Throwable $e) {
        report($e);
        return back()->with('error', $e->getMessage())->withInput();
    }

    return redirect()->route('checkout.thankyou');
}


    /** صفحة الشكر */
    public function thankyou(): View
    {
        return view('checkout.thankyou', [
            'order_no' => session('order_no'),
        ]);
    }
}
