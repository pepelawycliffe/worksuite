<?php

namespace App;

use App\Observers\ContractTypeObserver;
use App\Scopes\CompanyScope;

class ContractType extends BaseModel
{

    protected static function boot()
    {
        parent::boot();

        static::observe(ContractTypeObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

}
