<?php

namespace App\Observers;

use App\ClientContact;
use App\GoogleCalendarModules;

class GoogleCalendarModuleObserver
{

    public function saving(GoogleCalendarModules $calendarModule)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $calendarModule->company_id = company()->id;
        }

        if (user()) {
            $calendarModule->user_id = user()->id;
        }
    }

}
