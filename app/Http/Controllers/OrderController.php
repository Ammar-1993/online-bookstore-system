<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Barryvdh\DomPDF\Facade\Pdf;



class OrderController extends Controller
{
    public function index(): View
    {



        $orders = auth()->user()
            ->orders()
            ->latest()
            ->with('items')   // حمل العناصر مسبقًا
            ->paginate(10);


        $orders = Auth::user()->orders()->latest()->with('items')->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        // تأكد أن البيانات حديثة (خاصة بعد الويب هوك)
        $order->refresh();
        $order->load(['items.book', 'user']);

        return view('orders.show', compact('order'));
    }

    // ✅ تُستخدم من واجهة الدفع للانتظار حتى يُصبح الطلب "paid"
    public function status(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $order->refresh();

        return response()->json([
            'id'              => $order->id,
            'status'          => (string) $order->status,
            'payment_status'  => (string) $order->payment_status,
            'paid'            => $order->payment_status === 'paid',
            'updated_at'      => optional($order->updated_at)->toIso8601String(),
        ]);
    }

    public function invoice(Order $order): View
    {
        $this->authorize('view', $order);
        $order->load(['items.book', 'user']);
        return view('orders.invoice', compact('order'));
    }



    // public function invoicePdf(Order $order)
    // {
    //     $this->authorize('view', $order);
    //     $order->load(['items.book', 'user']);


    //     $pdf = Pdf::loadView('orders.invoice-pdf', compact('order'))
    //         ->setPaper('a4', 'portrait')
    //         ->setOptions([
    //             'isHtml5ParserEnabled' => true,
    //             'isRemoteEnabled' => true,
    //             'defaultFont' => 'DejaVu Sans',
    //         ]);

    //     $file = 'invoice-' . (method_exists($order, 'getNumberAttribute') ? $order->number : $order->id) . '.pdf';
    //     return $pdf->download($file);

    // }

    public function invoicePdf(Order $order)
{
    $this->authorize('view', $order);
    $order->load(['items.book','user']);

    $html = view('orders.invoice-pdf', compact('order'))->render();

    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'orientation' => 'P',
        'default_font' => 'dejavusans',
        'margin_top' => 0,
        'margin_bottom' => 0,
        'margin_left' => 0,
        'margin_right' => 0,
        'tempDir' => storage_path('app/mpdf-temp'),
    ]);

    $mpdf->autoScriptToLang = true;   // تفعيل كشف اللغة (تشكيل العربية)
    $mpdf->autoLangToFont   = true;   // اختيار خط مناسب تلقائيًا
    $mpdf->SetDirectionality('rtl');  // افتراض اتجاه RTL للصفحة

    $mpdf->WriteHTML($html);

    $file = 'invoice-' . (method_exists($order,'getNumberAttribute') ? $order->number : $order->id) . '.pdf';

    return response($mpdf->Output($file, \Mpdf\Output\Destination::STRING_RETURN), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="'.$file.'"',
    ]);
}

}


