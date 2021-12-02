<?php

namespace App;

use App\Observers\LeadCategoryObserver;
use App\Scopes\CompanyScope;

class LeadCategory extends BaseModel
{
    protected $table = 'lead_category';
    protected $default = ['id', 'lead_name'];

    public static function boot()
    {
        parent::boot();
        static::observe(LeadCategoryObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

}
