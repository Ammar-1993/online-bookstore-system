<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderShippedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order   = $this->order;
        $subject = 'تم شحن طلبك رقم ' . ($order->number ?? ('#' . $order->id));

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('مرحبًا!')
            ->line('تم شحن طلبك بنجاح.')
            ->line('رقم الطلب: ' . ($order->number ?? ('#' . $order->id)))
            ->line('شركة الشحن: ' . ($order->shipping_carrier ?: 'غير محدد'))
            ->line('رقم التتبع: ' . ($order->tracking_number ?: '—'));

        if ($order->tracking_url) {
            $mail->action('تتبّع الشحنة', $order->tracking_url);
        }

        return $mail->line('شكرًا لتسوقك معنا ♥');
    }
}
