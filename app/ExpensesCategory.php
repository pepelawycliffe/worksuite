<?php

namespace App;

use App\Observers\ExpensesCategoryObserver;
use App\Scopes\CompanyScope;

class ExpensesCategory extends BaseModel
{
    protected $table = 'expenses_category';
    protected $default = ['id','category_name'];

    protected static function boot()
    {
        parent::boot();
        static::observe(ExpensesCategoryObserver::class);
        static::addGlobalScope(new CompanyScope);
    }

    public function expense()
    {
        return $this->hasMany(Expense::class);
    }

    public function roles()
    {
        return $this->hasMany(ExpensesCategoryRole::class, 'expenses_category_id');
    }

}
