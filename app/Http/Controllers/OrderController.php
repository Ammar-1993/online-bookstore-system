<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * صفحة "مشترياتي" مع فلاتر بسيطة.
     */
    public function index(): View
    {
        $user = Auth::user();

        // اجمع فلاتر الواجهة (تتوافق مع القالب resources/views/orders/index.blade.php)
        $filters = [
            'status'         => request('status'),
            'payment_status' => request('payment_status'),
            'from'           => request('from'),
            'to'             => request('to'),
            'q'              => trim((string) request('q', '')),
        ];

        $orders = $user->orders()
            ->with('items') // لاستخراج الإجمالي سريعًا
            ->when($filters['status'], fn($q, $v) => $q->where('status', $v))
            ->when($filters['payment_status'], fn($q, $v) => $q->where('payment_status', $v))
            ->when($filters['from'], fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['to'], fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($filters['q'] !== '', function ($q) use ($filters) {
                $term = $filters['q'];

                // يدعم (ORD-000123) أو (123) أو أجزاء من payment_intent/charge
                $maybeId = null;
                if (preg_match('/(\d+)/', $term, $m)) {
                    $maybeId = (int) $m[1];
                }

                $q->where(function ($qq) use ($term, $maybeId) {
                    if ($maybeId) {
                        $qq->orWhere('id', $maybeId);
                    }
                    $qq->orWhere('payment_intent_id', 'like', "%{$term}%")
                       ->orWhere('charge_id', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', compact('orders', 'filters'));
    }

    /**
     * صفحة تفاصيل الطلب.
     */
    public function show(Order $order): View
    {
        $this->authorize('view', $order);

        // تأكد أن الحالة محدثة (مثلاً بعد Webhook)
        $order->refresh();
        $order->load(['items.book', 'user']);

        return view('orders.show', compact('order'));
    }

    /**
     * Endpoint خفيف لتتبّع حالة الطلب من الواجهة (Polling).
     */
    public function status(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $order->refresh();

        return response()->json([
            'id'             => $order->id,
            'status'         => (string) $order->status,
            'payment_status' => (string) $order->payment_status,
            'paid'           => $order->payment_status === 'paid',
            'updated_at'     => optional($order->updated_at)->toIso8601String(),
        ]);
    }

    /**
     * عرض الفاتورة HTML.
     */
    public function invoice(Order $order): View
    {
        $this->authorize('view', $order);
        $order->load(['items.book', 'user']);

        return view('orders.invoice', compact('order'));
    }

    /**
     * تنزيل الفاتورة PDF عبر mPDF (جاهزة للـ RTL).
     */
    public function invoicePdf(Order $order): Response
    {
        $this->authorize('view', $order);
        $order->load(['items.book', 'user']);

        $html = view('orders.invoice-pdf', compact('order'))->render();

        // تأكد من مجلد tmp الخاص بـ mPDF لتجنّب أخطاء الأذونات
        $tmp = storage_path('app/mpdf-temp');
        File::ensureDirectoryExists($tmp);

        $mpdf = new \Mpdf\Mpdf([
            'mode'           => 'utf-8',
            'format'         => 'A4',
            'orientation'    => 'P',
            'default_font'   => 'dejavusans',
            'margin_top'     => 0,
            'margin_bottom'  => 0,
            'margin_left'    => 0,
            'margin_right'   => 0,
            'tempDir'        => $tmp,
        ]);

        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont   = true;
        $mpdf->SetDirectionality('rtl');

        $mpdf->WriteHTML($html);

        $file = 'invoice-' . (method_exists($order, 'getNumberAttribute') ? $order->number : $order->id) . '.pdf';

        return response(
            $mpdf->Output($file, \Mpdf\Output\Destination::STRING_RETURN),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$file.'"',
            ]
        );
    }
}
