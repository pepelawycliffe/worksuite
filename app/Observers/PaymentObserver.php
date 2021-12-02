<?php

namespace App\Observers;

use App\Estimate;
use App\Notification;
use App\Notifications\NewInvoice;
use App\Notifications\NewPayment;
use App\Payment;
use App\Scopes\CompanyScope;
use App\User;

class PaymentObserver
{

    public function saving(Payment $payment)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $payment->company_id = company()->id;
        }
    }

    public function created(Payment $payment)
    {
        if (!isSeedingData()) {
            if( $payment->project != null){
                if (($payment->project_id && $payment->project->client_id != null) || ($payment->invoice_id && $payment->invoice->client_id != null)) {
                    $clientId = ($payment->project_id && $payment->project->client_id != null) ? $payment->project->client_id : $payment->invoice->client_id;
                    // Notify client
                    $notifyUser = User::withoutGlobalScopes(['active', CompanyScope::class])->findOrFail($clientId);
                    $notifyUser->notify(new NewPayment($payment));
                }
            }else{
                    if($payment->invoice != null || $payment->invoice_id != null ){
                         $clientId = $payment->invoice->client_id;
                    // Notify client
                    $notifyUser = User::withoutGlobalScopes(['active', CompanyScope::class])->findOrFail($clientId);
                    $notifyUser->notify(new NewPayment($payment));
                }

            }
        }
    }

    public function deleting(Payment $payment)
    {
        $notifiData = ['App\Notifications\NewPayment','App\Notifications\PaymentReminder'
        ];
        $notifications = Notification::
          whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('company_id',company()->id)
            ->where('data', 'like', '{"id":'.$payment->id.',%')
            ->delete();
    }

}
