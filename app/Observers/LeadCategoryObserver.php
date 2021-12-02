<?php

namespace App\Observers;

use App\LeadCategory;

class LeadCategoryObserver
{

    public function saving(LeadCategory $lead)
    {
        // Cannot put in creating, because saving is fired before creating. And we need company id for check bellow
        if (company()) {
            $lead->company_id = company()->id;
        }
    }

}
