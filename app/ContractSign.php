<?php

namespace App;

use App\Observers\ContractSignObserver;
use App\Scopes\CompanyScope;

class ContractSign extends BaseModel
{

    protected static function boot()
    {
        parent::boot();

        static::observe(ContractSignObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

    public function getSignatureAttribute()
    {
        return asset_url('contract/sign/'.$this->attributes['signature']);
    }

}
