<?php

namespace App\Observers;

use App\PusherSetting;

class PusherSettingObserver
{

    /**
     * Handle the notice "saving" event.
     *
     * @param  \App\PusherSetting  $pusher
     * @return void
     */
    public function saving(PusherSetting $pusher)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $pusher->company_id = company()->id;
        }
    }
}
