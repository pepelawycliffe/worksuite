<?php

namespace App;

use App\Observers\EmployeeFaqCategoryObserver;
use App\Scopes\CompanyScope;

class EmployeeFaqCategory extends BaseModel
{

    protected static function boot()
    {
        parent::boot();

        static::observe(EmployeeFaqCategoryObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function faqs()
    {
        return $this->hasMany(EmployeeFaq::class);
    }

}
