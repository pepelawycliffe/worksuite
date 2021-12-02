<?php

namespace App;

use App\Observers\ExpensesCategoryRoleObserver;
use App\Scopes\CompanyScope;

class ExpensesCategoryRole extends BaseModel
{
    protected $table = 'expenses_category_roles';
    //    protected $default = ['id','category_name'];

    protected static function boot()
    {
        parent::boot();
        static::observe(ExpensesCategoryRoleObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

    public function category()
    {
        return $this->belongsTo(ExpensesCategory::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

}
