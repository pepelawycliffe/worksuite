<?php

namespace App;

use App\Observers\UniversalSearchObserver;
use App\Scopes\CompanyScope;

class UniversalSearch extends BaseModel
{
    protected $table = 'universal_search';

    protected static function boot()
    {
        parent::boot();

        static::observe(UniversalSearchObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

}
