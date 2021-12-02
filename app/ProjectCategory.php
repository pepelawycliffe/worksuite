<?php

namespace App;

use App\Observers\ProjectCategoryObserver;
use App\Scopes\CompanyScope;

class ProjectCategory extends BaseModel
{
    protected $table = 'project_category';

    protected static function boot()
    {
        parent::boot();

        static::observe(ProjectCategoryObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

}
