<?php

namespace App;

use App\Observers\RemovalRequestLeadObserver;
use App\Scopes\CompanyScope;

class RemovalRequestLead extends BaseModel
{

    protected $table = 'removal_requests_lead';

    protected static function boot()
    {
        parent::boot();

        static::observe(RemovalRequestLeadObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

}
