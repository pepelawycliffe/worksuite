<?php

namespace App;

use App\Scopes\CompanyScope;
use App\Observers\CurrencyFormatObserver;
use Illuminate\Database\Eloquent\Builder;

class CurrencyFormatSetting extends BaseModel
{
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::observe(CurrencyFormatObserver::class);
       
    }

}
