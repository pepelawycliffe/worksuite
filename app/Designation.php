<?php

namespace App;

use App\Observers\DesignationObserver;
use App\Scopes\CompanyScope;

class Designation extends BaseModel
{
    protected $fillable = ['name', 'company_id'];

    protected static function boot()
    {
        parent::boot();

        static::observe(DesignationObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function members()
    {
        return $this->hasMany(EmployeeDetails::class, 'designation_id');
    }

}
