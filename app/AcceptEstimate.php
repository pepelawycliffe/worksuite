<?php

namespace App;

use App\Observers\AcceptEstimateObserver;
use App\Scopes\CompanyScope;

class AcceptEstimate extends BaseModel
{

    protected static function boot()
    {
        parent::boot();

        static::observe(AcceptEstimateObserver::class);

        static::addGlobalScope(new CompanyScope);

    }

    public function getSignatureAttribute()
    {
        return asset_url('estimate/accept/'.$this->attributes['signature']);
    }

}
