<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Notifications\OrderPaidNotification;
use App\Notifications\OrderCancelledNotification;
use App\Notifications\OrderStatusUpdatedNotification;
use App\Notifications\OrderShippedNotification;
use Stripe\StripeClient;

class OrderController extends Controller
{
    private const STATUSES = ['pending', 'processing', 'shipped', 'cancelled'];
    private const PAYMENT_STATUSES = ['unpaid', 'paid', 'refunded'];

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'status'          => ['nullable', 'in:' . implode(',', self::STATUSES)],
            'payment_status'  => ['nullable', 'in:' . implode(',', self::PAYMENT_STATUSES)],
            'email'           => ['nullable', 'string', 'max:255'],
            'from'            => ['nullable', 'date'],
            'to'              => ['nullable', 'date'],
        ]);

        $orders = Order::query()
            ->with('user')
            ->when(!empty($validated['status']), fn($q) =>
                $q->where('status', $validated['status'])
            )
            ->when(!empty($validated['payment_status']), fn($q) =>
                $q->where('payment_status', $validated['payment_status'])
            )
            ->when(!empty($validated['email']), fn($q) =>
                $q->whereHas('user', function ($uq) use ($validated) {
                    $email = trim((string) $validated['email']);
                    $uq->where('email', 'like', '%' . $email . '%');
                })
            )
            ->when(!empty($validated['from']), fn($q) =>
                $q->where('created_at', '>=', $validated['from'] . ' 00:00:00')
            )
            ->when(!empty($validated['to']), fn($q) =>
                $q->where('created_at', '<=', $validated['to'] . ' 23:59:59')
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders') + [
            'filters' => [
                'status'         => $validated['status']         ?? null,
                'payment_status' => $validated['payment_status'] ?? null,
                'email'          => $validated['email']          ?? null,
                'from'           => $validated['from']           ?? null,
                'to'             => $validated['to']             ?? null,
            ],
        ]);
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);
        $order->load(['items.book', 'user']);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status'          => ['required', 'string', 'in:' . implode(',', self::STATUSES)],
            'payment_status'  => ['required', 'string', 'in:' . implode(',', self::PAYMENT_STATUSES)],
        ]);

        $targetStatus   = $data['status'];
        $targetPayment  = $data['payment_status'];
        $oldStatus      = (string) $order->status;
        $wasPaid        = ($order->payment_status === 'paid');

        if ($targetPayment === 'paid' && $order->payment_status !== 'paid') {
            $order->markPaid();
            $order->user?->notify((new OrderPaidNotification($order))->locale('ar'));
        } elseif ($targetPayment === 'refunded' && $order->payment_status === 'paid') {
            $order->cancelAndRestock();
            $order->user?->notify((new OrderCancelledNotification($order))->locale('ar'));
        } else {
            if ($order->status !== $targetStatus) {
                $order->status = $targetStatus;
                $order->save();

                if ($targetStatus === 'cancelled' && ! $wasPaid) {
                    $order->user?->notify((new OrderCancelledNotification($order))->locale('ar'));
                }

                if (in_array($targetStatus, ['processing', 'shipped'], true)) {
                    $order->user?->notify((new OrderStatusUpdatedNotification($order, $oldStatus, $targetStatus))->locale('ar'));
                }
            }
        }

        return back()->with('success', 'تم تحديث الطلب.');
    }

    /** إجراء الشحن: تعيين رقم تتبع وتغيير الحالة وإرسال بريد */
    public function ship(Request $request, Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $data = $request->validate([
            'tracking_number' => ['required', 'string', 'max:190'],
            'shipping_carrier'=> ['nullable', 'string', 'max:50'],
            'tracking_url'    => ['nullable', 'url', 'max:500'],
        ]);

        $order->markShipped(
            $data['tracking_number'],
            $data['shipping_carrier'] ?? null,
            $data['tracking_url'] ?? null
        );

        $order->user?->notify((new OrderShippedNotification($order))->locale('ar'));

        return back()->with('success', 'تم تحديث معلومات الشحن وإرسال إشعار للعميل.');
    }

    public function refund(Order $order): RedirectResponse
    {
        $this->authorize('update', $order);

        if ($order->payment_status !== 'paid' || ! $order->charge_id) {
            return back()->with('warning', 'لا يمكن استرجاع هذا الطلب.');
        }

        $client = new StripeClient((string) env('STRIPE_SECRET'));

        try {
            $client->refunds->create([
                'charge'   => $order->charge_id,
                'metadata' => ['order_id' => (string) $order->id],
            ]);

            $order->cancelAndRestock();
            $order->user?->notify((new OrderCancelledNotification($order))->locale('ar'));

            return back()->with('success', 'تم استرجاع المبلغ وإلغاء الطلب.');
        } catch (\Throwable $e) {
            Log::error('admin refund failed', [
                'order_id' => $order->id,
                'err'      => $e->getMessage(),
            ]);

            return back()->with('error', 'فشل الاسترجاع: '.$e->getMessage());
        }
    }
}
