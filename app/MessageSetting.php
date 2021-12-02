<?php

namespace App;

use App\Observers\MessageSettingObserver;
use App\Scopes\CompanyScope;

class MessageSetting extends BaseModel
{

    protected static function boot()
    {
        parent::boot();

        static::observe(MessageSettingObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

}
