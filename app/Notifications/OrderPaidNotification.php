<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Illuminate\Support\Facades\Log;

class OrderPaidNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $order  = $this->order->fresh(['items.book','user']);
        $number = method_exists($order, 'getNumberAttribute')
            ? ($order->number ?? ('#'.$order->id))
            : ('#'.$order->id);

        $mail = (new MailMessage)
            ->subject('تم استلام دفعتك — ' . $number)
            ->greeting('شكرًا لك!')
            ->line('تم تأكيد الدفع لطلبك ' . $number . '.')
            ->line('حالة الطلب: ' . ($order->status ?? 'processing'))
            ->action('عرض الطلب', route('orders.show', $order));

        try {
            if (!is_dir(storage_path('app/mpdf-temp'))) {
                @mkdir(storage_path('app/mpdf-temp'), 0775, true);
            }

            $html = view('orders.invoice-pdf', compact('order'))->render();

            $mpdf = new Mpdf([
                'mode'           => 'utf-8',
                'format'         => 'A4',
                'orientation'    => 'P',
                'default_font'   => 'dejavusans',
                'margin_top'     => 0,
                'margin_bottom'  => 0,
                'margin_left'    => 0,
                'margin_right'   => 0,
                'tempDir'        => storage_path('app/mpdf-temp'),
            ]);

            $mpdf->autoScriptToLang = true;
            $mpdf->autoLangToFont   = true;
            $mpdf->SetDirectionality('rtl');
            $mpdf->WriteHTML($html);

            $fileName   = 'invoice-' . (method_exists($order,'getNumberAttribute') ? $order->number : $order->id) . '.pdf';
            $pdfContent = $mpdf->Output($fileName, Destination::STRING_RETURN);

            $mail->attachData($pdfContent, $fileName, ['mime' => 'application/pdf']);
        } catch (\Throwable $e) {
            Log::warning('OrderPaidNotification PDF attach failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
        }

        return $mail;
    }
}
