<?php

namespace App\Observers;

use App\ClientCategory;
use App\Notification;

class ClientCategoryObserver
{

    public function saving(ClientCategory $clientCategory)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (company()) {
                $clientCategory->company_id = company()->id;
            }
        }
    }

}
