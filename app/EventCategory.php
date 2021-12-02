<?php

namespace App;

use App\Observers\EventCategoryObserver;
use App\Scopes\CompanyScope;

class EventCategory extends BaseModel
{
    protected $table = 'event_categories';
    protected $fillable = ['company_id', 'category_name'];

    protected static function boot()
    {
        parent::boot();
        static::observe(EventCategoryObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

}
