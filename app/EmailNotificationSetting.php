<?php

namespace App;

use App\Observers\EmailNotificationSettingObserver;
use App\Scopes\CompanyScope;

class EmailNotificationSetting extends BaseModel
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::observe(EmailNotificationSettingObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

}
