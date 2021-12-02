<?php

namespace App;

use App\Observers\TicketReplyTemplateObserver;
use App\Scopes\CompanyScope;

class TicketReplyTemplate extends BaseModel
{

    protected static function boot()
    {
        parent::boot();

        static::observe(TicketReplyTemplateObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

}
