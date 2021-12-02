<?php

namespace App\Listeners;

use App\Events\InvoicePaymentEvent;
use App\Events\ProductPurchaseEvent;
use App\Notifications\ClientPurchaseInvoice;
use App\Notifications\InvoicePaymentReceived;
use Illuminate\Support\Facades\Notification;

class ProductPurchaseListener
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ProductPurchaseEvent $event
     * @return void
     */
    public function handle(ProductPurchaseEvent $event)
    {
        $admins = $event->notifyUser;
        $invoice = $event->invoice;

        if($invoice){
            Notification::send($admins, new ClientPurchaseInvoice($invoice));
        }
    }

}
