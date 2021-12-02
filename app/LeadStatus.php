<?php

namespace App;

use App\Observers\LeadStatusObserver;
use App\Scopes\CompanyScope;

class LeadStatus extends BaseModel
{
    protected $table = 'lead_status';

    protected static function boot()
    {
        parent::boot();

        static::observe(LeadStatusObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'status_id')->orderBy('column_priority');
    }

}
