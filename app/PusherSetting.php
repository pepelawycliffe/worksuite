<?php

namespace App;

use App\Observers\PusherSettingObserver;
use App\Scopes\CompanyScope;

class PusherSetting extends BaseModel
{
    protected static function boot()
    {
        parent::boot();

        static::observe(PusherSettingObserver::class);

        static::addGlobalScope(new CompanyScope());
    }
}
