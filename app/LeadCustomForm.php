<?php

namespace App;

use App\Scopes\CompanyScope;

class LeadCustomForm extends BaseModel
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
    
}
