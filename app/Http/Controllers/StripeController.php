<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Response;

class StripeController extends Controller
{
    public function pay(Order $order): View|RedirectResponse
    {
        $this->authorize('view', $order);

        if (method_exists($order, 'isPayable') && ! $order->isPayable()) {
            return redirect()->route('orders.show', $order)->with('warning', 'لا يمكن دفع هذا الطلب.');
        }

        $order->loadMissing(['items.book', 'user']);

        return view('payments.stripe', [
            'order'          => $order,
            'publishableKey' => env('STRIPE_KEY'),
        ]);
    }

    public function createIntent(Request $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        if (method_exists($order, 'isPayable') && ! $order->isPayable()) {
            return response()->json(['message' => 'Order not payable'], 422);
        }

        $amountCents = $this->calculateAmountCents($order);
        $currency    = $this->currencyCode($order);

        try {
            $client = $this->stripeClient();

            $intent = $client->paymentIntents->create(
                [
                    'amount'         => $amountCents,
                    'currency'       => $currency,
                    'metadata'       => [
                        'order_id' => (string) $order->id,
                        'user_id'  => (string) $order->user_id,
                    ],
                    'capture_method' => 'automatic',
                ],
                [
                    'idempotency_key' => $this->idempotencyKey($order),
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

            return response()->json(['message' => 'Stripe: ' . $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('PI create fatal', ['order_id' => $order->id, 'err' => $e->getMessage()]);
            return response()->json(['message' => 'Server error while creating intent.'], 500);
        }
    }

    public function webhook(Request $request): Response
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            return new Response('Invalid signature', 400);
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
        }

        // Idempotency للحماية من تكرار معالجة نفس الحدث
        $cacheKey = 'stripe_evt_' . $event->id;
        if (! Cache::add($cacheKey, true, now()->addDay())) {
            return new Response('duplicate', 200);
        }

        if ($event->type === 'payment_intent.succeeded') {
            /** @var \Stripe\PaymentIntent $pi */
            $pi = $event->data->object;

            $orderId = (int) ($pi->metadata->order_id ?? 0);
            if ($orderId > 0) {
                $order = Order::find($orderId);

                if ($order && $order->payment_status !== 'paid') {
                    try {
                        $order->markPaid();

                        $chargeId = $pi->latest_charge
                            ?: ($pi->charges->data[0]->id ?? null);

                        $order->forceFill([
                            'payment_intent_id' => $pi->id,
                            'charge_id'         => $chargeId,
                            'paid_at'           => now(),
                        ])->save();
                    } catch (\Throwable $e) {
                        Log::error('webhook markPaid failed', [
                            'order_id' => $order?->id,
                            'err'      => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        if ($event->type === 'charge.refunded') {
            /** @var \Stripe\Charge $ch */
            $ch = $event->data->object;

            $order = null;
            $orderIdFromMeta = (int) ($ch->metadata->order_id ?? 0);
            if ($orderIdFromMeta > 0) {
                $order = Order::find($orderIdFromMeta);
            }
            if (! $order) {
                $order = Order::where('charge_id', $ch->id)->first();
            }

            if ($order && $order->payment_status === 'paid') {
                try {
                    $order->cancelAndRestock();
                } catch (\Throwable $e) {
                    Log::error('webhook refund handling failed', [
                        'order_id' => $order->id,
                        'err'      => $e->getMessage(),
                    ]);
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
