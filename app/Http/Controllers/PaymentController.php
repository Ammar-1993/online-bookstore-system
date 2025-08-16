<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function mockSuccess(Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        if (! $order->isPayable()) {
            return redirect()->route('orders.show', $order)
                ->with('warning', 'لا يمكن إتمام الدفع لهذا الطلب.');
        }

        try {
            $order->markPaid();
        } catch (\Throwable $e) {
            Log::warning('mockSuccess failed', ['order_id' => $order->id, 'err' => $e->getMessage()]);
            return redirect()->route('orders.show', $order)
                ->with('error', $e->getMessage());
        }

        // optional($order->user)->notify(new \App\Notifications\OrderPaidNotification($order));
        return redirect()->route('orders.show', $order)->with('success', 'تم الدفع بنجاح!');
    }

    public function cancel(Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        if (! $order->isCancelable()) {
            return back()->with('warning', 'تعذر إلغاء هذا الطلب.');
        }

        $order->cancelAndRestock();
        // optional($order->user)->notify(new \App\Notifications\OrderCancelledNotification($order));
        return redirect()->route('orders.show', $order)->with('success', 'تم إلغاء الطلب وإرجاع المخزون (إن وُجد).');
    }
}
