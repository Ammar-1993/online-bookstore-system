<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\SignatureVerificationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use Stripe\StripeClient;


use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;

class StripeController extends Controller
{
    public function pay(Order $order): View|RedirectResponse
    {
        $this->authorize('view', $order);

        if (method_exists($order, 'isPayable') && !$order->isPayable()) {
            return redirect()->route('orders.show', $order)->with('warning', 'لا يمكن دفع هذا الطلب.');
        }

        $order->loadMissing(['items.book', 'user']);

        return view('payments.stripe', [
            'order' => $order,
            'publishableKey' => env('STRIPE_KEY'),
        ]);
    }




public function createIntent(Request $request, Order $order): JsonResponse
{
    $this->authorize('update', $order);

    if (method_exists($order, 'isPayable') && ! $order->isPayable()) {
        return response()->json(['message' => 'Order not payable'], 422);
    }

    // حساب المبلغ والعملة بشكل صريح وآمن
    $order->loadMissing(['items']);
    $amountFloat = method_exists($order, 'computedTotal')
        ? (float) $order->computedTotal()
        : (float) $order->items->sum('total_price');
    $amountCents = (int) round($amountFloat * 100);

    $currency = strtolower(trim((string) ($order->currency ?: config('app.currency', 'USD'))));

    try {
        $client = new StripeClient((string) env('STRIPE_SECRET'));

        $intent = $client->paymentIntents->create(
            [
                'amount'   => $amountCents,
                'currency' => $currency,
                'metadata' => [
                    'order_id' => (string) $order->id,
                    'user_id'  => (string) $order->user_id,
                ],
                'capture_method' => 'automatic',
            ],
            [
                'idempotency_key' => 'order_'.$order->id.'_'.($order->updated_at?->timestamp ?? time()),
            ]
        );

        return response()->json(['clientSecret' => $intent->client_secret]);

    } catch (ApiErrorException $e) {
        Log::error('Stripe PI error', [
            'order_id' => $order->id,
            'code'     => $e->getStripeCode(),
            'type'     => $e->getError()->type ?? null,
            'message'  => $e->getMessage(),
        ]);
        // نُعيد رسالة واضحة للواجهة أثناء التطوير
        return response()->json(['message' => 'Stripe: '.$e->getMessage()], 422);

    } catch (\Throwable $e) {
        Log::error('PI create fatal', ['order_id' => $order->id, 'err' => $e->getMessage()]);
        return response()->json(['message' => 'Server error while creating intent.'], 500);
    }
}



    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $secret
            );
        } catch (SignatureVerificationException $e) {
            return new Response('Invalid signature', 400);
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $pi = $event->data->object;
            $orderId = (int) ($pi->metadata->order_id ?? 0);
            if ($orderId > 0) {
                $order = Order::find($orderId);
                if ($order && $order->payment_status !== 'paid') {
                    try {
                        $order->markPaid();
                    } catch (\Throwable $e) {
                        // يمكن تسجيل الخطأ إن رغبت
                    }
                }
            }
        }

        if ($event->type === 'charge.refunded') {
            $charge = $event->data->object;
            $orderId = (int) (($charge->metadata->order_id ?? 0) ?: 0);
            if ($orderId > 0) {
                $order = Order::find($orderId);
                if ($order && $order->payment_status === 'paid') {
                    $order->cancelAndRestock();
                }
            }
        }

        return new Response('ok', 200);
    }

    private function calculateAmountCents(Order $order): int
    {
        $order->loadMissing(['items']);
        $amount = method_exists($order, 'computedTotal')
            ? (float) $order->computedTotal()
            : (float) $order->items->sum('total_price');

        return (int) round($amount * 100);
    }

    private function currencyCode(Order $order): string
    {
        $currency = $order->currency ?: config('app.currency', 'USD');
        return strtolower((string) $currency);
    }

    private function stripeClient(): StripeClient
    {
        return new StripeClient((string) env('STRIPE_SECRET'));
    }

    private function idempotencyKey(Order $order): string
    {
        $stamp = $order->updated_at?->timestamp ?? time();
        return 'order_' . $order->id . '_' . $stamp;
    }

}
