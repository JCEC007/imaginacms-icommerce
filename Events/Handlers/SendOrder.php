<?php

namespace Modules\Icommerce\Events\Handlers;

use Illuminate\Contracts\Mail\Mailer;

use Modules\Icommerce\Emails\OrderSuccess;
use Modules\User\Events\OrderWasCreated;

class SendOrder
{
   
    /**
     * @var Mailer
     */
    private $mail;

    public function __construct(Mailer $mail)
    {
        $this->mail = $mail;
    }

    public function handle(OrderWasCreated $event)
    {
        $order = $event->order;
        $this->mail->to($order->email)->send(new OrderSuccess($order));
    }
}
