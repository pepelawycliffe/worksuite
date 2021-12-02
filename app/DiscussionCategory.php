<?php

namespace App;

use App\Observers\DiscussionCategoryObserver;
use App\Scopes\CompanyScope;

class DiscussionCategory extends BaseModel
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();
        static::observe(DiscussionCategoryObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

}
