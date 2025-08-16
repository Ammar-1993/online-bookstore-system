<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function build()
    {
        $subject = 'تأكيد طلبك #' . sprintf('%06d', $this->order->id);

        return $this->subject($subject)
            ->view('emails.orders.placed');
    }
}
