<?php

namespace App\Observers;

use App\ClientDetails;
use App\Notification;
use App\UniversalSearch;

class ClientDetailObserver
{

    /**
     * Handle the leave "saving" event.
     *
     * @param  \App\ClientDetails  $detail
     * @return void
     */
    public function saving(ClientDetails $detail)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $detail->company_id = company()->id;
        }
    }

    public function deleting(ClientDetails $detail)
    {
        $universalSearches = UniversalSearch::where('searchable_id', $detail->user_id)->where('module_type', 'client')->get();
        if ($universalSearches) {
            foreach ($universalSearches as $universalSearch) {
                UniversalSearch::destroy($universalSearch->id);
            }
        }
        $notifiData = ['App\Notifications\ClientPurchaseInvoice','App\Notifications\NewClientProposal','App\Notifications\NewClientTask','App\Notifications\NewContract'];

        $notifications = Notification::
            whereIn('type', $notifiData)
            ->whereNull('read_at')
            ->where('company_id',company()->id)
            ->where(function ($q) use ($detail) {
                $q->where('data', 'like', '{"id":'.$detail->id.',%');
                $q->orWhere('data', 'like', '%,"client_id":'.$detail->id.',%');
            })->delete();
            
    }

}
