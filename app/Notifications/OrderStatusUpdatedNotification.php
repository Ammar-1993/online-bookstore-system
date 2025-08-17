<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $oldStatus, public string $newStatus) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $order = $this->order->fresh(['items.book','user']);

        $number = method_exists($order, 'getNumberAttribute')
            ? ($order->number ?? ('#'.$order->id))
            : ('#'.$order->id);

        $title = 'تحديث حالة الطلب — ' . $number;

        $line = match ($this->newStatus) {
            'processing' => 'طلبك الآن قيد المعالجة.',
            'shipped'    => 'تم شحن طلبك. نتمنى لك قراءة ممتعة!',
            default      => 'تغيّرت حالة طلبك.',
        };

        return (new MailMessage)
            ->subject($title)
            ->greeting('تحديث حالة الطلب')
            ->line('رقم الطلب: ' . $number)
            ->line($line)
            ->action('عرض الطلب', route('orders.show', $order));
    }
}
