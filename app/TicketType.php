<?php

namespace App;

use App\Observers\TicketTypeObserver;
use App\Scopes\CompanyScope;

class TicketType extends BaseModel
{

    protected static function boot()
    {
        parent::boot();

        static::observe(TicketTypeObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

}
