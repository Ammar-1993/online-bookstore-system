<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

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
        $number = method_exists($this->order, 'getNumberAttribute')
            ? ($this->order->number ?? ('#' . $this->order->id))
            : ('#' . $this->order->id);

        return (new MailMessage)
            ->subject('تم استلام دفعتك — ' . $number)
            ->greeting('شكرًا لك!')
            ->line('تم تأكيد الدفع لطلبك ' . $number . '.')
            ->line('حالة الطلب: ' . ($this->order->status ?? 'processing'))
            ->action('عرض الطلب', route('orders.show', $this->order))
            ->line('سنعلمك بأي تحديثات قادمة.');
    }
}
