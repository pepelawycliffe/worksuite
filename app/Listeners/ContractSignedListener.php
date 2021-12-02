<?php

namespace App\Listeners;

use App\Events\ContractSignedEvent;
use App\Events\InvoicePaymentEvent;
use App\Notifications\ContractSigned;
use App\Notifications\InvoicePaymentReceived;
use Illuminate\Support\Facades\Notification;

class ContractSignedListener
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
     * @param ContractSignedListener $event
     * @return void
     */
    public function handle(ContractSignedEvent $event)
    {
        $admins = $event->notifyUser;
        $contract = $event->contract;

        if($contract){
            Notification::send($admins, new ContractSigned($contract, $event->contractSign));
        }
    }

}
