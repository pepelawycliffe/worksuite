<?php

namespace App;

use App\Scopes\CompanyScope;

class CustomFieldGroup extends BaseModel
{

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new CompanyScope);
    }

}
