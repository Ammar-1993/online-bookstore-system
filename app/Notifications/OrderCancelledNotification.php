<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderCancelledNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $number = method_exists($this->order, 'getNumberAttribute')
            ? ($this->order->number ?? ('#' . $this->order->id))
            : ('#' . $this->order->id);

        $isRefunded = ($this->order->payment_status ?? '') === 'refunded';

        return (new MailMessage)
            ->subject(($isRefunded ? 'تم إرجاع المبلغ — ' : 'تم إلغاء الطلب — ') . $number)
            ->greeting('تنبيه')
            ->line($isRefunded
                ? ('تم إلغاء طلبك ' . $number . ' وتمت إعادة المبلغ وإرجاع المخزون.')
                : ('تم إلغاء طلبك ' . $number . '.'))
            ->action('عرض الطلب', route('orders.show', $this->order));
    }
}
