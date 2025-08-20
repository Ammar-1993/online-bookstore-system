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

// إشعارات البريد
use App\Notifications\OrderPaidNotification;
use App\Notifications\OrderCancelledNotification;

class StripeController extends Controller
{
    /**
     * صفحة الدفع بالبطاقة.
     */
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

    /**
     * إنشاء PaymentIntent وإرجاع client_secret.
     */
    public function createIntent(Request $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);

        if (method_exists($order, 'isPayable') && ! $order->isPayable()) {
            return response()->json(['message' => 'Order not payable'], 422);
        }

        $amountCents = $this->calculateAmountCents($order);
        $currency    = $this->currencyCode($order);

        try {
            $client  = $this->stripeClient();
            $payload = $this->buildPaymentIntentPayload($order, $amountCents, $currency);
            $key     = $this->idempotencyKey($order);

            $intent = $this->createStripePaymentIntent($client, $payload, $key);

            return response()->json(['clientSecret' => $intent->client_secret]);

        } catch (ApiErrorException $e) {
            Log::error('Stripe PI error', [
                'order_id' => $order->id,
                'code'     => $e->getStripeCode(),
                'type'     => $e->getError()->type ?? null,
                'message'  => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Stripe: '.$e->getMessage()], 422);

        } catch (\Throwable $e) {
            Log::error('PI create fatal', ['order_id' => $order->id, 'err' => $e->getMessage()]);
            return response()->json(['message' => 'Server error while creating intent.'], 500);
        }
    }

    /**
     * Webhook من Stripe — يُعالج الدفع/الاسترجاع بإديمبوتنسي.
     */
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

        // لا تُعالج نفس الحدث مرتين
        if (! Cache::add('stripe_evt_'.$event->id, true, now()->addDay())) {
            return new Response('duplicate', 200);
        }

        try {
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    /** @var \Stripe\PaymentIntent $pi */
                    $pi = $event->data->object;
                    $this->handlePaymentIntentSucceeded($pi);
                    break;

                case 'charge.succeeded':
                    /** @var \Stripe\Charge $ch */
                    $ch = $event->data->object;
                    $this->handleChargeSucceeded($ch);
                    break;

                case 'charge.refunded':
                    /** @var \Stripe\Charge $ch */
                    $ch = $event->data->object;
                    $this->handleChargeRefunded($ch);
                    break;

                case 'payment_intent.payment_failed':
                    /** @var \Stripe\PaymentIntent $pi */
                    $pi = $event->data->object;
                    Log::warning('PI failed', [
                        'pi'       => $pi->id ?? null,
                        'order_id' => $pi->metadata->order_id ?? null,
                        'last_err' => $pi->last_payment_error?->message ?? null,
                    ]);
                    break;

                default:
                    // أحداث أخرى غير مهمة حالياً
                    break;
            }
        } catch (\Throwable $e) {
            Log::error('webhook unhandled exception', ['event' => $event->type, 'err' => $e->getMessage()]);
        }

        return new Response('ok', 200);
    }

    /* ===================== Helpers ===================== */

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
        return 'order_'.$order->id.'_'.$stamp;
    }

    /**
     * Payload قياسي لـ PaymentIntent.
     *
     * @return array<string,mixed>
     */
    private function buildPaymentIntentPayload(Order $order, int $amountCents, string $currency): array
    {
        $payload = [
            'amount'                    => $amountCents,
            'currency'                  => $currency,
            'automatic_payment_methods' => ['enabled' => true],
            'metadata'                  => [
                'order_id' => (string) $order->id,
                'user_id'  => (string) $order->user_id,
            ],
            'description'               => 'Order #'.$order->id,
        ];

        $email = $order->user?->email;
        if (is_string($email) && $email !== '') {
            $payload['receipt_email'] = $email;
        }

        return $payload;
    }

    /**
     * إنشاء PaymentIntent مع idempotency.
     * @throws \Stripe\Exception\ApiErrorException
     */
    private function createStripePaymentIntent(StripeClient $client, array $payload, string $idempotencyKey): \Stripe\PaymentIntent
    {
        return $client->paymentIntents->create(
            $payload,
            ['idempotency_key' => $idempotencyKey]
        );
    }

    /**
     * عند نجاح PaymentIntent.
     * - يحدّث الطلب ويُرسل بريد "تم الدفع".
     *
     * @param \Stripe\PaymentIntent $pi
     */
    private function handlePaymentIntentSucceeded($pi): void
    {
        $orderId = (int) ($pi->metadata->order_id ?? 0);
        if ($orderId <= 0) {
            Log::warning('PI succeeded without order_id', ['pi' => $pi->id]);
            return;
        }

        $order = Order::find($orderId);
        if (! $order) {
            Log::warning('Order not found for PI', ['pi' => $pi->id, 'order_id' => $orderId]);
            return;
        }

        if ($order->payment_status === 'paid') {
            // مُعالَج سابقاً
            return;
        }

        $chargeId = $pi->latest_charge ?: ($pi->charges->data[0]->id ?? null);

        $order->markPaid();
        $order->forceFill([
            'payment_intent_id' => $pi->id,
            'charge_id'         => $chargeId,
            'paid_at'           => now(),
        ])->save();

        // بريد "تم الدفع"
        $order->user?->notify((new OrderPaidNotification($order))->locale('ar'));
    }

    /**
     * عند نجاح Charge (في حال سبق الـ PI أو الميتاداتا على الشارج).
     *
     * @param \Stripe\Charge $ch
     */
    private function handleChargeSucceeded($ch): void
    {
        // حاول إيجاد الطلب من metadata مباشرة
        $orderId = (int) ($ch->metadata->order_id ?? 0);
        $order   = $orderId ? Order::find($orderId) : null;

        // إن لم يوجد، اجلب PI واقرأ metadata
        if (! $order && isset($ch->payment_intent) && is_string($ch->payment_intent)) {
            try {
                $pi = $this->stripeClient()->paymentIntents->retrieve($ch->payment_intent);
                $orderId = (int) ($pi->metadata->order_id ?? 0);
                if ($orderId > 0) {
                    $order = Order::find($orderId);
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to retrieve PI for charge.succeeded', [
                    'charge' => $ch->id, 'err' => $e->getMessage(),
                ]);
            }
        }

        if (! $order) {
            Log::warning('charge.succeeded: order not resolved', ['charge' => $ch->id]);
            return;
        }

        if ($order->payment_status !== 'paid') {
            // مرّر عبر مسار التحديث نفسه
            $pi = (object) [
                'id'            => $ch->payment_intent ?? null,
                'metadata'      => (object) ['order_id' => (string) $order->id],
                'latest_charge' => $ch->id,
                'charges'       => (object) ['data' => [ (object) ['id' => $ch->id] ]],
            ];
            $this->handlePaymentIntentSucceeded($pi);
        }
    }

    /**
     * عند استرجاع المبلغ.
     *
     * @param \Stripe\Charge $ch
     */
    private function handleChargeRefunded($ch): void
    {
        // أولاً من metadata
        $order = null;
        $orderIdFromMeta = (int) ($ch->metadata->order_id ?? 0);
        if ($orderIdFromMeta > 0) {
            $order = Order::find($orderIdFromMeta);
        }

        // أو من charge_id المخزّن
        if (! $order) {
            $order = Order::where('charge_id', $ch->id)->first();
        }

        if (! $order) {
            Log::warning('charge.refunded: order not resolved', ['charge' => $ch->id]);
            return;
        }

        if ($order->payment_status === 'paid') {
            $order->cancelAndRestock();
            // بريد "تم الإلغاء/الاسترجاع"
            $order->user?->notify((new OrderCancelledNotification($order))->locale('ar'));
        }
    }
}
