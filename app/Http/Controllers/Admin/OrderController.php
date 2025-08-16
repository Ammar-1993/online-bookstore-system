<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const STATUSES = ['pending', 'processing', 'shipped', 'cancelled'];
    private const PAYMENT_STATUSES = ['unpaid', 'paid', 'refunded'];

    public function index(): View
    {
        $orders = Order::with('user')->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        // OrderController@show و @invoice
        $this->authorize('view', $order);
        $order->load(['items.book', 'user']);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', self::STATUSES)],
            'payment_status' => ['required', 'string', 'in:' . implode(',', self::PAYMENT_STATUSES)],
        ]);

        $targetStatus = $data['status'];
        $targetPayment = $data['payment_status'];

        if ($targetPayment === 'paid' && $order->payment_status !== 'paid') {
            $order->markPaid(); // يحدّث status => processing
        } elseif ($targetPayment === 'refunded' && $order->payment_status !== 'refunded') {
            $order->cancelAndRestock(); // إرجاع مخزون + cancel/refund
        } else {
            if ($order->status !== $targetStatus) {
                $order->status = $targetStatus;
                $order->save();
            }
        }

        return back()->with('success', 'تم تحديث الطلب.');
    }
}
