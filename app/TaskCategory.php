<?php

namespace App;

use App\Observers\TaskCategoryObserver;
use App\Scopes\CompanyScope;

class TaskCategory extends BaseModel
{
    protected $table = 'task_category';

    protected static function boot()
    {
        parent::boot();

        static::observe(TaskCategoryObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

}
