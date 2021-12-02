<?php

namespace App;

use App\Observers\ThemeSettingObserver;
use App\Scopes\CompanyScope;

class ThemeSetting extends BaseModel
{

    protected static function boot()
    {
        parent::boot();

        static::observe(ThemeSettingObserver::class);

        static::addGlobalScope(new CompanyScope);
    }

}
